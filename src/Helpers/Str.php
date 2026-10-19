<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

use InvalidArgumentException;

/**
 * Classe de Suporte para Manipulação de Strings.
 *
 * Fornece métodos para formatação de nomes próprios, truncamento, slugs,
 * remoção de acentos e compatibilidade com métodos do Laravel Str com fallback nativo.
 */
class Str
{
    /**
     * Nome qualificado da classe Laravel Str opcional.
     */
    private const string LARAVEL_STR = 'Illuminate\\Support\\Str';


    /**
     * Lista padrão de siglas e termos que devem obrigatoriamente permanecer em maiúsculo.
     *
     * @var list<string>
     */
    public const array PALAVRAS_MAIUSCULAS = [
        'BB',
        'BBI',
        'BMG',
        'BMP',
        'BNY',
        'BRB',
        'BS2',
        'BTG',
        'BV',
        'CCTVM',
        'CSF',
        'DTVM',
        'IP',
        'NU',
        'UBS',
        'XP',
    ];

    /**
     * Trunca um texto limitando ao número máximo de caracteres especificado, adicionando sufixo.
     *
     * @param  string|null  $texto  Texto original a ser truncado.
     * @param  int  $limite  Limite máximo de caracteres.
     * @param  string  $sufixo  Sufixo indicador de truncamento (padrão: '...').
     * @return string|null Texto truncado ou null se o texto for vazio.
     */
    public static function truncar(?string $texto, int $limite = 50, string $sufixo = '...'): ?string
    {
        if (empty($texto)) {
            return null;
        }

        if (mb_strlen($texto, 'UTF-8') <= $limite) {
            return $texto;
        }

        return mb_substr($texto, 0, $limite, 'UTF-8').$sufixo;
    }

    /**
     * Gera uma versão em formato slug (URL-friendly) para uma string informada.
     *
     * @param  string|null  $texto  Texto de entrada a ser convertido.
     * @param  string  $separator  Separador de palavras (padrão: '-').
     * @return string|null Slug gerado ou null se a string for vazia.
     */
    public static function slug(?string $texto, string $separator = '-'): ?string
    {
        if (empty($texto)) {
            return null;
        }

        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::slug($texto, $separator);
        }

