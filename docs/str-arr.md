# Manipulação de Strings e Arrays (`Str` & `Arr`)

Os helpers `Str` e `Arr` reúnem utilitários essenciais para transformação de texto, geração de códigos aleatórios legíveis, saneamento de coleções e formatação de nomes próprios brasileiros.

- **Classes:** `Softtan\Toolbox\Helpers\Str` e `Softtan\Toolbox\Helpers\Arr`
- **Substituem:** `StringSupport` e `ArraySupport`

---

## ⚠️ Convenção de Nomenclatura: Por que `Str` e `Arr`?

Na linguagem PHP, as palavras **`string`** e **`array`** são palavras-chave reservadas da linguagem para declaração de tipos escalares e primitivos. Criar classes chamadas `String` ou `Array` é expressamente proibido pela gramática do compilador PHP (`Fatal error: Cannot use 'String' as class name as it is reserved`).

Portanto, em conformidade com as melhores práticas da comunidade PHP e do ecossistema Laravel, adotamos os nomes canônicos:
- **`Softtan\Toolbox\Helpers\Str`** para manipulação de strings.
- **`Softtan\Toolbox\Helpers\Arr`** para manipulação de arrays.

---

## 🗂️ Helper `Arr`

```php
use Softtan\Toolbox\Helpers\Arr;
```

### `Arr::valoresNaoVazios`

Filtra um array de elementos removendo todos os valores considerados vazios pelo PHP através de `!empty($valor)`.

```php
public static function valoresNaoVazios(array $array): array
```

#### Parâmetros
- `array $array`: Array de origem a ser saneado.

#### Retorno
- `array`: Novo array contendo somente os elementos com valores não vazios.

#### Exemplos

```php
$dados = [
    'nome' => 'Maria Silva',
    'telefone' => '',
    'complemento' => null,
    'tags' => [],
    'ativo' => true,
];

$resultado = Arr::valoresNaoVazios($dados);
/*
[
    'nome' => 'Maria Silva',
    'ativo' => true,
]
*/
```

---

### `Arr::apenasChaves`

Filtra um array associativo retornando apenas as chaves explicitamente especificadas em uma lista branca (whitelist).

```php
public static function apenasChaves(array $array, array $chaves): array
```

#### Parâmetros
- `array $array`: Array associativo original.
- `array $chaves`: Lista de chaves permitidas no array de saída.

#### Retorno
- `array`: Array contendo apenas as chaves permitidas.

#### Exemplos

```php
$payload = [
    'id' => 42,
    'nome' => 'João Souza',
    'senha_hash' => 'secret_123',
    'email' => 'joao@exemplo.com',
    'admin' => true,
];

$publico = Arr::apenasChaves($payload, ['id', 'nome', 'email']);
/*
[
    'id' => 42,
    'nome' => 'João Souza',
    'email' => 'joao@exemplo.com',
]
*/
```

---

## 🔤 Helper `Str`

```php
use Softtan\Toolbox\Helpers\Str;
```

### `Str::nomeProprio`

Formata nomes próprios ou razões sociais no padrão **Title Case**, preservando regras ortográficas e mercadológicas avançadas:
1. **Conectivos em minúsculo:** Mantém preposições e conjunções (`de`, `da`, `das`, `do`, `dos`, `e`, `ou`, `com`) em minúsculo, exceto na primeira palavra.
2. **Siglas e Instituições Financeiras:** Preserva siglas bancárias conhecidas (`BB`, `BMG`, `BRB`, `BTG`, `BV`, `XP`, `UBS`, etc.) e permite passar siglas customizadas adicionais.
3. **Abreviações societárias:** Normaliza abreviações societárias como `S.A.`, `S/A`, `DTVM`, `LTDA`.
4. **Numerais Romanos:** Detecta e mantém numerais romanos em maiúsculo (`I`, `II`, `III`, `IV`, `V`, `VI`, `IX`, `X`, `XIV`, `XXI`).
5. **Apóstrofos:** Capitaliza corretamente após apóstrofos (ex: `D'Ávila`, `Sant'Ana`).

```php
public static function nomeProprio(?string $nome, array $palavrasMaiusculas = []): string
```

#### Parâmetros
- `?string $nome`: Nome a ser capitalizado.
- `array $palavrasMaiusculas`: Lista adicional de palavras ou siglas para manter em maiúsculo.

#### Retorno
- `string`: Nome formatado.

#### Exemplos

```php
// Conectivos em minúsculo
Str::nomeProprio('PEDRO DE ALCANTARA E SILVA');
// Resultado: "Pedro de Alcantara e Silva"

// Numerais romanos
Str::nomeProprio('papa joao paulo ii');
// Resultado: "Papa Joao Paulo II"

Str::nomeProprio('pedro iv de alcantara');
// Resultado: "Pedro IV de Alcantara"

// Siglas corporativas e S.A.
Str::nomeProprio('banco do brasil s.a.');
// Resultado: "Banco do Brasil S.A."

Str::nomeProprio('xp investimentos cctvm s/a');
// Resultado: "XP Investimentos CCTVM S/A"

// Apóstrofos
Str::nomeProprio("marcos d'avila silva");
// Resultado: "Marcos D'Ávila Silva"

// Siglas personalizadas adicionais
Str::nomeProprio('associacao dos servidores da ufmg', ['UFMG']);
// Resultado: "Associacao dos Servidores da UFMG"
```

---

