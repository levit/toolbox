# 🧠 Contexto do Projeto e Diretrizes do Assistente (Gemini)

Este documento estabelece a identidade, padrões de engenharia, arquitetura e diretrizes operacionais para o desenvolvimento e evolução do projeto **Softtan Toolbox**. Qualquer interação com o assistente de IA deve obrigatoriamente balizar-se pelas regras contidas neste guia.

---

## 🏢 1. Identificação do Projeto

* **Empresa:** SoftTAN Sistemas
* **Autor:** Levi Tancredo (<levitancredo@gmail.com>)
* **Pacote Composer:** `softtan/toolbox`
* **Tipo:** Biblioteca (`library`) de utilitários, helpers de infraestrutura e componentes para o ecossistema PHP e Laravel.
* **Licença:** MIT

---

## 🛠️ 2. Stack Tecnológica e Requisitos

* **Linguagem:** **PHP 8.5+**
* **Tipagem:** Estrita (`declare(strict_types=1);` obrigatório em todos os arquivos).
* **Frameworks Suportados:** Laravel 11.x e 12.x (compatível também com aplicações PHP puras modernas).
* **Dependências de Produção:**
  * `illuminate/support` (^11.0|^12.0)
  * `illuminate/http` (^11.0|^12.0)
  * `illuminate/database` (^11.0|^12.0)
  * `nesbot/carbon` (^2.0|^3.0)
* **Extensões PHP Requeridas:** `mbstring`, `iconv`, `json`.
* **Ambiente de Testes:**
  * Suíte autônoma interna executável via `php tests/run.php` (sem dependências externas).
  * Compatível com PHPUnit 11/12 (`vendor/bin/phpunit`) e Orchestra Testbench.

---

## 🏛️ 3. Princípios Arquiteturais e Padrões de Código

1. **Tipagem Estrita e Recursos Modernos do PHP 8.5+**:
   * Todo arquivo PHP deve iniciar com `declare(strict_types=1);`.
   * Parâmetros e retornos devem ser tipados com precisão (tipos escalares, união `string|null`, interseção, tipos nulos explícitos).
   * Uso de recursos idiomáticos modernos (match expressions, named arguments, promoções de propriedades, constantes tipadas).

2. **Helpers Estáticos Puros e Desacoplados**:
   * Métodos utilitários devem ser estáticos e preferencialmente puros (sem efeitos colaterais indesejados).
   * Sem overhead de instanciação desnecessária: chamadas diretas como `Format::cnpj(...)`, `Validate::cpf(...)`.
   * Desacoplamento inteligente: serviços do Laravel (como sessão, rotas e autenticação) devem possuir fallback elegante ou verificação de existência para permitir execução limpa em ambientes de CLI ou fora do ciclo HTTP padrão.

3. **Nomenclatura Limpa e Consistente**:
   * **`Str` e `Arr`**: Adotadas no lugar de `String` e `Array` devido a palavras reservadas do PHP.
   * Nomes de classes diretos e sem sufixos redundantes (`Format` em vez de `FormatSupport`, `Validate` em vez de `ValidateSupport`).
   * Padrão PSR-4: Namespace raiz `Softtan\Toolbox\`, helpers em `Softtan\Toolbox\Helpers\`.

4. **Suporte Nativo a Unicode (UTF-8)**:
   * Toda manipulação de strings deve utilizar funções multibyte (`mb_*`) ou `iconv` para suporte impecável a acentos e caracteres internacionais.

5. **Padrões e Regras Brasileiras (BR)**:
   * Validação algorítmica rigorosa de CPF e CNPJ (módulo 11 com eliminação de sequências inválidas).
   * Formatação e remoção de máscaras para CNPJ, CEP, Telefones (fixo e 9 dígitos).
   * Moeda brasileira (`R$ 1.500,50` ⇄ `1500.50`).
   * Datas no padrão brasileiro (`d/m/Y` e `d/m/Y H:i`).
   * Validação estrita das 27 Unidades Federativas (UFs).

---

## 📦 4. Versionamento, Publicação e Composer

1. **Omissão da Chave `version` no `composer.json`**:
   * O `composer.json` **não deve conter** o campo `"version"`.
   * O Composer e o Packagist inferem as versões dinamicamente a partir das **Tags do Git**.

2. **Versionamento Semântico (SemVer)**:
   * **PATCH (`v1.0.1`)**: Correções de bugs retrocompatíveis.
   * **MINOR (`v1.1.0`)**: Novas funcionalidades que não quebram código existente.
   * **MAJOR (`v2.0.0`)**: Alterações que quebram retrocompatibilidade (*breaking changes*).

3. **Publicação via Git Tags**:
   * Sempre criar tags anotadas: `git tag -a v1.0.0 -m "Release v1.0.0: ..."`
   * Envio para repositório remoto: `git push origin v1.0.0` (ou `git push origin --tags`).

4. **Distribuição Limpa (`.gitattributes`)**:
   * O arquivo `.gitattributes` contém diretivas `export-ignore` para impedir que arquivos de teste (`/tests`), arquivos de CI (`/.github`), documentação interna (`/docs`, `/md`) e configurações locais de desenvolvimento poluam o diretório `vendor/` dos projetos que consom a biblioteca.

---

## 🧪 5. Diretrizes para Testes de Unidade

* **Regra de Ouro:** Nenhuma nova funcionalidade ou refatoração deve ser dada como concluída sem que a suíte de testes seja executada e apresente **100% de sucesso**.
* **Comando padrão de execução:**
  ```bash
  php tests/run.php
  ```
* Todo novo método utilitário criado em `src/Helpers/` deve ter seu respectivo caso de teste adicionado em `tests/*Test.php`.
* Os testes devem cobrir fluxos de sucesso, entradas nulas/vazias e casos de borda/exceções.

---

## 📂 6. Estrutura de Diretórios

```text
toolbox/
├── .gitattributes          # Exclusão de arquivos de desenvolvimento na distribuição
├── composer.json           # Definição do pacote, dependências e PSR-4
├── phpunit.xml.dist        # Configuração oficial do PHPUnit
├── README.md               # Apresentação do pacote e quickstart
├── gemini.md               # Este arquivo: diretrizes e contexto do projeto
├── docs/                   # Documentação técnica em Markdown dos helpers
├── md/                     # Guias específicos de publicação e tags
├── src/
│   └── Helpers/            # Classes utilitárias do pacote (Format, Validate, Str, etc.)
└── tests/
    ├── run.php             # Runner autônomo de testes (PHP puro, saída colorida)
    ├── bootstrap.php       # Mock/Emulação de serviços do Laravel para testes isolados
    ├── TestCase.php        # Classe base de testes com asserções estritas
    └── *Test.php           # Arquivos de testes de cada componente
```

---

## 🤖 7. Comportamento Esperado do Assistente de IA

1. **Idioma de Interação:** Respostas, explicações e comentários em **Português (Brasil)**.
2. **Qualidade do Código:** Códigos fornecidos devem seguir estritamente o PHP 8.5+, com tipagem completa e sem supressão cega de erros (`@`).
3. **Verificação Automatizada:** Sempre que modificar arquivos do projeto em `src/` ou `tests/`, execute o comando `php tests/run.php` para validar se nenhuma quebra foi introduzida.
4. **Respeito à Arquitetura Existente:** Seguir as convenções já estabelecidas nas classes de `src/Helpers/` e a organização da documentação em `docs/`.
