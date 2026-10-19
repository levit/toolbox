<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para disparo e gerenciamento de Notificações rápidas (Toasts) na sessão.
 */
class Notification
{
    /**
     * Envia uma mensagem toast flash na sessão para ser exibida no próximo ciclo de requisição.
     *
     * @param  string  $message  Conteúdo textual da notificação.
     * @param  string  $type  Tipo visual do toast (ex: 'info', 'success', 'warning', 'danger', 'error').
     */
    public static function toast(string $message, string $type = 'info'): void
    {
        if (function_exists('session')) {
            session()->flash('toast.'.$type, $message);

            return;
        }

        if (isset($_SESSION)) {
            $_SESSION['toast.'.$type] = $message;
        }
    }
}
