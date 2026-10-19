<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para Menus, Acessibilidade e Navegação do Sistema.
 *
 * Fornece métodos inteligentes para detecção contextual de rotas ativas,
 * aplicação de classes CSS e geração de atributos de acessibilidade (ARIA).
 */
class Menu
{
    /**
     * Determina se uma rota ou padrão de rota corresponde à rota ativa atual.
     *
     * Suporta:
     * - Correspondência direta ou com curinga: 'cargos.list', 'cargos.*'
     * - Reconhecimento contextual de CRUD ('*.list'): se a rota do menu for 'cargos.list',
     *   reconhece automaticamente todas as telas do CRUD ('cargos.create', 'cargos.edit',
     *   'cargos.show', 'cargos.delete', 'cargos.importar', etc.).
     * - Módulos operacionais ('*.index'): reconhece telas filhas (ex: 'apuracoes.espelho'),
     *   mantendo o isolamento de menus irmãos como no 'dashboard'.
     *
     * @param  string  $routePattern  Rota ou padrão do menu (ex: 'cargos.list').
     * @param  string|null  $currentRoute  Rota atual a ser comparada (se nula, obtém de request()->route()->getName()).
     * @return bool Retorna true se a rota atual corresponder ao menu.
     */
    public static function isRouteActive(string $routePattern, ?string $currentRoute = null): bool
    {
        if ($currentRoute === null && function_exists('request') && request()?->route()) {
            $currentRoute = request()->route()?->getName();
        }

        $currentRoute = (string) $currentRoute;

        if ($currentRoute === '') {
            return false;
        }

        // 1. Correspondência exata ou via curinga explícito (ex: 'cargos.*' ou 'dashboard.inscricoes')
        if ($currentRoute === $routePattern || Str::is($routePattern, $currentRoute)) {
            return true;
        }

        // 2. Regra de CRUD (rotas terminadas em '.list'):
        // Menus cadastrados como '<recurso>.list' permanecem ativos em todas as rotas filhas do recurso ('<recurso>.*')
        if (str_ends_with($routePattern, '.list')) {
            $prefix = substr($routePattern, 0, -strlen('.list'));

            // Exceção: o CRUD de Módulos ('modulos.list') não deve ativar nas rotas de landing dos módulos ('modulos.core', etc.) ou seleção
            if ($prefix === 'modulos') {
                $moduloRotas = ['modulos.selecionar'];
                if (class_exists('App\\Enums\\ModuloEnum')) {
                    $cases = 'App\\Enums\\ModuloEnum'::cases();
                    foreach ($cases as $case) {
                        if (method_exists($case, 'rota')) {
                            $moduloRotas[] = $case->rota();
                        }
                    }
                }
                if (in_array($currentRoute, $moduloRotas, true)) {
                    return false;
                }
            }

            if (Str::is($prefix.'.*', $currentRoute)) {
                return true;
            }
        }

        // 3. Regra de módulos operacionais (rotas terminadas em '.index'):
        // Mantém ativo para rotas filhas ('<recurso>.*'), exceto prefixos compartilhados por menus irmãos (ex: 'dashboard.')
        if (str_ends_with($routePattern, '.index') && ! str_starts_with($routePattern, 'dashboard.')) {
            $prefix = substr($routePattern, 0, -strlen('.index'));

            // Exceção idêntica para caso o CRUD de Módulos utilize .index
            if ($prefix === 'modulos') {
                $moduloRotas = ['modulos.selecionar'];
                if (class_exists('App\\Enums\\ModuloEnum')) {
                    $cases = 'App\\Enums\\ModuloEnum'::cases();
                    foreach ($cases as $case) {
                        if (method_exists($case, 'rota')) {
                            $moduloRotas[] = $case->rota();
                        }
                    }
                }
                if (in_array($currentRoute, $moduloRotas, true)) {
                    return false;
                }
            }

            if (Str::is($prefix.'.*', $currentRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna a classe CSS de menu ativo caso a rota corresponda ao padrão.
     *
     * @param  string  $routePattern  Rota ou padrão do menu a ser testado.
     * @param  string  $activeClass  Classe CSS aplicada se ativo (padrão: 'active').
     * @param  string  $inactiveClass  Classe CSS aplicada se inativo (padrão: vazio).
     * @param  string|null  $currentRoute  Rota atual de referência (opcional).
     * @return string Classe CSS resultante.
     */
    public static function setActive(
        string $routePattern,
        string $activeClass = 'active',
        string $inactiveClass = '',
        ?string $currentRoute = null
    ): string {
        return static::isRouteActive($routePattern, $currentRoute) ? $activeClass : $inactiveClass;
    }

    /**
     * Retorna os atributos de acessibilidade (ARIA) para menus ativos ou expandidos.
     *
     * @param  string  $routePattern  Rota ou padrão do menu a ser testado.
     * @param  string|null  $currentRoute  Rota atual de referência (opcional).
     * @return string Atributos HTML ARIA formatados.
     */
    public static function setActiveAria(string $routePattern, ?string $currentRoute = null): string
    {
        if (static::isRouteActive($routePattern, $currentRoute)) {
            return 'aria-expanded="true" aria-current="page"';
        }

        return 'aria-expanded="false"';
    }

    /**
     * Determina o atributo aria-current="page" para a rota atual.
     *
     * @param  string|null  $currentRoute  Rota atual de referência (opcional).
     * @return string Atributo 'aria-current="page"' se ativo ou string vazia.
     */
    public static function getMenu(?string $currentRoute = null): string
    {
        if ($currentRoute === null && function_exists('request') && request()?->route()) {
            $currentRoute = request()->route()?->getName();
        }

        $currentRoute = (string) $currentRoute;

        return static::isRouteActive($currentRoute, $currentRoute) ? 'aria-current="page"' : '';
    }
}
