<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\Notification;

class NotificationTest extends TestCase
{
    public function testToast(): void
    {
        Notification::toast('Operação concluída com êxito!', 'success');
        $this->assertSame('Operação concluída com êxito!', session('toast.success'));

        Notification::toast('Atenção aos dados informados', 'warning');
        $this->assertSame('Atenção aos dados informados', session('toast.warning'));

        Notification::toast('Ocorreu uma falha no sistema', 'error');
        $this->assertSame('Ocorreu uma falha no sistema', session('toast.error'));

        Notification::toast('Informativo geral'); // tipo padrão 'info'
        $this->assertSame('Informativo geral', session('toast.info'));
    }
}
