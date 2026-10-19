<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para Diagnóstico de Ambiente do Sistema e Inspeção de Rotas.
 *
 * Fornece métodos utilitários para verificação de ambiente de execução (produção/local)
 * e resolução de parâmetros da rota atual da requisição com fallbacks resilientes.
 */
class System
{
    /**
     * Verifica se a aplicação está operando em ambiente de produção.
     *
     * @return bool True se o ambiente atual for 'production'.
     */
    public static function emProducao(): bool
    {
        if (function_exists('app') && method_exists(app(), 'environment')) {
            return app()->environment('production');
        }

        return getenv('APP_ENV') === 'production';
    }

    /**
     * Verifica se a aplicação está operando em ambiente de desenvolvimento ou local.
     *
     * @return bool True se o ambiente atual for 'local' ou 'development'.
     */
    public static function emDesenvolvimento(): bool
    {
        if (function_exists('app') && method_exists(app(), 'environment')) {
            return app()->environment('local', 'development');
        }

        $env = getenv('APP_ENV');

        return $env === 'local' || $env === 'development';
    }

    /**
     * Retorna o nome da rota atualmente correspondida pela requisição HTTP.
     *
     * @return string|null Nome da rota ativa ou null caso nenhuma rota nomeada corresponda.
     */
    public static function rotaAtiva(): ?string
    {
        if (function_exists('request') && request()?->route()) {
            return request()->route()?->getName();
        }

        return null;
    }

    /**
     * Obtém o valor de um parâmetro específico da rota ativa da requisição.
     *
     * @param  string  $parametro  Nome do parâmetro definido na rota (ex: 'id', 'uuid').
     * @param  mixed  $padrao  Valor retornado caso o parâmetro não exista na rota.
     * @return mixed Valor do parâmetro ou valor padrão.
     */
    public static function parametroRota(string $parametro, mixed $padrao = null): mixed
    {
        if (function_exists('request') && request()?->route()) {
            return request()->route()?->parameter($parametro) ?? $padrao;
        }

        return $padrao;
    }
}
