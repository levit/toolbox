<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use InvalidArgumentException;
use Softtan\Toolbox\Helpers\Validate;

class ValidateTest extends TestCase
{
    public function testCpfValido(): void
    {
        // CPFs válidos matematicamente (com e sem formatação)
        $this->assertTrue(Validate::cpf('52998224725'));
        $this->assertTrue(Validate::cpf('529.982.247-25'));
        $this->assertTrue(Validate::cpf('11144477735'));
        $this->assertTrue(Validate::cpf('111.444.777-35'));
    }

    public function testCpfInvalido(): void
    {
        // Dígitos verificadores incorretos
        $this->assertFalse(Validate::cpf('12345678900'));
        $this->assertFalse(Validate::cpf('123.456.789-00'));

        // Sequências repetidas
        $this->assertFalse(Validate::cpf('00000000000'));
        $this->assertFalse(Validate::cpf('11111111111'));
        $this->assertFalse(Validate::cpf('99999999999'));

        // Tamanho incorreto
        $this->assertFalse(Validate::cpf('123'));
        $this->assertFalse(Validate::cpf('123456789012'));

        // Vazio ou nulo
        $this->assertFalse(Validate::cpf(null));
        $this->assertFalse(Validate::cpf(''));
    }

    public function testCnpjValido(): void
    {
        // CNPJs válidos matematicamente
        $this->assertTrue(Validate::cnpj('11222333000181'));
        $this->assertTrue(Validate::cnpj('11.222.333/0001-81'));
        $this->assertTrue(Validate::cnpj('12345678000195'));
        $this->assertTrue(Validate::cnpj('12.345.678/0001-95'));
    }

    public function testCnpjInvalido(): void
    {
        // Dígitos verificadores errados
        $this->assertFalse(Validate::cnpj('11222333000100'));
        $this->assertFalse(Validate::cnpj('11.222.333/0001-00'));

        // Sequências repetidas
        $this->assertFalse(Validate::cnpj('00000000000000'));
        $this->assertFalse(Validate::cnpj('11111111111111'));

        // Tamanho incorreto
        $this->assertFalse(Validate::cnpj('123'));
        $this->assertFalse(Validate::cnpj('123456789012345'));

        // Vazio ou nulo
        $this->assertFalse(Validate::cnpj(null));
        $this->assertFalse(Validate::cnpj(''));
    }

    public function testUfValida(): void
    {
        $this->assertSame('SP', Validate::uf('SP'));
        $this->assertSame('RJ', Validate::uf('rj'));
        $this->assertSame('MG', Validate::uf(' MG '));
        $this->assertSame('BA', Validate::uf('ba'));
        $this->assertSame('RS', Validate::uf('rs'));
        $this->assertSame('DF', Validate::uf('df'));
    }

    public function testUfInvalidaLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("UF informada é inválida: 'XX'");
        Validate::uf('XX');
    }

    public function testUfVaziaLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parâmetro UF não pode ser vazio');
        Validate::uf('');
    }

    public function testUfNulaLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parâmetro UF não pode ser vazio');
        Validate::uf(null);
    }
}
