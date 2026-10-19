<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\Menu;
use TestRequestStore;

class MenuTest extends TestCase
{
    public function testIsRouteActiveCorrespondenciaExata(): void
    {
        $this->assertTrue(Menu::isRouteActive('cargos.list', 'cargos.list'));
        $this->assertTrue(Menu::isRouteActive('dashboard', 'dashboard'));
        $this->assertFalse(Menu::isRouteActive('cargos.list', 'usuarios.list'));
    }

    public function testIsRouteActiveViaRequest(): void
    {
        TestRequestStore::setRoute('cargos.list');
        $this->assertTrue(Menu::isRouteActive('cargos.list'));
        $this->assertFalse(Menu::isRouteActive('usuarios.list'));
    }

    public function testIsRouteActiveCuringa(): void
    {
        $this->assertTrue(Menu::isRouteActive('cargos.*', 'cargos.create'));
        $this->assertTrue(Menu::isRouteActive('cargos.*', 'cargos.edit'));
        $this->assertTrue(Menu::isRouteActive('cargos.*', 'cargos.show'));
        $this->assertFalse(Menu::isRouteActive('cargos.*', 'usuarios.create'));
    }

    public function testIsRouteActiveRegraCrud(): void
    {
        // Padrão que termina em '.list' deve ativar automaticamente para as rotas filhas do recurso
        $this->assertTrue(Menu::isRouteActive('cargos.list', 'cargos.create'));
        $this->assertTrue(Menu::isRouteActive('cargos.list', 'cargos.edit'));
        $this->assertTrue(Menu::isRouteActive('cargos.list', 'cargos.show'));
        $this->assertTrue(Menu::isRouteActive('cargos.list', 'cargos.delete'));
        $this->assertFalse(Menu::isRouteActive('cargos.list', 'outro_recurso.create'));
    }

    public function testSetActive(): void
    {
        // Ativo (padrão 'active')
        $this->assertSame('active', Menu::setActive('cargos.list', 'active', '', 'cargos.list'));

        // Inativo (padrão '')
        $this->assertSame('', Menu::setActive('cargos.list', 'active', '', 'usuarios.list'));

        // Classes customizadas
        $this->assertSame('is-active text-primary', Menu::setActive('cargos.list', 'is-active text-primary', 'text-muted', 'cargos.list'));
        $this->assertSame('text-muted', Menu::setActive('cargos.list', 'is-active text-primary', 'text-muted', 'usuarios.list'));
    }

    public function testSetActiveAria(): void
    {
        $this->assertSame('aria-expanded="true" aria-current="page"', Menu::setActiveAria('cargos.list', 'cargos.list'));
        $this->assertSame('aria-expanded="false"', Menu::setActiveAria('cargos.list', 'usuarios.list'));
    }

    public function testGetMenu(): void
    {
        $this->assertSame('aria-current="page"', Menu::getMenu('dashboard'));
    }
}
