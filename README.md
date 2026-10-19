# 🧰 Softtan Toolbox

[![PHP Version](https://img.shields.io/badge/PHP-%5E8.5-777bb4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Package Version](https://img.shields.io/badge/composer-softtan%2Ftoolbox-informational.svg?style=flat-square&logo=composer)](https://packagist.org/)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg?style=flat-square)](tests/run.php)

**Softtan Toolbox** é uma suíte abrangente de utilitários, helpers fortemente tipados e componentes de infraestrutura de alto desempenho para aplicações modernas em **PHP 8.5+** e **Laravel**.

Projetada para elevar a produtividade, a biblioteca centraliza formatações e validações do padrão brasileiro, saneamento de dados, inteligência de formulários, detecção avançada de clientes HTTP e governança para ambientes SaaS multi-tenant.

---

## 📋 Requisitos

- **PHP:** `^8.5`
- **Extensões PHP:** `ext-mbstring`, `ext-iconv`, `ext-json`
- **Framework:** Compatível com Laravel 11+ / 12+ ou aplicações PHP modernas

---

## 📥 Instalação

Adicione o pacote ao seu projeto via [Composer](https://getcomposer.org/):

```bash
composer require softtan/toolbox
```

---

## ⚡ Guia Rápido de Uso (Quickstart)

Abaixo estão exemplos rápidos dos helpers mais utilizados no dia a dia:

### 1. Formatações e Máscaras (`Format`)

```php
use Softtan\Toolbox\Helpers\Format;

// Documentos e Contatos
echo Format::cnpj('11222333000181');       // "11.222.333/0001-81"
echo Format::telefone('11999998888');       // "(11) 99999-8888"
echo Format::cep('01001000');              // "01001-000"

// Moeda e Bytes
echo Format::moeda(1500.50);               // "R$ 1.500,50"
echo Format::converterMoedaBd('R$ 1.500,50'); // 1500.5 (float)
echo Format::bytes(1048576);               // "1 MB"

// Sanitização
echo Format::somenteNumeros('CPF: 123.456.789-00'); // "12345678900"
echo Format::limparLogradouro('Av. Paulista, s/n'); // "Av. Paulista"
```

### 2. Validações Algorítmicas (`Validate`)

```php
use Softtan\Toolbox\Helpers\Validate;

// Validação matemática com cálculo de dígitos verificadores
Validate::cpf('529.982.247-25'); // true
Validate::cpf('111.111.111-11'); // false (sequências repetidas rejeitadas)

Validate::cnpj('11.222.333/0001-81'); // true
Validate::cnpj('11.222.333/0001-99'); // false

// Validação e normalização de Unidade Federativa
echo Validate::uf(' sp '); // "SP"
// Validate::uf('XX');     // Lança \InvalidArgumentException
```

### 3. Manipulação Inteligente de Strings (`Str`)

```php
use Softtan\Toolbox\Helpers\Str;

// Capitalização Title Case preservando regras brasileiras, siglas e numerais romanos
echo Str::nomeProprio('banco do brasil s.a.'); 
// "Banco do Brasil S.A."

echo Str::nomeProprio('xp investimentos cctvm s/a'); 
// "XP Investimentos CCTVM S/A"

echo Str::nomeProprio('papa joao paulo ii'); 
// "Papa Joao Paulo II"

echo Str::nomeProprio("marcos d'avila silva"); 
// "Marcos D'Ávila Silva"

// Gerador de códigos alfanuméricos seguros (sem caracteres ambíguos: sem I, O, Q, 0, 1)
echo Str::gerarLocalizador(6, true);  // Ex: "AKMNPR"
echo Str::gerarLocalizador(8, false); // Ex: "Y7HN43WP"

// Slugs amigáveis e truncamento com segurança UTF-8
echo Str::slug('Licitação & Contratos Públicos #2026'); // "licitacao-contratos-publicos-2026"
echo Str::truncar('Texto muito longo para visualização', 15); // "Texto muito lon..."
```

### 4. Saneamento de Coleções (`Arr`)

```php
use Softtan\Toolbox\Helpers\Arr;

// Filtragem de elementos vazios
$dados = ['nome' => 'Carlos', 'telefone' => '', 'cargo' => null, 'ativo' => true];
$limpo = Arr::valoresNaoVazios($dados);
// ['nome' => 'Carlos', 'ativo' => true]

// Lista branca (whitelist) de chaves
$payload = ['id' => 1, 'nome' => 'Ana', 'hash_senha' => 'xyz', 'admin' => true];
$publico = Arr::apenasChaves($payload, ['id', 'nome']);
// ['id' => 1, 'nome' => 'Ana']
```

### 5. Detecção de Dispositivos e Navegadores (`UserAgent`)

```php
use Softtan\Toolbox\Helpers\UserAgent;

$info = UserAgent::parse(request()->userAgent());

echo $info['os'];           // Ex: "Windows 10/11" ou "Ubuntu Linux"
echo $info['os_icon'];      // Ex: "bi-windows" ou "bi-ubuntu" (Bootstrap Icons)
echo $info['browser'];      // Ex: "Google Chrome 124"
echo $info['browser_icon']; // Ex: "bi-browser-chrome"
var_dump($info['is_bot']);  // false
var_dump($info['is_cli']);  // false
```

---

## 🏷️ Convenção de Nomenclatura

A arquitetura do pacote passou por uma importante padronização em sua versão moderna:

1. **Eliminação do Sufixo "Support":** Em versões anteriores, as classes utilizavam nomes prolixos como `FormatSupport` e `ValidateSupport`. O sufixo foi removido para proporcionar uma sintaxe limpa, expressiva e concisa (`Format::cnpj(...)`, `Validate::cpf(...)`).
2. **Uso de `Arr` e `Str` devido às Palavras Reservadas do PHP:** Na linguagem PHP, os identificadores `array` e `string` são **palavras-chave reservadas** pela gramática da linguagem para a declaração de tipos nativos. Criar classes chamadas `Array` ou `String` dispara erro fatal do compilador PHP (`Fatal error: Cannot use 'String' as class name as it is reserved`). Por esse motivo, adotamos a convenção universal da comunidade PHP: **`Arr`** e **`Str`**.

---

## 🗺️ Mapa de Componentes Disponíveis

Todos os helpers estão disponíveis sob o namespace **`Softtan\Toolbox\Helpers\`**:

| Componente | Nome Anterior | Namespace Completo | Descrição Principal | Documentação |
| :--- | :--- | :--- | :--- | :---: |
| **`Format`** | `FormatSupport` | `Softtan\Toolbox\Helpers\Format` | Formatação de CNPJ, telefones, CEP, moeda BRL e bytes. | [Ver Guia](docs/format.md) |
| **`Validate`** | `ValidateSupport` | `Softtan\Toolbox\Helpers\Validate` | Validação algorítmica de CPF, CNPJ e estados (UFs). | [Ver Guia](docs/validate.md) |
| **`Str`** | `StringSupport` | `Softtan\Toolbox\Helpers\Str` | Title Case inteligente, slugs, remoção de acentos e códigos seguros. | [Ver Guia](docs/str-arr.md) |
| **`Arr`** | `ArraySupport` | `Softtan\Toolbox\Helpers\Arr` | Filtragem de coleções e extração seletiva de chaves. | [Ver Guia](docs/str-arr.md) |
| **`Json`** | `JsonSupport` | `Softtan\Toolbox\Helpers\Json` | Serialização JSON com Unicode nativo e formatação limpa. | [Ver Guia](docs/json.md) |
| **`UserAgent`** | `UserAgentSupport` | `Softtan\Toolbox\Helpers\UserAgent` | Parsing de HTTP User-Agent, SOs, browsers e ícones Bootstrap. | [Ver Guia](docs/user-agent.md) |
| **`DateTime`** | `DateTimeSupport` | `Softtan\Toolbox\Helpers\DateTime` | Formatação de data e hora no padrão brasileiro (`d/m/Y`, `d/m/Y H:i`). | [Ver Guia](docs/datetime.md) |
| **`Api`** | `ApiSupport` | `Softtan\Toolbox\Helpers\Api` | Respostas JSON estruturadas para APIs RESTful. | [Ver Guia](docs/web-helpers.md) |
| **`Menu`** | `MenuSupport` | `Softtan\Toolbox\Helpers\Menu` | Reconhecimento inteligente de rotas ativas em CRUDs e ARIA. | [Ver Guia](docs/web-helpers.md) |
| **`Log`** | `LogSupport` | `Softtan\Toolbox\Helpers\Log` | Registro dinâmico de logs contextualizados no sistema. | [Ver Guia](docs/web-helpers.md) |
| **`View`** | `ViewSupport` | `Softtan\Toolbox\Helpers\View` | Gestão de parâmetros de visualização e itens por página. | [Ver Guia](docs/web-helpers.md) |
| **`Notification`** | `NotificationSupport` | `Softtan\Toolbox\Helpers\Notification` | Toasts e avisos rápidos na sessão flash da aplicação. | [Ver Guia](docs/web-helpers.md) |
| **`System`** | `SystemSupport` | `Softtan\Toolbox\Helpers\System` | Diagnóstico de ambiente (produção/local) e rotas ativas. | [Ver Guia](docs/web-helpers.md) |
| **`Form`** | `FormSupport` | `Softtan\Toolbox\Helpers\Form` | Preenchimento e cascata de obrigatoriedade (View > Request > DDL). | [Ver Guia](docs/security-forms.md) |
| **`Image`** | `ImageSupport` | `Softtan\Toolbox\Helpers\Image` | Utilitários e manipulação de arquivos de imagem. | [Ver Guia](docs/security-forms.md) |

---

## 📖 Documentação Completa

Para aprofundar-se em métodos, parâmetros e casos de uso específicos, consulte a documentação detalhada na pasta [`/docs`](docs/):

- 📚 [**Visão Geral e Arquitetura**](docs/index.md): Princípios de design, organização de namespaces e convenções.
- 📐 [**Formatação e Sanitização (`Format`)**](docs/format.md): CNPJ, telefones, CEP, BRL, bytes e limpeza de logradouros.
- 🛡️ [**Validações Brasileiras (`Validate`)**](docs/validate.md): Algoritmos oficiais de CPF, CNPJ e estados (UFs).
- 🔤 [**Strings e Arrays (`Str` & `Arr`)**](docs/str-arr.md): Title Case com preservação de siglas e conectivos, geração de códigos e filtros.
- 📦 [**Serialização JSON (`Json`)**](docs/json.md): Encode Unicode limpo e decodificação segura para arrays associativos.
- 🌐 [**Identificação de Dispositivos (`UserAgent`)**](docs/user-agent.md): Detecção de SOs, browsers, robôs e classes Bootstrap Icons.
- ⏰ [**Data e Hora (`DateTime`)**](docs/datetime.md): Padronização para datas e horas no padrão brasileiro (`d/m/Y`, `d/m/Y H:i`).
- 🧭 [**Utilitários Web e de Navegação**](docs/web-helpers.md): Helpers `Api`, `View`, `System`, `Notification`, `Menu` e `Log`.
- 📝 [**Formulários e Imagens**](docs/security-forms.md): `Form` e `Image`.

---

## 🧪 Executando os Testes

A biblioteca dispõe de uma suíte de testes de unidade automatizada compatível tanto com o executor autônomo (sem dependências externas) quanto com o PHPUnit oficial:

```bash
# Execução direta via runner autônomo (rápido e sem dependências)
php tests/run.php

# Ou via PHPUnit oficial (quando instalado via composer require --dev)
vendor/bin/phpunit
```

---

## 📜 Licença e Créditos

Este pacote é um software de código aberto licenciado sob a [Licença MIT](LICENSE).

- **Autor:** Levi Tancredo ([levitancredo@gmail.com](mailto:levitancredo@gmail.com))
- **Desenvolvido por:** [Softtan Tecnologia](https://softtan.com.br)
