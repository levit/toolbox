<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\Arr;

class ArrTest extends TestCase
{
    public function testValoresNaoVazios(): void
    {
        $input = [
            'nome' => 'Levi',
            'email' => '',
            'telefone' => null,
            'idade' => 30,
            'ativo' => false,
            'interesses' => [],
            'cidade' => 'São Paulo',
        ];

        $resultado = Arr::valoresNaoVazios($input);

        $this->assertSame([
            'nome' => 'Levi',
            'idade' => 30,
            'cidade' => 'São Paulo',
        ], $resultado);
    }

    public function testApenasChaves(): void
    {
        $input = [
            'id' => 1,
            'nome' => 'Softtan',
            'token' => 'secreto_123',
            'email' => 'contato@softtan.com.br',
            'status' => 'ativo',
        ];

        $chavesPermitidas = ['id', 'nome', 'email'];

        $resultado = Arr::apenasChaves($input, $chavesPermitidas);

        $this->assertSame([
            'id' => 1,
            'nome' => 'Softtan',
            'email' => 'contato@softtan.com.br',
        ], $resultado);

        // Chaves que não existem no array original são ignoradas
        $resultadoComChavesInexistentes = Arr::apenasChaves($input, ['id', 'inexistente']);
        $this->assertSame(['id' => 1], $resultadoComChavesInexistentes);
    }
}
