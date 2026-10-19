<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para serialização e desserialização de dados em formato JSON.
 *
 * Garante padronização com suporte nativo a caracteres Unicode e formatação legível.
 */
class Json
{
    /**
     * Converte um valor ou estrutura de dados em string JSON formatada com Unicode.
     *
     * @param  mixed  $valor  Valor a ser convertido (array, objeto, primitivo).
     * @return string String JSON formatada.
     */
    public static function encode(mixed $valor): string
    {
        return json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Decodifica uma string JSON para um array associativo do PHP.
     *
     * @param  string|null  $json  String JSON a ser decodificada.
     * @return array<string, mixed>|null Array associativo com os dados decodificados ou null se vazio/inválido.
     */
    public static function decode(?string $json): ?array
    {
        if (empty($json)) {
            return null;
        }

        return json_decode($json, true);
    }
}
