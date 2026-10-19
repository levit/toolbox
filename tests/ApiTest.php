<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\Api;
use TestJsonResponse;

class ApiTest extends TestCase
{
    public function testRespostaSucesso(): void
    {
        $dados = ['id' => 10, 'nome' => 'Softtan'];
        $resposta = Api::respostaSucesso($dados, 'Cadastro efetuado com sucesso', 201);

        if ($resposta instanceof TestJsonResponse) {
            $this->assertSame(201, $resposta->getStatusCode());
            $payload = $resposta->getData();
        } else {
            $payload = (array) $resposta;
        }

        $this->assertTrue($payload['sucesso']);
        $this->assertSame('Cadastro efetuado com sucesso', $payload['mensagem']);
        $this->assertSame($dados, $payload['dados']);
    }

    public function testRespostaSucessoPadrao(): void
    {
        $resposta = Api::respostaSucesso();

        if ($resposta instanceof TestJsonResponse) {
            $this->assertSame(200, $resposta->getStatusCode());
            $payload = $resposta->getData();
        } else {
            $payload = (array) $resposta;
        }

        $this->assertTrue($payload['sucesso']);
        $this->assertSame('Operação realizada com sucesso', $payload['mensagem']);
        $this->assertNull($payload['dados']);
    }

    public function testRespostaErro(): void
    {
        $erros = ['email' => ['E-mail já está em uso']];
        $resposta = Api::respostaErro('Falha de validação', 422, $erros);

        if ($resposta instanceof TestJsonResponse) {
            $this->assertSame(422, $resposta->getStatusCode());
            $payload = $resposta->getData();
        } else {
            $payload = (array) $resposta;
        }

        $this->assertFalse($payload['sucesso']);
        $this->assertSame('Falha de validação', $payload['mensagem']);
        $this->assertSame($erros, $payload['erros']);
    }

    public function testRespostaErroPadrao(): void
    {
        $resposta = Api::respostaErro();

        if ($resposta instanceof TestJsonResponse) {
            $this->assertSame(400, $resposta->getStatusCode());
            $payload = $resposta->getData();
        } else {
            $payload = (array) $resposta;
        }

        $this->assertFalse($payload['sucesso']);
        $this->assertSame('Erro ao processar requisição', $payload['mensagem']);
        $this->assertNull($payload['erros']);
    }
}
