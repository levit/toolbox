<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

use InvalidArgumentException;

/**
 * Classe de Suporte para Formatação e Sanitização de Dados.
 *
 * Fornece métodos utilitários para aplicação e remoção de máscaras em documentos
 * brasileiros (CNPJ, CEP, Telefone), valores monetários (BRL), bytes e logradouros.
 */
class Format
{
    /**
     * Aplica a máscara padrão de CNPJ (00.000.000/0000-00) sobre uma cadeia de caracteres.
     *
     * @param  string|null  $cnpj  Sequência numérica do CNPJ.
     * @return string|null CNPJ formatado ou o valor original se não possuir 14 dígitos (ou null se vazio).
     */
    public static function cnpj(?string $cnpj): ?string
    {
        if (empty($cnpj)) {
            return null;
        }

        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }

    /**
     * Aplica máscara de telefone fixo (10 dígitos: (00) 0000-0000) ou celular (11 dígitos: (00) 00000-0000).
     *
     * @param  string|null  $telefone  Sequência numérica do telefone.
     * @return string|null Telefone formatado ou o valor original caso o tamanho não corresponda a 10 ou 11 dígitos.
     */
    public static function telefone(?string $telefone): ?string
    {
        if (empty($telefone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $telefone);
        $len = strlen($digits);

        if ($len === 11) {
            return sprintf(
                '(%s) %s-%s',
                substr($digits, 0, 2),
                substr($digits, 2, 5),
                substr($digits, 7, 4)
            );
        }

        return $len === 10
            ? sprintf(
                '(%s) %s-%s',
                substr($digits, 0, 2),
                substr($digits, 2, 4),
                substr($digits, 6, 4)
            )
            : $telefone;
    }

    /**
     * Converte uma quantidade de bytes em uma string legível para humanos (B, KB, MB, GB, TB).
     *
     * @param  int  $bytes  Quantidade inteira de bytes.
     * @param  int  $precision  Quantidade de casas decimais no arredondamento.
     * @return string Tamanho formatado com a unidade correspondente.
     */
    public static function bytes(int $bytes, int $precision = 2): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($unidades) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$unidades[$pow];
    }

    /**
     * Formata um valor numérico para o padrão de moeda brasileira (Real - R$ 0,00).
     *
     * @param  mixed  $valor  Valor numérico decimal a ser formatado.
     * @param  bool  $simbolo  Indica se deve prefixar com 'R$ '.
     * @return string|null Valor formatado ou null se o valor for nulo.
     */
    public static function moeda(mixed $valor, bool $simbolo = true): ?string
    {
        if ($valor === null) {
            return null;
        }

        $formatado = number_format((float) $valor, 2, ',', '.');

        return $simbolo ? 'R$ '.$formatado : $formatado;
    }

    /**
     * Converte uma string formatada em Real (ex: 'R$ 1.500,50' ou '1500,50') em float para persistência no banco.
     *
     * @param  string|null  $valor  Valor monetário formatado.
     * @return float|null Valor float compatível com SQL decimal ou null se vazio.
     */
    public static function converterMoedaBd(?string $valor): ?float
    {
        if (empty($valor)) {
            return null;
        }

        $valor = preg_replace('/[^\d,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }

    /**
     * Formata uma cadeia numérica como CEP brasileiro (00000-000).
     *
     * @param  string  $cep  CEP com 8 dígitos ou já formatado.
     * @return string CEP formatado no padrão 00000-000.
     *
     * @throws InvalidArgumentException Caso o formato do CEP seja incompatível.
     */
    public static function cep(string $cep): string
    {
        $cep = trim($cep);

        if (preg_match('/^\d{5}-\d{3}$/', $cep)) {
            return $cep;
        }

        if (preg_match('/^\d{8}$/', $cep)) {
            return substr($cep, 0, 5).'-'.substr($cep, 5, 3);
        }

        throw new InvalidArgumentException("CEP inválido: '{$cep}'. Esperado '00000000' ou '00000-000'.");
    }

    /**
     * Remove complementos numéricos e informativos secundários (ex: 's/n', 'lado ímpar', 'até 100') de um logradouro.
     *
     * Útil para normalização e busca em bases de CEP dos Correios ou geolocalização.
     *
     * @param  string  $logradouro  Nome completo do logradouro.
     * @param  bool  $manterOriginalSeVazio  Se true, devolve o logradouro original caso a limpeza resulte em vazio.
     * @return string Logradouro sanitizado e desprovido de anotações secundárias.
     */
    public static function limparLogradouro(string $logradouro, bool $manterOriginalSeVazio = true): string
    {
        $logradouro = trim($logradouro);

        $padroes = [
            '/,?\s+s\/n$/iu',
            '/(?:\s*-\s*)?\s*lado\s+í?mpar$/iu',
            '/(?:\s*-\s*)?\s*lado\s+par$/iu',
            '/(?:\s*-\s*)?\s*at[eé]\s+\d+\/\d+$/iu',
            '/(?:\s*-\s*)?\s*at[eé]\s+\d+$/iu',
            '/(?:\s*-\s*)?\s*de\s+\d+\/\d+\s+a\s+\d+\/\d+$/iu',
            '/(?:\s*-\s*)?\s*de\s+\d+\/\d+\s+ao\s+fim$/iu',
            '/(?:\s*-\s*)?\s*de\s+\d+\s+a\s+\d+$/iu',
            '/(?:\s*-\s*)?\s*de\s+\d+\s+ao\s+fim$/iu',
            '/(?:\s*-\s*)?\s*at[eé]\s+\d+\s*(?:-\s*)?\s*lado\s+í?mpar$/iu',
            '/(?:\s*-\s*)?\s*at[eé]\s+\d+\s*(?:-\s*)?\s*lado\s+par$/iu',
        ];

        foreach ($padroes as $regex) {
            $logradouro = preg_replace($regex, '', $logradouro);
        }

        $logradouro = preg_replace('/\s+-\s*$/', '', $logradouro);
        $logradouro = preg_replace('/\s+/', ' ', $logradouro);
        $logradouro = trim($logradouro);

        if ($logradouro === '') {
            return $manterOriginalSeVazio ? $logradouro : '';
        }

        return $logradouro;
    }

    /**
     * Remove todos os caracteres não-dígitos de uma string, retornando estritamente números.
     *
     * @param  string  $str  String contendo dígitos e outros caracteres.
     * @return string Apenas os números contidos na string de entrada.
     */
    public static function somenteNumeros(string $str): string
    {
        return preg_replace('/\D/', '', $str);
    }
}
