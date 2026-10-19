<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\Json;

class JsonTest extends TestCase
{
    public function testEncode(): void
    {
        $dados = [
            'titulo' => 'Atenção e Comunicação',
            'preco' => 199.90,
            'tags' => ['ação', 'foco', 'gestão'],
        ];

        $json = Json::encode($dados);

        // Garante que não escapou caracteres unicode (ex: não gerou \u00e3)
        $this->assertStringContainsString('Atenção e Comunicação', $json);
        $this->assertStringContainsString('ação', $json);

        // Garante formatação legível (pretty print com quebras de linha)
        $this->assertStringContainsString("\n", $json);
    }

    public function testDecode(): void
    {
        $json = '{"nome":"Levi","empresa":"Softtan","idade":30}';

        $resultado = Json::decode($json);

        $this->assertIsArray($resultado);
        $this->assertSame('Levi', $resultado['nome']);
        $this->assertSame('Softtan', $resultado['empresa']);
        $this->assertSame(30, $resultado['idade']);

        // Nulo ou vazio retorna null
        $this->assertNull(Json::decode(null));
        $this->assertNull(Json::decode(''));
    }
}
