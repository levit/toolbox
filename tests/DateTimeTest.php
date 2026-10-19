<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use DateTime as PhpDateTime;
use DateTimeImmutable;
use Softtan\Toolbox\Helpers\DateTime;

class DateTimeTest extends TestCase
{
    public function testDataBr(): void
    {
        // A partir de string 'Y-m-d'
        $this->assertSame('12/10/2026', DateTime::dataBr('2026-10-12'));
        $this->assertSame('01/01/2025', DateTime::dataBr('2025-01-01'));

        // A partir de DateTime do PHP
        $dt = new PhpDateTime('2026-10-12 14:30:00');
        $this->assertSame('12/10/2026', DateTime::dataBr($dt));

        // A partir de DateTimeImmutable
        $dti = new DateTimeImmutable('2026-12-25');
        $this->assertSame('25/12/2026', DateTime::dataBr($dti));

        // Vazio ou nulo retorna null
        $this->assertNull(DateTime::dataBr(null));
        $this->assertNull(DateTime::dataBr(''));

        // String inválida retorna null
        $this->assertNull(DateTime::dataBr('data-invalida-xyz'));
    }

    public function testDataHoraBr(): void
    {
        // A partir de string 'Y-m-d H:i:s'
        $this->assertSame('12/10/2026 15:45', DateTime::dataHoraBr('2026-10-12 15:45:00'));

        // A partir de DateTime do PHP
        $dt = new PhpDateTime('2026-10-12 09:05:00');
        $this->assertSame('12/10/2026 09:05', DateTime::dataHoraBr($dt));

        // Vazio ou nulo retorna null
        $this->assertNull(DateTime::dataHoraBr(null));
        $this->assertNull(DateTime::dataHoraBr(''));

        // String inválida retorna null
        $this->assertNull(DateTime::dataHoraBr('data-invalida-xyz'));
    }
}
