<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Helpers;

/**
 * Classe de Suporte para parsing e identificação de User-Agents HTTP.
 *
 * Extrai Sistema Operacional, Navegador Web, ferramentas de CLI/API e bots/crawlers,
 * mapeando automaticamente os ícones correspondentes do Bootstrap Icons.
 */
class UserAgent
{
    /**
     * Analisa a string de User-Agent e extrai metadados do Sistema Operacional e Navegador,
     * incluindo nomes amigáveis e classes de ícones Bootstrap Icons correspondentes.
     *
     * @return array{
     *     os: string,
     *     os_icon: string,
     *     browser: string,
     *     browser_icon: string,
     *     full: string,
     *     is_bot: bool,
     *     is_cli: bool
     * }
     */
    public static function parse(?string $userAgent): array
    {
        $raw = trim((string) $userAgent);

        if ($raw === '' || $raw === 'N/A' || $raw === '-') {
            return [
                'os' => 'Desconhecido',
                'os_icon' => 'bi-pc',
                'browser' => 'Desconhecido',
                'browser_icon' => 'bi-globe',
                'full' => $raw !== '' ? $raw : 'N/A',
                'is_bot' => false,
                'is_cli' => false,
            ];
        }

        $osData = static::detectOs($raw);
        $browserData = static::detectBrowser($raw);

        return [
            'os' => $osData['name'],
            'os_icon' => $osData['icon'],
            'browser' => $browserData['name'],
            'browser_icon' => $browserData['icon'],
            'full' => $raw,
            'is_bot' => $osData['is_bot'] || $browserData['is_bot'],
            'is_cli' => $osData['is_cli'] || $browserData['is_cli'],
        ];
    }

    /**
     * Detecta o Sistema Operacional e seu ícone Bootstrap Icon associado.
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    public static function detectOs(string $ua): array
    {
        $special = self::detectOsBotOrCli($ua);
        if ($special !== null) {
            return $special;
        }

        return self::detectOsSystem($ua);
    }

    /**
     * Detecta se o User-Agent corresponde a um Bot ou cliente CLI/API.
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}|null
     */
    private static function detectOsBotOrCli(string $ua): ?array
    {
        // 1. Bots / Spiders / Rastreadores
        if (preg_match('/(Googlebot|bingbot|Baiduspider|YandexBot|DuckDuckBot|Slurp|facebookexternalhit|Twitterbot|LinkedInBot|bot|spider|crawl)/i', $ua, $m)) {
            return [
                'name' => 'Bot / Rastreador ('.$m[1].')',
                'icon' => 'bi-robot',
                'is_bot' => true,
                'is_cli' => false,
            ];
        }

        // 2. Clientes HTTP / APIs / Scripts de linha de comando
        if (preg_match('/(PostmanRuntime|Postman|curl|Wget|GuzzleHttp|python-requests|Insomnia|axios|Apache-HttpClient|Go-http-client)/i', $ua, $m)) {
            return [
                'name' => 'Cliente API / CLI ('.$m[1].')',
                'icon' => 'bi-terminal',
                'is_bot' => false,
                'is_cli' => true,
            ];
        }

        return null;
    }

