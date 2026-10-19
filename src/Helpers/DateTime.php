<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

use DateTimeInterface;
use Throwable;

/**
 * Classe de Suporte para formatação e manipulação de Data e Hora.
 *
 * Padroniza conversões para o formato brasileiro (dd/mm/aaaa) com ou sem componente de hora,
 * com suporte transparente a Carbon e fallback nativo para DateTime do PHP.
 */
class DateTime
{
    /**
     * Converte e normaliza o dado de entrada para uma instância de DateTimeInterface.
     */
    private static function parseDate(mixed $data): ?DateTimeInterface
    {
        if ($data instanceof DateTimeInterface) {
            return $data;
        }

        $result = null;
        if (is_string($data) && trim($data) !== '') {
            $carbonClass = 'Carbon\\Carbon';
            try {
                $result = class_exists($carbonClass) ? $carbonClass::parse($data) : new \DateTime($data);
            } catch (Throwable) {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * Formata uma data para o padrão brasileiro dd/mm/aaaa.
     *
     * @param  string|DateTimeInterface|null  $data  Instância de data ou string parseável.
     * @return string|null String formatada em 'd/m/Y' ou null se vazio.
     */
    public static function dataBr(mixed $data): ?string
    {
        return self::parseDate($data)?->format('d/m/Y');
    }

    /**
     * Formata uma data/hora para o padrão brasileiro dd/mm/aaaa HH:mm.
     *
     * @param  string|DateTimeInterface|null  $data  Instância de data ou string parseável.
     * @return string|null String formatada em 'd/m/Y H:i' ou null se vazio.
     */
    public static function dataHoraBr(mixed $data): ?string
    {
        return self::parseDate($data)?->format('d/m/Y H:i');
    }
}

