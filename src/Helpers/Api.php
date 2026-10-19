<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

// Removed uninstalled import to resolve Intelephense P1009/P1133

/**
 * Classe de Suporte para respostas padronizadas da API do sistema.
 *
 * Fornece métodos utilitários estáticos para construir respostas JSON consistentes
 * contendo flags de sucesso, mensagens descritivas, payloads de dados e códigos HTTP.
 */
class Api
{
    /**
     * Retorna uma resposta JSON padronizada de sucesso para APIs.
     *
     * @param  mixed  $dados  Dados ou payload a serem transmitidos no corpo da resposta.
     * @param  string  $mensagem  Mensagem informativa de confirmação da operação.
     * @param  int  $codigo  Código de status HTTP (padrão: 200 OK).
     * @return mixed Resposta HTTP em JsonResponse ou array estruturado.
     */
    public static function respostaSucesso(mixed $dados = null, string $mensagem = 'Operação realizada com sucesso', int $codigo = 200): mixed
    {
        $payload = [
            'sucesso' => true,
            'mensagem' => $mensagem,
            'dados' => $dados,
        ];

        $jsonClass = 'Illuminate\\Http\\JsonResponse';
        if (class_exists($jsonClass)) {
            return new $jsonClass($payload, $codigo);
        }

        if (\function_exists('response')) {
            return \response()->json($payload, $codigo);
        }

        return $payload;
    }

    /**
     * Retorna uma resposta JSON padronizada de erro ou falha para APIs.
     *
     * @param  string  $mensagem  Mensagem descritiva do erro ou motivo do impedimento.
     * @param  int  $codigo  Código de status HTTP (padrão: 400 Bad Request).
     * @param  mixed  $erros  Detalhes específicos dos erros ou mensagens de validação.
     * @return mixed Resposta HTTP em JsonResponse ou array estruturado.
     */
    public static function respostaErro(string $mensagem = 'Erro ao processar requisição', int $codigo = 400, mixed $erros = null): mixed
    {
        $payload = [
            'sucesso' => false,
            'mensagem' => $mensagem,
            'erros' => $erros,
        ];

        $jsonClass = 'Illuminate\\Http\\JsonResponse';
        if (class_exists($jsonClass)) {
            return new $jsonClass($payload, $codigo);
        }

        if (\function_exists('response')) {
            return \response()->json($payload, $codigo);
        }

        return $payload;
    }
}

