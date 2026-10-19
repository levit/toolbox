<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Formatador especializado para Nomes Próprios e Razões Sociais.
 *
 * Aplica regras refinadas de Title Case para o padrão brasileiro, tratando:
 * - Conectivos em minúsculo ('de', 'da', 'dos', etc.) exceto quando no início
 * - Siglas comerciais e bancárias fixas ou configuráveis (ex: BB, BMG, XP)
 * - Siglas com pontuação ou barras (ex: S.A., S/A, D.T.V.M.)
 * - Numerais romanos até XXX (ex: II, IV, XXI)
 * - Nomes com apóstrofos (ex: D'Ávila)
 */
class StrNameFormatter
{
    /**
     * Lista padrão de numerais romanos tratados em nomes próprios.
     *
     * @var list<string>
     */
    private const array NUMERAIS_ROMANOS = [
        'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X',
        'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX',
        'XXI', 'XXII', 'XXIII', 'XXIV', 'XXV', 'XXVI', 'XXVII', 'XXVIII', 'XXIX', 'XXX',
    ];

    /**
     * Formata um nome próprio ou razão social em Title Case com tratamento de siglas e numerais romanos.
     *
     * @param  string|null  $nome  Texto com o nome original a ser formatado.
     * @param  array<int, string>  $palavrasMaiusculas  Palavras adicionais para manter em maiúsculo.
     * @return string Nome formatado em Title Case com siglas tratadas.
     */
    public static function format(?string $nome, array $palavrasMaiusculas = []): string
    {
        if ($nome === null || trim($nome) === '') {
            return '';
        }

        $palavras = preg_split('/\s+/u', trim($nome), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($palavras)) {
            return '';
        }

        $excecoes = ['de', 'da', 'das', 'do', 'dos', 'e', 'ou', 'com'];
        $maiusculasMap = self::buildMaiusculasMap($palavrasMaiusculas);

        $palavrasFormatadas = array_map(
            fn (string $p, int $i): string => self::formatarPalavra($p, $i, $excecoes, $maiusculasMap),
            $palavras,
            array_keys($palavras)
        );

        return implode(' ', $palavrasFormatadas);
    }

    private static function buildMaiusculasMap(array $palavrasMaiusculas): array
    {
        $todas = ! empty($palavrasMaiusculas)
            ? array_unique(array_merge(Str::PALAVRAS_MAIUSCULAS, $palavrasMaiusculas))
            : Str::PALAVRAS_MAIUSCULAS;

        return array_fill_keys(
            array_map(static fn (string $w): string => mb_strtoupper($w, 'UTF-8'), $todas),
            true
        );
    }

    private static function formatarPalavra(
        string $palavra,
        int $index,
        array $excecoes,
        array $maiusculasMap
    ): string {
        $sigla = self::tratarSigla($palavra);
        if ($sigla !== null) {
            return $sigla;
        }

        $especial = self::tratarTermoEspecial($palavra, $maiusculasMap);
        if ($especial !== null) {
            return $especial;
        }

        return self::tratarCapitalizacao($palavra, $index, $excecoes);
    }

    private static function tratarSigla(string $palavra): ?string
    {
        if (preg_match('/^([^\p{L}\p{N}]*)([a-z](?:[.\/][a-z])+\.?)([^\p{L}\p{N}]*)$/iu', $palavra, $matches)) {
            $prefix = $matches[1];
            $sigla = mb_strtoupper($matches[2], 'UTF-8');
            if (strcasecmp($sigla, 'S.A') === 0 && ! str_ends_with($sigla, '.')) {
                $sigla .= '.';
            }

            return $prefix.$sigla.$matches[3];
        }

        return null;
    }

    private static function tratarTermoEspecial(string $palavra, array $maiusculasMap): ?string
    {
        if (! preg_match('/^([^\p{L}\p{N}]*)([\p{L}\p{N}]+)([^\p{L}\p{N}]*)$/iu', $palavra, $matches)) {
            return null;
        }

        $prefix = $matches[1];
        $core = $matches[2];
        $suffix = $matches[3];
        $termoUpper = mb_strtoupper($core, 'UTF-8');

        if (isset($maiusculasMap[$termoUpper]) || in_array($termoUpper, self::NUMERAIS_ROMANOS, true)) {
            return $prefix.$termoUpper.$suffix;
        }

        return null;
    }

    private static function tratarCapitalizacao(string $palavra, int $index, array $excecoes): string
    {
        $palavraLower = mb_strtolower($palavra, 'UTF-8');

        if ($index > 0 && in_array($palavraLower, $excecoes, true)) {
            return $palavraLower;
        }

        $convertido = mb_convert_case($palavraLower, MB_CASE_TITLE, 'UTF-8');

        return (string) preg_replace_callback("/(^|[\\'’])(\p{L})/u", static function (array $m): string {
            return $m[1].mb_strtoupper($m[2], 'UTF-8');
        }, $convertido);
    }
}
