<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para configurações e utilitários de Views e Paginação.
 */
class View
{
    /**
     * Obtém a quantidade de itens por página a partir do parâmetro 'per_page' da requisição.
     *
     * @param  int  $padrao  Quantidade padrão de itens caso o parâmetro não seja informado na requisição.
     * @return int Quantidade inteira de itens por página sanitizada.
     */
    public static function itensPorPagina(int $padrao = 15): int
    {
        if (\function_exists('request')) {
            $req = \request();
            if (is_object($req) && method_exists($req, 'get')) {
                return (int) $req->get('per_page', $padrao);
            }
        }

        if (isset($_GET['per_page'])) {
            return (int) $_GET['per_page'];
        }

        return $padrao;
    }
}
