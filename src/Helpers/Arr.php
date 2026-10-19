<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para manipulação e filtragem avançada de Arrays.
 *
 * Fornece métodos utilitários para saneamento de coleções e extração seletiva de chaves.
 */
class Arr
{
    /**
     * Filtra um array removendo recursiva ou iterativamente todos os valores considerados vazios pelo PHP.
     *
     * @param  array<array-key, mixed>  $array  Array de origem a ser filtrado.
     * @return array<array-key, mixed> Novo array contendo somente os elementos com valor não-vazio.
     */
    public static function valoresNaoVazios(array $array): array
    {
        return array_filter($array, static fn (mixed $valor): bool => ! empty($valor));
    }

    /**
     * Retorna um novo array contendo exclusivamente as chaves especificadas na lista branca.
     *
     * @param  array<array-key, mixed>  $array  Array associativo original.
     * @param  array<int, array-key>  $chaves  Lista de chaves permitidas no retorno.
     * @return array<array-key, mixed> Array filtrado apenas com as chaves informadas.
     */
    public static function apenasChaves(array $array, array $chaves): array
    {
        return array_intersect_key($array, array_flip($chaves));
    }
}