        return self::nativeSlug($texto, $separator);
    }

    private static function nativeSlug(string $texto, string $separator): ?string
    {
        $limpo = static::removerAcentos($texto);
        if ($limpo === null) {
            return null;
        }

        $limpo = preg_replace('~[^\pL\d]+~u', $separator, $limpo);
        $limpo = trim((string) $limpo, $separator);
        $limpo = preg_replace('~[^-\w]+~', '', (string) $limpo);
        $limpo = mb_strtolower((string) $limpo, 'UTF-8');
        $limpo = preg_replace('~'.preg_quote($separator, '~').'+~', $separator, (string) $limpo);

        return empty($limpo) ? null : $limpo;
    }

    /**
     * Converte o texto para minúsculas com suporte a UTF-8.
     *
     * @param  string|null  $value  Texto a ser convertido.
     * @return string Texto em minúsculas.
     */
    public static function lower(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::lower($value);
        }

        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Converte o texto para maiúsculas com suporte a UTF-8.
     *
     * @param  string|null  $value  Texto a ser convertido.
     * @return string Texto em maiúsculas.
     */
    public static function upper(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::upper($value);
        }

        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Converte o texto para Title Case (primeira letra de cada palavra em maiúscula).
     *
     * @param  string|null  $value  Texto a ser convertido.
     * @return string Texto em Title Case.
     */
    public static function title(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::title($value);
        }

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Substitui ocorrências em uma string.
     *
     * @param  string|iterable<string>  $search
     * @param  string|iterable<string>  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replace(string|iterable $search, string|iterable $replace, string $subject): string
    {
        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::replace($search, $replace, $subject);
        }

        $searchArr = is_iterable($search) ? (array) $search : $search;
        $replaceArr = is_iterable($replace) ? (array) $replace : $replace;

        return str_replace($searchArr, $replaceArr, $subject);
    }

    /**
     * Determina se uma string começa com um determinado prefixo ou qualquer prefixo de uma lista.
     *
     * @param  string  $haystack
     * @param  string|iterable<string>  $needles
     * @return bool
     */
    public static function startsWith(string $haystack, string|iterable $needles): bool
    {
        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::startsWith($haystack, $needles);
        }

        foreach ((is_iterable($needles) ? $needles : [$needles]) as $needle) {
            if ((string) $needle !== '' && str_starts_with($haystack, (string) $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determina se uma string termina com um determinado sufixo ou qualquer sufixo de uma lista.
     *
     * @param  string  $haystack
     * @param  string|iterable<string>  $needles
     * @return bool
     */
    public static function endsWith(string $haystack, string|iterable $needles): bool
    {
        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::endsWith($haystack, $needles);
        }

        foreach ((is_iterable($needles) ? $needles : [$needles]) as $needle) {
            if ((string) $needle !== '' && str_ends_with($haystack, (string) $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna a porção da string antes da primeira ocorrência do delimitador.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function before(string $subject, string $search): string
    {
        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::before($subject, $search);
        }

        if ($search === '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        return $pos === false ? $subject : substr($subject, 0, $pos);
    }

    /**
     * Determina se uma dada string corresponde a um padrão curinga (*).
     *
     * @param  string|iterable<string>  $pattern
     * @param  string  $value
     * @return bool
     */
    public static function is(string|iterable $pattern, string $value): bool
    {
        $laravelStr = self::LARAVEL_STR;
        if (class_exists($laravelStr)) {
            return $laravelStr::is($pattern, $value);
        }

        return self::matchWildcardPattern($pattern, $value);
    }

    private static function matchWildcardPattern(string|iterable $pattern, string $value): bool
    {
        $patterns = is_iterable($pattern) ? $pattern : [$pattern];

        foreach ($patterns as $p) {
            $p = (string) $p;
            if ($p === $value) {
                return true;
            }

            $quoted = preg_quote($p, '#');
            $regex = str_replace('\*', '.*', $quoted);

            if (preg_match('#^'.$regex.'\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove acentuação e caracteres diacríticos de uma string, normalizando apóstrofos.
     *
     * @param  string|null  $texto  Texto a ser normalizado.
     * @param  bool  $substituirApostrofosPorEspaco  Se true, substitui apóstrofos por espaço; se false, apenas remove.
     * @return string|null Texto sem acentos ou null se o texto de entrada for null.
     */
    public static function removerAcentos(?string $texto, bool $substituirApostrofosPorEspaco = true): ?string
    {
        if ($texto === null || $texto === '') {
            return $texto;
        }

        $apostrofosPattern = "/[´`'’]/u";

        if ($substituirApostrofosPorEspaco) {
            $texto = preg_replace($apostrofosPattern, ' ', $texto);
        } else {
            $texto = preg_replace($apostrofosPattern, '', $texto);
        }

        $semAcento = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $texto);

        if ($semAcento === false) {
            $semAcento = $texto;
        }

        return preg_replace('/\s+/', ' ', trim((string) $semAcento));
    }

    /**
     * Gera um localizador ou código aleatório alfanumérico seguro.
     *
     * @param  int  $tamanho  Comprimento do código gerado (maior que zero).
     * @param  bool  $apenasLetras  Se true, gera apenas letras maiúsculas; se false, inclui números.
     * @return string Código aleatório gerado.
     *
     * @throws InvalidArgumentException Caso o tamanho informado seja menor ou igual a zero.
     */
    public static function gerarLocalizador(int $tamanho = 6, bool $apenasLetras = true): string
    {
        if ($tamanho <= 0) {
            throw new InvalidArgumentException('O tamanho deve ser maior que zero.');
        }

        $caracteres = $apenasLetras
            ? 'ABCDEFGHJKLMNPRSTUVWXYZ' // Removido caracteres confusos: I, O, Q
            : 'ABCDEFGHJKLMNPRSTUVWXYZ23456789'; // Removido caracteres confusos: I, O, Q, 0, 1

        $localizador = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $tamanho; $i++) {
            $localizador .= $caracteres[random_int(0, $max)];
        }

        return $localizador;
    }

    /**
     * Gera um nome/identificador aleatório para elementos de formulário ou referências DOM.
     *
     * @param  int  $size  Quantidade de caracteres do identificador gerado.
     * @param  bool  $lower  Se true, converte o resultado para minúsculas.
     * @return string Identificador aleatório gerado.
     */
    public static function nameRandom(int $size = 7, bool $lower = true): string
    {
        $rand = static::gerarLocalizador($size, false);

        return $lower ? static::lower($rand) : $rand;
    }

    /**
     * Converte identificadores em camelCase ou kebab-case para formato legível por humanos (Title Case).
     *
     * @param  string  $text  Texto em formato camelCase ou kebab-case (ex: 'usuarioNome', 'user-profile').
     * @return string Texto convertido em palavras legíveis (ex: 'Usuario Nome', 'User Profile').
     */
    public static function humanize(string $text): string
    {
        $separated = preg_replace('/([a-z])([A-Z])/', '$1 $2', $text) ?? $text;
        $noHyphen = str_replace('-', ' ', $separated);

        return static::title($noHyphen);
    }

    /**
     * Formata um nome próprio ou razão social em Title Case,
     * preservando siglas, abreviações (ex: S.A., S/A) e numerais romanos,
     * além de forçar palavras específicas em maiúsculo.
     *
     * @param  string|null  $nome  Texto com o nome original a ser formatado.
     * @param  array<int, string>  $palavrasMaiusculas  Palavras adicionais para manter em maiúsculo.
     * @return string Nome formatado em Title Case com siglas tratadas.
     */
    public static function nomeProprio(?string $nome, array $palavrasMaiusculas = []): string
    {
        return StrNameFormatter::format($nome, $palavrasMaiusculas);
    }

    /**
     * Determina se o valor informado possui conteúdo (não é nulo e não é uma string vazia).
     *
     * Diferente de empty(), este método preserva valores numéricos ou strings '0' como conteúdo válido.
     *
     * @param  mixed  $value  Valor a ser verificado.
     * @return bool True se contiver valor/conteúdo válido, false caso contrário.
     */
    public static function hasValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return $value !== '';
        }

        return true;
    }
}
