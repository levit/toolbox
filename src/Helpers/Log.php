<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

// Removed uninstalled import to resolve Intelephense P1009

/**
 * Classe de Suporte para registro de Logs padronizados no sistema.
 *
 * Encapsula chamadas ao logger do Laravel permitindo definição de nível dinâmico,
 * com fallback nativo para error_log caso o framework não esteja presente.
 */
class Log
{
    /**
     * Registra uma mensagem no log da aplicação com nível e contexto especificados.
     *
     * @param  string  $mensagem  Texto principal da mensagem de log.
     * @param  string  $nivel  Nível do log (ex: 'info', 'warning', 'error', 'debug', 'alert').
     * @param  array<string, mixed>  $contexto  Dados contextuais adicionais para inspeção.
     */
    public static function registrar(string $mensagem, string $nivel = 'info', array $contexto = []): void
    {
        $laravelLog = 'Illuminate\\Support\\Facades\\Log';
        if (class_exists($laravelLog)) {
            $laravelLog::$nivel($mensagem, $contexto);

            return;
        }

        $contextStr = ! empty($contexto) ? ' ' . json_encode($contexto, JSON_UNESCAPED_UNICODE) : '';
        error_log(sprintf('[%s] %s%s', strtoupper($nivel), $mensagem, $contextStr));
    }
}