### `Str::truncar`

Trunca um texto com segurança multibyte UTF-8 até o limite de caracteres estipulado, adicionando um sufixo indicador.

```php
public static function truncar(?string $texto, int $limite = 50, string $sufixo = '...'): ?string
```

#### Parâmetros
- `?string $texto`: Texto de entrada.
- `int $limite`: Comprimento máximo antes do corte (padrão: `50`).
- `string $sufixo`: Indicador de corte adicionado ao final (padrão: `'...'`).

#### Retorno
- `?string`: Texto truncado ou `null` se a entrada for vazia.

#### Exemplos

```php
Str::truncar('Relatório detalhado sobre as despesas da instituição no terceiro trimestre', 25);
// Resultado: "Relatório detalhado sobre..."

Str::truncar('Curto', 25);
// Resultado: "Curto"

Str::truncar(null);
// Resultado: null
```

---

### `Str::slug`

Converte qualquer texto para formato *slug* seguro para URLs (URL-friendly), separando palavras por traços (`-`).

```php
public static function slug(?string $texto): ?string
```

#### Parâmetros
- `?string $texto`: Texto original.

#### Retorno
- `?string`: String em minúsculas sem acentos separada por hífens, ou `null` se vazio.

#### Exemplos

```php
Str::slug('Aviso de Licitação & Contratos Públicos #2026');
// Resultado: "aviso-de-licitacao-contratos-publicos-2026"
```

---

### `Str::removerAcentos`

Remove acentos e caracteres diacríticos através de transliteração `iconv`, tratando apóstrofos e normalizando espaçamentos.

```php
public static function removerAcentos(?string $texto, bool $substituirApostrofosPorEspaco = true): ?string
```

#### Parâmetros
- `?string $texto`: Texto a ser normalizado.
- `bool $substituirApostrofosPorEspaco`: Se `true`, substitui apóstrofos por espaço; se `false`, apenas os remove.

#### Retorno
- `?string`: Texto sem diacríticos ou `null` se a entrada for nula.

#### Exemplos

```php
Str::removerAcentos('Conceição da Aparecida - Ação & Informações');
// Resultado: "Conceicao da Aparecida - Acao & Informacoes"

Str::removerAcentos("D'Ávila", true);
// Resultado: "D Avila"
```

---

### `Str::gerarLocalizador`

Gera um código localizador criptograficamente seguro com comprimento customizado através de `random_int`.
Para evitar confusão visual na leitura humana, são eliminados caracteres ambíguos:
- Letras excluídas: `I`, `O`, `Q`
- Números excluídos: `0`, `1`

```php
public static function gerarLocalizador(int $tamanho = 6, bool $apenasLetras = true): string
```

#### Parâmetros
- `int $tamanho`: Quantidade de caracteres do código gerado (deve ser maior que zero).
- `bool $apenasLetras`: Se `true`, utiliza apenas letras maiúsculas; se `false`, mescla letras e dígitos numéricos.

#### Retorno
- `string`: Código aleatório legível (ex: `'KJHBNP'`, `'AB97TK'`).

#### Exceções
- Lança `\InvalidArgumentException` se `$tamanho <= 0`.

#### Exemplos

```php
// Localizador apenas letras (6 caracteres)
$loc = Str::gerarLocalizador(6, true);
// Exemplo: "AKMNPR"

// Localizador alfanumérico (8 caracteres)
$locAlfanum = Str::gerarLocalizador(8, false);
// Exemplo: "Y7HN43WP"
```

---

### `Str::nameRandom`

Gera um nome randômico alfanumérico seguro para atributos `id` ou `name` de elementos DOM HTML ou campos temporários.

```php
public static function nameRandom(int $size = 7, bool $lower = true): string
```

#### Parâmetros
- `int $size`: Quantidade de caracteres (padrão: `7`).
- `bool $lower`: Se `true`, converte o resultado para minúsculas (padrão: `true`).

#### Retorno
- `string`: Identificador randômico.

#### Exemplos

```php
Str::nameRandom(5);
// Exemplo: "x8k2m"
```

---

### `Str::humanize`

Converte termos técnicos em convenções como `camelCase` ou `kebab-case` para texto legível em **Title Case** para humanos.

```php
public static function humanize(string $text): string
```

#### Parâmetros
- `string $text`: Texto técnico (ex: `'usuarioNome'`, `'user-profile'`, `'codigoAcesso'`).

#### Retorno
- `string`: Texto formatado (ex: `'Usuario Nome'`, `'User Profile'`, `'Codigo Acesso'`).

#### Exemplos

```php
Str::humanize('dataNascimento'); // "Data Nascimento"
Str::humanize('cargo-atual');     // "Cargo Atual"
```

---

### `Str::hasValue`

Determina se uma variável possui conteúdo válido (não é nula e não é string vazia).
Diferente da função `empty()` do PHP, este método considera `'0'` e `0` como conteúdos válidos!

```php
public static function hasValue(mixed $value): bool
```

#### Parâmetros
- `mixed $value`: Valor a ser verificado.

#### Retorno
- `bool`: `true` se contiver valor/conteúdo válido, `false` se for nulo ou `""`.

#### Exemplos

```php
Str::hasValue('texto'); // true
Str::hasValue(0);       // true  (empty(0) seria false!)
Str::hasValue('0');     // true  (empty('0') seria false!)
Str::hasValue('');      // false
Str::hasValue(null);    // false
```
