<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use InvalidArgumentException;
use Softtan\Toolbox\Helpers\Format;

class FormatTest extends TestCase
{
    public function testCnpj(): void
    {
        // CNPJ válido com 14 dígitos
        $this->assertSame('12.345.678/0001-95', Format::cnpj('12345678000195'));

        // CNPJ já com pontuação deve ser preservado e reformatado
        $this->assertSame('12.345.678/0001-95', Format::cnpj('12.345.678/0001-95'));

        // Vazio ou null
        $this->assertNull(Format::cnpj(null));
        $this->assertNull(Format::cnpj(''));

        // Tamanho incompatível (deve retornar string original não-formatada)
        $this->assertSame('12345', Format::cnpj('12345'));
    }

    public function testTelefone(): void
    {
        // Celular 11 dígitos
        $this->assertSame('(11) 98765-4321', Format::telefone('11987654321'));

        // Telefone fixo 10 dígitos
        $this->assertSame('(11) 3333-4444', Format::telefone('1133334444'));

        // Vazio ou null
        $this->assertNull(Format::telefone(null));
        $this->assertNull(Format::telefone(''));

        // Tamanho incompatível
        $this->assertSame('123456', Format::telefone('123456'));
    }

    public function testBytes(): void
    {
        $this->assertSame('0 B', Format::bytes(0));
        $this->assertSame('500 B', Format::bytes(500));
        $this->assertSame('1 KB', Format::bytes(1024));
        $this->assertSame('1.5 KB', Format::bytes(1536, 1));
        $this->assertSame('1 MB', Format::bytes(1048576));
        $this->assertSame('1 GB', Format::bytes(1073741824));
        $this->assertSame('1 TB', Format::bytes(1099511627776));
    }

    public function testMoeda(): void
    {
        // Com símbolo padrão
        $this->assertSame('R$ 10,50', Format::moeda(10.5));
        $this->assertSame('R$ 1.500,75', Format::moeda(1500.75));
        $this->assertSame('R$ 0,00', Format::moeda(0));

        // Sem símbolo
        $this->assertSame('10,50', Format::moeda(10.5, false));
        $this->assertSame('1.500,75', Format::moeda(1500.75, false));

        // Nulo
        $this->assertNull(Format::moeda(null));
    }

    public function testConverterMoedaBd(): void
    {
        $this->assertSame(1234.56, Format::converterMoedaBd('R$ 1.234,56'));
        $this->assertSame(1500.5, Format::converterMoedaBd('1500,50'));
        $this->assertSame(0.99, Format::converterMoedaBd('0,99'));
        $this->assertNull(Format::converterMoedaBd(null));
        $this->assertNull(Format::converterMoedaBd(''));
    }

    public function testCep(): void
    {
        // 8 dígitos numéricos
        $this->assertSame('01001-000', Format::cep('01001000'));

        // Já formatado
        $this->assertSame('01001-000', Format::cep('01001-000'));

        // Com espaços
        $this->assertSame('01001-000', Format::cep('  01001000  '));
    }

    public function testCepInvalidoLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Format::cep('1234');
    }

    public function testLimparLogradouro(): void
    {
        $this->assertSame('Rua das Flores', Format::limparLogradouro('Rua das Flores, s/n'));
        $this->assertSame('Avenida Paulista', Format::limparLogradouro('Avenida Paulista - lado ímpar'));
        $this->assertSame('Rua XV de Novembro', Format::limparLogradouro('Rua XV de Novembro até 100'));
        $this->assertSame('Alameda Santos', Format::limparLogradouro('Alameda Santos de 10 a 50'));
        $this->assertSame('Travessa do Ouvidor', Format::limparLogradouro('Travessa do Ouvidor, s/n'));
    }

    public function testSomenteNumeros(): void
    {
        $this->assertSame('12345678901', Format::somenteNumeros('123.456.789-01'));
        $this->assertSame('12345678000195', Format::somenteNumeros('12.345.678/0001-95'));
        $this->assertSame('01001000', Format::somenteNumeros('01001-000'));
        $this->assertSame('', Format::somenteNumeros('Apenas letras!'));
    }
}
