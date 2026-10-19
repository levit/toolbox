<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

use BackedEnum;
use Throwable;

/**
 * Classe de Suporte para preenchimento de campos e inferência de metadados de Formulários no sistema.
 *
 * Facilita a recuperação de valores com precedência inteligente:
 * old() da sessão > atributo do model (com suporte a enums) > valor padrão.
 *
 * Provê também resolução em cascata (Fallback Cascade) para obrigatoriedade de campos (required):
 * 1. Override explícito na View Blade (:required="true|false")
 * 2. FormRequest associado ao FormBuilder ou Rota ativa (regras de validação)
 * 3. Schema DDL do Banco de Dados via Model (NOT NULL, sem DEFAULT, presente no $fillable, ignorando colunas de sistema)
 * 4. Fallback padrão: false
 */
class Form
{
    /**
     * Instância ativa do FormBuilder no ciclo de renderização da view atual.
     */
    private static ?object $activeForm = null;

    /**
     * Instância ativa do Model Eloquent no ciclo de renderização da view atual.
     */
    private static ?object $activeModel = null;

    /**
     * Colunas técnicas de infraestrutura que nunca devem ser inferidas como campos required no frontend.
     *
     * @var list<string>
     */
    private const array SYSTEM_COLUMNS = [
        'id',
        'uuid',
        'instituicao_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Cache estático em memória durante a requisição (evita múltiplos acessos a cache/banco).
     *
     * @var array<string, array<string, bool>>
     */
    private static array $schemaCache = [];

    /**
     * Define o formulário ativo na renderização.
     */
    public static function setActiveForm(?object $form): void
    {
        self::$activeForm = $form;
    }

    /**
     * Retorna o formulário ativo.
     */
    public static function getActiveForm(): ?object
    {
        return self::$activeForm;
    }

    /**
     * Define o model ativo na renderização.
     */
    public static function setActiveModel(?object $model): void
    {
        self::$activeModel = $model;
    }

    /**
     * Retorna o model ativo.
     */
    public static function getActiveModel(): ?object
    {
        return self::$activeModel;
    }

    /**
     * Limpa o estado ativo (útil entre requisições ou testes).
     */
    public static function clear(): void
    {
        self::$activeForm = null;
        self::$activeModel = null;
    }

    /**
     * Limpa o cache de schema estático e persistente.
     */
    public static function clearSchemaCache(?string $table = null): void
    {
        if ($table !== null) {
            unset(self::$schemaCache[$table]);
            $cacheClass = 'Illuminate\\Support\\Facades\\Cache';
            if (class_exists($cacheClass)) {
                try {
                    $cacheClass::forget("schema_required_cols_{$table}");
                } catch (Throwable) {
                    // Silencioso em caso de falha de conexão com cache
                }
            }

            return;
        }

        self::$schemaCache = [];
    }

    /**
     * Recupera o valor atual para um campo de formulário considerando old input, model e enums.
     *
     * @param  string  $name  Nome do campo de formulário (ex: 'nome', 'endereco.cidade').
     * @param  object|null  $model  Instância do Model associada à tela ou null para buscar do contexto ativo.
     * @param  mixed  $default  Valor padrão caso não haja input anterior ou dado no model.
     * @return mixed Valor resolvido para exibição no campo do formulário.
     */
    public static function value(string $name, ?object $model = null, mixed $default = null): mixed
    {
        $targetModel = $model ?? self::getActiveModel();
        if ($targetModel === null && self::getActiveForm() && method_exists(self::getActiveForm(), 'getModelClass')) {
            $resolved = self::getActiveForm()->getModelClass();
            if (is_object($resolved)) {
                $targetModel = $resolved;
            }
        }

        if ($targetModel === null) {
            return \function_exists('old') ? \call_user_func('old', $name, $default) : $default;
        }

        $modelValue = null;
        if (\function_exists('data_get')) {
            $modelValue = \call_user_func('data_get', $targetModel, $name, $default);
        } else {
            $modelValue = $targetModel->{$name} ?? $default;
        }

        if ($modelValue instanceof BackedEnum) {
            $modelValue = $modelValue->value;
        }

        return \function_exists('old') ? \call_user_func('old', $name, $modelValue) : $modelValue;
    }

    /**
     * Resolve se um campo de formulário é obrigatório utilizando resolução em cascata.
     *
     * @param  string  $name  Nome do campo (ex: 'nome', 'titulo', 'dados[cpf]').
     * @param  object|null  $model  Model associado ou null para inferir do contexto ativo.
     * @param  object|null  $form  FormBuilder associado ou null para inferir do contexto ativo.
     * @param  bool|null  $explicit  Valor explícito passado pela view Blade (:required="...").
     * @return bool True se o campo for obrigatório, False caso contrário.
     */
    public static function isRequired(
        string $name,
        ?object $model = null,
        ?object $form = null,
        ?bool $explicit = null
    ): bool {
        // Nível 1: Override explícito na View Blade (prioridade máxima)
        if ($explicit !== null) {
            return $explicit;
        }

        $activeForm = $form ?? self::getActiveForm();
        $targetModel = self::resolveTargetModel($model, $activeForm);

        // Nível 2: Inspecionar regras do FormRequest
        $fromRequest = self::resolveFromForm($name, $activeForm);
        if ($fromRequest !== null) {
            return $fromRequest;
        }

        // Nível 3 e 4: Inspecionar DDL do Banco de Dados / Fallback
        return ($targetModel !== null) ? (self::checkModelSchema($name, $targetModel) ?? false) : false;
    }

    private static function resolveTargetModel(?object $model, ?object $form): ?object
    {
        $target = $model ?? self::getActiveModel();
        if ($target === null && $form !== null && method_exists($form, 'getModelClass')) {
            $resolved = $form->getModelClass();
            $target = is_object($resolved) ? $resolved : null;
        }

        return $target;
    }

    private static function resolveFromForm(string $name, ?object $form): ?bool
    {
        if ($form !== null && method_exists($form, 'getRequestClass')) {
            $requestClass = $form->getRequestClass();
            if (is_string($requestClass) && $requestClass !== '') {
                return self::checkRequestRules($name, $requestClass);
            }
        }

        return null;
    }

    /**
     * Inspeciona se as regras de validação de um FormRequest determinam o campo como obrigatório.
     */
    private static function checkRequestRules(string $name, string $requestClass): ?bool
    {
        if (! class_exists($requestClass)) {
            return null;
        }

        try {
            $instance = new $requestClass();
            $rules = method_exists($instance, 'rules') ? $instance->rules() : [];
            $fieldRules = self::extractFieldRules($name, $rules);

            return $fieldRules !== null ? self::evaluateRules($fieldRules) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function extractFieldRules(string $name, array $rules): mixed
    {
        $normalizedName = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
        $fieldRules = $rules[$name] ?? $rules[$normalizedName] ?? null;

        if ($fieldRules === null) {
            $baseField = explode('.', $normalizedName)[0];
            $fieldRules = $rules[$baseField] ?? null;
        }

        return $fieldRules;
    }

    private static function evaluateRules(mixed $fieldRules): ?bool
    {
        $rulesList = is_array($fieldRules) ? $fieldRules : explode('|', (string) $fieldRules);
        $hasRequired = false;
        $hasNullable = false;

        foreach ($rulesList as $rule) {
            if (is_string($rule)) {
                [$req, $null] = self::parseStringRule($rule);
                $hasRequired = $hasRequired || $req;
                $hasNullable = $hasNullable || $null;
            } elseif (is_object($rule) && str_contains(strtolower(get_class($rule)), 'required')) {
                $hasRequired = true;
            }
        }

        if ($hasRequired && ! $hasNullable) {
            return true;
        }

        return $hasNullable ? false : null;
    }

    /**
     * @return array{0: bool, 1: bool}
     */
    private static function parseStringRule(string $rule): array
    {
        $hasReq = false;
        $hasNull = false;
        foreach (explode('|', $rule) as $subRule) {
            $trimmed = strtolower(trim($subRule));
            if ($trimmed === 'required') {
                $hasReq = true;
            } elseif ($trimmed === 'nullable') {
                $hasNull = true;
            }
        }

        return [$hasReq, $hasNull];
    }

    /**
     * Inspeciona o schema da tabela do Model no Banco de Dados.
     */
    private static function checkModelSchema(string $name, object $model): ?bool
    {
        try {
            $columnName = self::extractColumnName($name);
            if (self::isSystemOrNotFillable($columnName, $model)) {
                return false;
            }

            $table = method_exists($model, 'getTable') ? (string) $model->getTable() : '';

            return self::querySchemaRequired($table, $columnName);
        } catch (Throwable) {
            return null;
        }
    }

    private static function extractColumnName(string $name): string
    {
        $normalizedName = trim(str_replace(['[', ']'], ['.', ''], $name), '.');

        return str_contains($normalizedName, '.')
            ? substr($normalizedName, strrpos($normalizedName, '.') + 1)
            : $normalizedName;
    }

    private static function isSystemOrNotFillable(string $columnName, object $model): bool
    {
        if (in_array($columnName, self::SYSTEM_COLUMNS, true)) {
            return true;
        }

        if (method_exists($model, 'getFillable')) {
            $fillable = $model->getFillable();
            if (! empty($fillable) && ! in_array($columnName, $fillable, true)) {
                return true;
            }
        }

        return false;
    }

    private static function querySchemaRequired(string $table, string $columnName): ?bool
    {
        $schemaClass = 'Illuminate\\Support\\Facades\\Schema';
        if ($table === '' || ! class_exists($schemaClass)) {
            return null;
        }

        self::ensureSchemaCached($table, $schemaClass);

        return self::$schemaCache[$table][$columnName] ?? false;
    }

    private static function ensureSchemaCached(string $table, string $schemaClass): void
    {
        if (isset(self::$schemaCache[$table])) {
            return;
        }

        $loadSchema = function () use ($table, $schemaClass): array {
            $columns = $schemaClass::getColumns($table);
            $requiredMap = [];
            foreach ($columns as $col) {
                $colName = $col['name'] ?? null;
                if (! $colName) {
                    continue;
                }
                $isNullable = (bool) ($col['nullable'] ?? true);
                $hasDefault = ($col['default'] !== null);
                $extra = (string) ($col['extra'] ?? '');
                $isAuto = str_contains($extra, 'auto_increment');
                $isGenerated = ! empty($col['generation_expression'] ?? null);

                $requiredMap[$colName] = (! $isNullable && ! $hasDefault && ! $isAuto && ! $isGenerated);
            }

            return $requiredMap;
        };

        $cacheClass = 'Illuminate\\Support\\Facades\\Cache';
        if (class_exists($cacheClass)) {
            self::$schemaCache[$table] = $cacheClass::remember(
                "schema_required_cols_{$table}",
                86400,
                $loadSchema
            );
        } else {
            self::$schemaCache[$table] = $loadSchema();
        }
    }
}
