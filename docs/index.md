# Documentação Técnica: Softtan Toolbox

**Softtan Toolbox** (`softtan/toolbox`) é uma biblioteca moderna e de alto desempenho desenvolvida para o ecossistema PHP 8.5+ e Laravel, fornecendo utilitários desacoplados, helpers fortemente tipados, validações de documentos brasileiros, controle de contexto multi-tenant e componentes resilientes de infraestrutura.

---

## 🏛️ Visão Geral da Arquitetura

A biblioteca foi concebida seguindo as melhores práticas modernas do ecossistema PHP e da engenharia de software:

1. **Tipagem Estrita e PHP 8.5+**: Todos os componentes utilizam `declare(strict_types=1);`, aproveitando recursos modernos do PHP como tipos estritos, união/interseção de tipos e constantes tipadas.
2. **Métodos Estáticos Puros**: Utilitários projetados para chamadas estáticas sem overhead de instanciação, permitindo código limpo e direto (`Format::cnpj(...)`, `Validate::cpf(...)`).
3. **Padrão de Nomenclatura Limpo e Direto**: Remoção do sufixo prolixo `Support` presente em implementações legadas. As classes agora possuem nomes objetivos como `Format`, `Validate`, `UserAgent` e `DateTime`.
4. **Convenção `Arr` e `Str`**: Como `array` e `string` são palavras reservadas no PHP, adotou-se a convenção padrão da comunidade (`Arr` e `Str`), garantindo clareza e familiaridade imediata.
5. **Suporte Completo a Unicode / UTF-8**: Manipulação de strings usando `mbstring`, garantindo suporte nativo a acentuação e caracteres internacionais.

---

## 📦 Namespaces e Organização

| Camada | Namespace | Descrição |
| :--- | :--- | :--- |
| **Namespace Raiz** | `Softtan\Toolbox\` | Raiz do pacote registrado no PSR-4 do Composer |
| **Helpers & Utilitários** | `Softtan\Toolbox\Helpers\` | Classes utilitárias estáticas e helpers |

---

## 📚 Índice da Documentação

Navegue pelos guias temáticos detalhados abaixo:

1. [**Formatação e Sanitização (`Format`)**](format.md)
   - Formatação e remoção de máscaras de CNPJ, telefones fixos e celulares, CEP brasileiro.
   - Conversão de bytes em notação legível (B, KB, MB, GB, TB).
   - Manipulação de moeda brasileira (BRL) e persistência em banco de dados.
   - Higienização inteligente de logradouros e extração de dígitos numéricos.

2. [**Validações Brasileiras (`Validate`)**](validate.md)
   - Validação algorítmica de CPF (módulo 11 com eliminação de sequências repetidas).
   - Validação algorítmica de CNPJ (pesos oficiais da Receita Federal).
   - Validação estrita e normalização de Unidades Federativas (todas as 27 UFs do Brasil).

3. [**Manipulação de Strings e Arrays (`Str` & `Arr`)**](str-arr.md)
   - Por que `Str` e `Arr` (palavras reservadas no PHP).
   - Filtragem de arrays vazios e lista branca de chaves (`Arr`).
   - Capitalização inteligente de nomes próprios (`Str::nomeProprio`) respeitando siglas (S.A., S/A, LTDA), numerais romanos (I, II, IV) e conectivos.
   - Slugs amigáveis, truncamento UTF-8, remoção de acentos via transliteração e geração de localizadores alfanuméricos seguros.

4. [**Serialização JSON (`Json`)**](json.md)
   - `Json::encode` com preservação nativa de Unicode e formatação legível (`PRETTY_PRINT`).
   - `Json::decode` seguro com conversão direta para arrays associativos e tratamento de nulos.

5. [**Detecção de Dispositivos e Navegadores (`UserAgent`)**](user-agent.md)
   - Parsing de strings User-Agent HTTP.
   - Identificação de Sistemas Operacionais (Windows, macOS, iOS, Android, Linux, distros).
   - Identificação de Navegadores (Chrome, Firefox, Safari, Edge, Opera, Brave, Vivaldi, etc.).
   - Detecção de robôs/crawlers e ferramentas de CLI/API (cURL, Postman, Guzzle).
   - Mapeamento automático para ícones do **Bootstrap Icons**.

6. [**Data e Hora (`DateTime`)**](datetime.md)
   - Formatação padronizada para o padrão brasileiro (`d/m/Y` e `d/m/Y H:i`).
   - Suporte transparente para strings ISO, instâncias do `Carbon` e objetos `DateTimeInterface`.

7. [**Utilitários Web e de Navegação (`Api`, `View`, `System`, `Notification`, `Menu`)**](web-helpers.md)
   - Respostas de API padronizadas com payloads estruturados (`Api`).
   - Configurações dinâmicas de paginação (`View`).
   - Inspeção de ambiente de execução e resolução de parâmetros de rota (`System`).
   - Notificações flash rápidas para toasts de interface (`Notification`).
   - Detecção inteligente de rotas ativas em menus de navegação com suporte a padrões CRUD (`.list`, `.index`) e atributos de acessibilidade ARIA (`Menu`).

8. [**Formulários e Imagens (`Form`, `Image`)**](security-forms.md)
   - Preenchimento dinâmico de formulários (`Form::value`) e cascata de obrigatoriedade (`Form::isRequired` - View > FormRequest > Schema DDL do Banco).
   - Módulo `Image` reservado para utilidades visuais.

9. [**Publicação e Tags Git (`SemVer`)**](git-tags.md)
    - Criação de tags anotadas, versionamento semântico e envio para repositório remoto para consumo no Composer.

---

## 🧪 Suíte de Testes

A biblioteca acompanha uma suíte de testes automatizados abrangente cobrindo todos os métodos utilitários, cálculos de dígitos verificadores e casos de borda:

```bash
php tests/run.php
```

---

## 📄 Licença

Distribuído sob a licença **MIT**. Consulte o arquivo de licença para mais informações.
