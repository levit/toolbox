<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

use InvalidArgumentException;

/**
 * Classe de Suporte para Validações customizadas de documentos brasileiros e dados geográficos.
 *
 * Implementa validação algorítmica de dígitos verificadores para CPF e CNPJ,
 * além de checagem estrita de Unidades Federativas (UFs) válidas.
 */
class Validate
{
    /**
     * Valida se um número de CPF é válido de acordo com o cálculo dos dígitos verificadores.
     *
     * Ignora caracteres não-numéricos e rejeita sequências repetidas (ex: 111.111.111-11).
     *
     * @param  string|null  $cpf  Número do CPF com ou sem máscara.
     * @return bool Retorna true se o CPF for válido, false caso contrário.
     */
    public static function cpf(?string $cpf): bool
    {
        $digits = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digits) !== 11 || preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($i = 0; $i < $t; $i++) {
                $d += (int) $digits[$i] * (($t + 1) - $i);
            }

            if ((int) $digits[$t] !== (((10 * $d) % 11) % 10)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida se um número de CNPJ é válido de acordo com o cálculo dos dígitos verificadores da Receita Federal.
     *
     * Ignora caracteres não-numéricos e rejeita sequências repetidas.
     *
     * @param  string|null  $cnpj  Número do CNPJ com ou sem formatação.
     * @return bool Retorna true se o CNPJ for válido, false caso contrário.
     */
    public static function cnpj(?string $cnpj): bool
    {
        $digits = preg_replace('/\D/', '', (string) $cnpj);

        if (strlen($digits) !== 14 || preg_match('/^(\d)\1{13}$/', $digits)) {
            return false;
        }

        return self::verificarDigitosCnpj($digits);
    }

    /**
     * Calcula e verifica os dois dígitos verificadores do CNPJ.
     */
    private static function verificarDigitosCnpj(string $cnpj): bool
    {
        $pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma1 += (int) $cnpj[$i] * $pesos1[$i];
        }
        $resto1 = $soma1 % 11;
        $d1 = $resto1 < 2 ? 0 : 11 - $resto1;

        $pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma2 += (int) $cnpj[$i] * $pesos2[$i];
        }
        $resto2 = $soma2 % 11;
        $d2 = $resto2 < 2 ? 0 : 11 - $resto2;

        return ((int) $cnpj[12] === $d1) && ((int) $cnpj[13] === $d2);
    }

    /**
     * Valida e normaliza a sigla de uma Unidade Federativa (UF) brasileira.
     *
     * @param  string|null  $uf  Sigla da UF informada (ex: 'SP', 'rj', ' MG ').
     * @return string Sigla normalizada em 2 letras maiúsculas.
     *
     * @throws InvalidArgumentException Caso a UF seja vazia ou não pertença à lista de estados brasileiros.
     */
    public static function uf(?string $uf): string
    {
        if (empty($uf)) {
            throw new InvalidArgumentException('Parâmetro UF não pode ser vazio');
        }

        $ufNormalizada = strtoupper(substr(trim($uf), 0, 2));

        $ufsValidas = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
        ];

        if (! in_array($ufNormalizada, $ufsValidas, true)) {
            throw new InvalidArgumentException("UF informada é inválida: '{$uf}'");
        }

        return $ufNormalizada;
    }
}
