<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\System;
use TestAppStore;
use TestRequestStore;

class SystemTest extends TestCase
{
    public function testEmProducao(): void
    {
        TestAppStore::getInstance()->setEnvironment('production');
        $this->assertTrue(System::emProducao());
        $this->assertFalse(System::emDesenvolvimento());

        TestAppStore::getInstance()->setEnvironment('local');
        $this->assertFalse(System::emProducao());
    }

    public function testEmDesenvolvimento(): void
    {
        TestAppStore::getInstance()->setEnvironment('local');
        $this->assertTrue(System::emDesenvolvimento());
        $this->assertFalse(System::emProducao());

        TestAppStore::getInstance()->setEnvironment('development');
        $this->assertTrue(System::emDesenvolvimento());

        TestAppStore::getInstance()->setEnvironment('staging');
        $this->assertFalse(System::emDesenvolvimento());
    }

    public function testRotaAtiva(): void
    {
        // Sem rota ativa configurada
        TestRequestStore::reset();
        $this->assertNull(System::rotaAtiva());

        // Com rota configurada
        TestRequestStore::setRoute('usuarios.index');
        $this->assertSame('usuarios.index', System::rotaAtiva());
    }

    public function testParametroRota(): void
    {
        TestRequestStore::setRoute('usuarios.show', ['id' => 42, 'slug' => 'administrador']);

        $this->assertSame(42, System::parametroRota('id'));
        $this->assertSame('administrador', System::parametroRota('slug'));

        // Parâmetro não existente retorna fallback padrão
        $this->assertNull(System::parametroRota('inexistente'));
        $this->assertSame('padrao', System::parametroRota('inexistente', 'padrao'));
    }
}
