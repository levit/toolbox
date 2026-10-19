# Detecção de Dispositivos e Navegadores (`UserAgent`)

O helper `UserAgent` realiza análise profunda e parsing de cabeçalhos HTTP `User-Agent`, identificando de forma precisa o **Sistema Operacional**, o **Navegador Web**, clientes de **Linha de Comando / API** (CLI) e **Bots / Rastreadores**, mapeando automaticamente os ícones visuais correspondentes do **Bootstrap Icons**.

- **Classe:** `Softtan\Toolbox\Helpers\UserAgent`
- **Substitui:** `UserAgentSupport`

```php
use Softtan\Toolbox\Helpers\UserAgent;
```

---

## 📌 Sumário de Métodos

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| [`UserAgent::parse(?string $userAgent)`](#useragentparse) | `array` | Analisa a string e retorna um payload completo com OS, Browser, Ícones e flags booleanas. |
| [`UserAgent::detectOs(string $ua)`](#useragentdetectos) | `array` | Identifica o Sistema Operacional e o respectivo ícone do Bootstrap Icons. |
| [`UserAgent::detectBrowser(string $ua)`](#useragentdetectbrowser) | `array` | Identifica o Navegador / Cliente HTTP com versão principal e seu respectivo ícone. |

---

## 📖 Referência Detalhada dos Métodos

### `UserAgent::parse`

Analisa uma string de `User-Agent` (geralmente obtida de `request()->userAgent()`) e retorna um array associativo estruturado com metadados do cliente.

```php
public static function parse(?string $userAgent): array
```

#### Estrutura do Payload Retornado

```php
array{
    os: string,           // Nome amigável do Sistema Operacional (ex: "Windows 10/11", "Ubuntu Linux")
    os_icon: string,      // Classe CSS do Bootstrap Icons (ex: "bi-windows", "bi-apple", "bi-ubuntu")
    browser: string,      // Nome e versão principal do Navegador (ex: "Google Chrome 124")
    browser_icon: string, // Classe CSS do Bootstrap Icons (ex: "bi-browser-chrome", "bi-browser-firefox")
    full: string,         // String original do User-Agent sanitizada
    is_bot: bool,         // True se identificado como bot ou crawler
    is_cli: bool          // True se identificado como cliente API ou CLI (curl, Postman, Guzzle)
}
```

#### Tratamento de Casos Vazios
Se a string for nula, vazia, `'-'` ou `'N/A'`, o método retorna um fallback seguro sem lançar exceções:
```php
[
    'os' => 'Desconhecido',
    'os_icon' => 'bi-pc',
    'browser' => 'Desconhecido',
    'browser_icon' => 'bi-globe',
    'full' => 'N/A',
    'is_bot' => false,
    'is_cli' => false,
]
```

---

## 📊 Matriz de Reconhecimento e Ícones

### Sistemas Operacionais Suportados

| Sistema Operacional | Detecção | Ícone Bootstrap | Flag `is_bot` | Flag `is_cli` |
| :--- | :--- | :--- | :---: | :---: |
| **Windows 10 / 11** | `Windows NT 10.0` | `bi-windows` | `false` | `false` |
| **Windows 8.1 / 8 / 7** | `Windows NT 6.3 / 6.2 / 6.1` | `bi-windows` | `false` | `false` |
| **Apple macOS** | `Macintosh`, `Mac OS X`, `macOS` | `bi-apple` | `false` | `false` |
| **Apple iOS (iPhone / iPod)** | `iPhone`, `iPod` | `bi-apple` | `false` | `false` |
| **Apple iPadOS (iPad)** | `iPad` | `bi-apple` | `false` | `false` |
| **Google Android** | `Android [versão]` | `bi-android2` | `false` | `false` |
| **Ubuntu Linux** | `Ubuntu` | `bi-ubuntu` | `false` | `false` |
| **Linux Genérico** | `Linux`, `X11` | `bi-tux` | `false` | `false` |
| **ChromeOS** | `CrOS` | `bi-laptop` | `false` | `false` |
| **BSD (FreeBSD, OpenBSD)** | `FreeBSD`, `OpenBSD`, `NetBSD` | `bi-pc` | `false` | `false` |
| **Clientes API / CLI** | `curl`, `Postman`, `Guzzle`, `python` | `bi-terminal` | `false` | `true` |
| **Robôs e Rastreadores** | `Googlebot`, `bingbot`, `bot`, `spider` | `bi-robot` | `true` | `false` |

---

### Navegadores e Clientes Suportados

| Navegador / Cliente | Extração de Versão | Ícone Bootstrap |
| :--- | :--- | :--- |
| **Google Chrome / Chromium** | `Chrome/124.0.0.0` -> `Google Chrome 124` | `bi-browser-chrome` |
| **Mozilla Firefox** | `Firefox/125.0` -> `Mozilla Firefox 125` | `bi-browser-firefox` |
| **Microsoft Edge** | `Edg/123.0` -> `Microsoft Edge 123` | `bi-browser-edge` |
| **Apple Safari** | `Version/17.4 Safari` -> `Apple Safari 17` | `bi-browser-safari` |
| **Opera / Opera GX** | `OPR/109.0` -> `Opera 109` | `bi-globe` |
| **Brave** | `Brave/1.65` -> `Brave 1` | `bi-browser-chrome` |
| **Vivaldi** | `Vivaldi/6.6` -> `Vivaldi 6` | `bi-globe` |
| **Samsung Internet** | `SamsungBrowser/24.0` -> `Samsung Internet 24` | `bi-browser-chrome` |
| **Internet Explorer** | `MSIE 11.0` ou `Trident/7.0` | `bi-browser-edge` |
| **Ferramentas CLI (cURL, Postman)** | `PostmanRuntime`, `curl`, `GuzzleHttp`, etc. | `bi-terminal` |
| **Bots e Crawlers** | `Googlebot`, `bingbot`, `Baiduspider`, etc. | `bi-robot` |

> **Nota de Precedência:** A análise de navegadores segue uma ordem estrita de precedência (Edge, Opera, Vivaldi, Brave, Samsung Internet, Firefox, Chrome e por último Safari), evitando que navegadores baseados em Chromium ou WebKit sejam identificados genericamente como Chrome ou Safari.

---

## 🧪 Exemplos Práticos de Entrada e Saída

### Exemplo 1: Google Chrome no Windows 11
```php
$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

$info = UserAgent::parse($ua);
/*
[
    'os' => 'Windows 10/11',
    'os_icon' => 'bi-windows',
    'browser' => 'Google Chrome 124',
    'browser_icon' => 'bi-browser-chrome',
    'full' => 'Mozilla/5.0 ...',
    'is_bot' => false,
    'is_cli' => false,
]
*/
```

### Exemplo 2: Safari no iPhone com iOS
```php
$ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1';

$info = UserAgent::parse($ua);
/*
[
    'os' => 'iOS (iPhone)',
    'os_icon' => 'bi-apple',
    'browser' => 'Apple Safari 17',
    'browser_icon' => 'bi-browser-safari',
    'full' => 'Mozilla/5.0 (iPhone ...',
    'is_bot' => false,
    'is_cli' => false,
]
*/
```

### Exemplo 3: Chamada via cURL ou Postman
```php
$ua = 'PostmanRuntime/7.37.3';

$info = UserAgent::parse($ua);
/*
[
    'os' => 'Cliente API / CLI (PostmanRuntime)',
    'os_icon' => 'bi-terminal',
    'browser' => 'PostmanRuntime',
    'browser_icon' => 'bi-terminal',
    'full' => 'PostmanRuntime/7.37.3',
    'is_bot' => false,
    'is_cli' => true,
]
*/
```

### Exemplo 4: Googlebot Crawler
```php
$ua = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

$info = UserAgent::parse($ua);
/*
[
    'os' => 'Bot / Rastreador (Googlebot)',
    'os_icon' => 'bi-robot',
    'browser' => 'Bot (Googlebot)',
    'browser_icon' => 'bi-robot',
    'full' => 'Mozilla/5.0 (compatible; Googlebot ...',
    'is_bot' => true,
    'is_cli' => false,
]
*/
```

---

## 🎨 Renderização em Blade com Bootstrap Icons

Renderizar o ícone e o nome do dispositivo em tabelas de auditoria ou logs de acesso torna-se trivial:

```blade
@php($dispositivo = \Softtan\Toolbox\Helpers\UserAgent::parse($log->user_agent))

<div class="d-flex align-items-center gap-2">
    <i class="bi {{ $dispositivo['os_icon'] }}" title="{{ $dispositivo['os'] }}"></i>
    <span>{{ $dispositivo['os'] }}</span>
    <span class="text-muted">/</span>
    <i class="bi {{ $dispositivo['browser_icon'] }}" title="{{ $dispositivo['browser'] }}"></i>
    <span>{{ $dispositivo['browser'] }}</span>
    
    @if($dispositivo['is_bot'])
        <span class="badge bg-warning text-dark">Bot</span>
    @elseif($dispositivo['is_cli'])
        <span class="badge bg-info text-dark">API</span>
    @endif
</div>
```
