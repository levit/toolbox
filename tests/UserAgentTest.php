<?php

declare(strict_types=1);

namespace Softtan\Toolbox\Tests;

use Softtan\Toolbox\Helpers\UserAgent;

class UserAgentTest extends TestCase
{
    public function testParseDesconhecidoOuVazio(): void
    {
        $parsed = UserAgent::parse('');
        $this->assertSame('Desconhecido', $parsed['os']);
        $this->assertSame('Desconhecido', $parsed['browser']);
        $this->assertFalse($parsed['is_bot']);
        $this->assertFalse($parsed['is_cli']);

        $parsedNa = UserAgent::parse('N/A');
        $this->assertSame('Desconhecido', $parsedNa['os']);
        $this->assertSame('Desconhecido', $parsedNa['browser']);
    }

    public function testDetectOs(): void
    {
        // Windows 10/11
        $uaWin = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $osWin = UserAgent::detectOs($uaWin);
        $this->assertSame('Windows 10/11', $osWin['name']);
        $this->assertSame('bi-windows', $osWin['icon']);

        // macOS
        $uaMac = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15';
        $osMac = UserAgent::detectOs($uaMac);
        $this->assertSame('macOS', $osMac['name']);
        $this->assertSame('bi-apple', $osMac['icon']);

        // Ubuntu Linux
        $uaUbuntu = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/118.0';
        $osUbuntu = UserAgent::detectOs($uaUbuntu);
        $this->assertSame('Ubuntu Linux', $osUbuntu['name']);
        $this->assertSame('bi-ubuntu', $osUbuntu['icon']);

        // Linux Genérico
        $uaLinux = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36';
        $osLinux = UserAgent::detectOs($uaLinux);
        $this->assertSame('Linux', $osLinux['name']);
        $this->assertSame('bi-tux', $osLinux['icon']);

        // Android
        $uaAndroid = 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36';
        $osAndroid = UserAgent::detectOs($uaAndroid);
        $this->assertStringContainsString('Android', $osAndroid['name']);
        $this->assertSame('bi-android2', $osAndroid['icon']);

        // iOS iPhone
        $uaIphone = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15';
        $osIphone = UserAgent::detectOs($uaIphone);
        $this->assertSame('iOS (iPhone)', $osIphone['name']);
        $this->assertSame('bi-apple', $osIphone['icon']);

        // iPad
        $uaIpad = 'Mozilla/5.0 (iPad; CPU OS 16_5 like Mac OS X) AppleWebKit/605.1.15';
        $osIpad = UserAgent::detectOs($uaIpad);
        $this->assertSame('iPadOS (iPad)', $osIpad['name']);

        // Bot (Googlebot)
        $uaBot = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $osBot = UserAgent::detectOs($uaBot);
        $this->assertTrue($osBot['is_bot']);
        $this->assertSame('bi-robot', $osBot['icon']);

        // CLI / API (curl)
        $uaCurl = 'curl/7.88.1';
        $osCurl = UserAgent::detectOs($uaCurl);
        $this->assertTrue($osCurl['is_cli']);
        $this->assertSame('bi-terminal', $osCurl['icon']);
    }

    public function testDetectBrowser(): void
    {
        // Google Chrome
        $uaChrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $browserChrome = UserAgent::detectBrowser($uaChrome);
        $this->assertSame('Google Chrome 120', $browserChrome['name']);
        $this->assertSame('bi-browser-chrome', $browserChrome['icon']);

        // Mozilla Firefox
        $uaFirefox = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0';
        $browserFirefox = UserAgent::detectBrowser($uaFirefox);
        $this->assertSame('Mozilla Firefox 121', $browserFirefox['name']);
        $this->assertSame('bi-browser-firefox', $browserFirefox['icon']);

        // Apple Safari
        $uaSafari = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15';
        $browserSafari = UserAgent::detectBrowser($uaSafari);
        $this->assertSame('Apple Safari 17', $browserSafari['name']);
        $this->assertSame('bi-browser-safari', $browserSafari['icon']);

        // Microsoft Edge
        $uaEdge = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.2210.91';
        $browserEdge = UserAgent::detectBrowser($uaEdge);
        $this->assertSame('Microsoft Edge 120', $browserEdge['name']);
        $this->assertSame('bi-browser-edge', $browserEdge['icon']);

        // Opera
        $uaOpera = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0';
        $browserOpera = UserAgent::detectBrowser($uaOpera);
        $this->assertSame('Opera 106', $browserOpera['name']);

        // Postman Runtime
        $uaPostman = 'PostmanRuntime/7.36.0';
        $browserPostman = UserAgent::detectBrowser($uaPostman);
        $this->assertSame('PostmanRuntime', $browserPostman['name']);
        $this->assertTrue($browserPostman['is_cli']);
    }

    public function testParseCompleto(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';
        $parsed = UserAgent::parse($ua);

        $this->assertSame('Windows 10/11', $parsed['os']);
        $this->assertSame('bi-windows', $parsed['os_icon']);
        $this->assertSame('Google Chrome 122', $parsed['browser']);
        $this->assertSame('bi-browser-chrome', $parsed['browser_icon']);
        $this->assertSame($ua, $parsed['full']);
        $this->assertFalse($parsed['is_bot']);
        $this->assertFalse($parsed['is_cli']);
    }
}