    /**
     * Detecta o sistema operacional padrão a partir do User-Agent.
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    private static function detectOsSystem(string $ua): array
    {
        if (preg_match('/Android(?:\s+([\d.]+))?/i', $ua, $m)) {
            $version = ! empty($m[1]) ? ' '.$m[1] : '';

            return ['name' => 'Android'.$version, 'icon' => 'bi-android2', 'is_bot' => false, 'is_cli' => false];
        }

        if (preg_match('/(FreeBSD|OpenBSD|NetBSD)/i', $ua, $m)) {
            return ['name' => $m[1], 'icon' => 'bi-pc', 'is_bot' => false, 'is_cli' => false];
        }

        return self::matchOsPattern($ua);
    }

    /**
     * Mapeia padrões fixos de sistema operacional (iOS, macOS, Windows, Linux).
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    private static function matchOsPattern(string $ua): array
    {
        $patterns = [
            '/iPhone/i' => ['iOS (iPhone)', 'bi-apple'],
            '/iPad/i' => ['iPadOS (iPad)', 'bi-apple'],
            '/iPod/i' => ['iOS (iPod)', 'bi-apple'],
            '/(Macintosh|Mac OS X|macOS)/i' => ['macOS', 'bi-apple'],
            '/Windows NT 10\.0/i' => ['Windows 10/11', 'bi-windows'],
            '/Windows NT 6\.3/i' => ['Windows 8.1', 'bi-windows'],
            '/Windows NT 6\.2/i' => ['Windows 8', 'bi-windows'],
            '/Windows NT 6\.1/i' => ['Windows 7', 'bi-windows'],
            '/Windows/i' => ['Windows', 'bi-windows'],
            '/Ubuntu/i' => ['Ubuntu Linux', 'bi-ubuntu'],
            '/CrOS/i' => ['ChromeOS', 'bi-laptop'],
            '/(Linux|X11)/i' => ['Linux', 'bi-tux'],
        ];

        foreach ($patterns as $pattern => $info) {
            if (preg_match($pattern, $ua)) {
                return ['name' => $info[0], 'icon' => $info[1], 'is_bot' => false, 'is_cli' => false];
            }
        }

        return [
            'name' => 'SO Desconhecido',
            'icon' => 'bi-pc',
            'is_bot' => false,
            'is_cli' => false,
        ];
    }

    /**
     * Detecta o Navegador Web / Cliente e seu ícone Bootstrap Icon associado.
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    public static function detectBrowser(string $ua): array
    {
        $special = self::detectBrowserBotOrCli($ua);
        if ($special !== null) {
            return $special;
        }

        return self::detectStandardBrowser($ua);
    }

    /**
     * Detecta se o navegador é um Bot ou Ferramenta de Linha de Comando.
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}|null
     */
    private static function detectBrowserBotOrCli(string $ua): ?array
    {
        // 1. Bots / Spiders
        if (preg_match('/(Googlebot|bingbot|Baiduspider|YandexBot|DuckDuckBot|Slurp|bot|spider|crawl)/i', $ua, $m)) {
            return [
                'name' => 'Bot ('.$m[1].')',
                'icon' => 'bi-robot',
                'is_bot' => true,
                'is_cli' => false,
            ];
        }

        // 2. Ferramentas CLI / API
        if (preg_match('/(PostmanRuntime|Postman|curl|Wget|GuzzleHttp|python-requests|Insomnia|axios)/i', $ua, $m)) {
            return [
                'name' => $m[1],
                'icon' => 'bi-terminal',
                'is_bot' => false,
                'is_cli' => true,
            ];
        }

        return null;
    }

    /**
     * Detecta navegadores padrões ou legacy (IE).
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    private static function detectStandardBrowser(string $ua): array
    {
        if (preg_match('/(?:MSIE\s+([\d.]+)|Trident\/.*rv:([\d.]+))/i', $ua, $m)) {
            $ver = ! empty($m[1]) ? $m[1] : ($m[2] ?? '');
            $major = explode('.', $ver)[0];

            return [
                'name' => "Internet Explorer {$major}",
                'icon' => 'bi-browser-edge',
                'is_bot' => false,
                'is_cli' => false,
            ];
        }

        return self::matchBrowserPatterns($ua);
    }

    /**
     * Mapeia padrões de navegadores modernos (Edge, Opera, Vivaldi, Chrome, Firefox, Safari).
     *
     * @return array{name: string, icon: string, is_bot: bool, is_cli: bool}
     */
    private static function matchBrowserPatterns(string $ua): array
    {
        $patterns = [
            '/(?:Edg|Edge)\/([\d.]+)/i' => ['Microsoft Edge %s', 'bi-browser-edge'],
            '/(?:OPR|Opera)\/([\d.]+)/i' => ['Opera %s', 'bi-globe'],
            '/Vivaldi\/([\d.]+)/i' => ['Vivaldi %s', 'bi-globe'],
            '/Brave\/([\d.]+)/i' => ['Brave %s', 'bi-browser-chrome'],
            '/SamsungBrowser\/([\d.]+)/i' => ['Samsung Internet %s', 'bi-browser-chrome'],
            '/(?:Firefox|FxiOS)\/([\d.]+)/i' => ['Mozilla Firefox %s', 'bi-browser-firefox'],
            '/(?:Chrome|CriOS)\/([\d.]+)/i' => ['Google Chrome %s', 'bi-browser-chrome'],
            '/Version\/([\d.]+).*Safari/i' => ['Apple Safari %s', 'bi-browser-safari'],
        ];

        foreach ($patterns as $pattern => $info) {
            if (preg_match($pattern, $ua, $m)) {
                $major = explode('.', $m[1])[0];

                return [
                    'name' => sprintf($info[0], $major),
                    'icon' => $info[1],
                    'is_bot' => false,
                    'is_cli' => false,
                ];
            }
        }

        return [
            'name' => 'Navegador Web',
            'icon' => 'bi-globe',
            'is_bot' => false,
            'is_cli' => false,
        ];
    }
}
