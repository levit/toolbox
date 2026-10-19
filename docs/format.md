# Formatação e Sanitização (`Format`)

O helper `Format` centraliza operações de formatação, aplicação de máscaras, conversão de unidades e sanitização de dados com foco em documentos brasileiros e padrões de dados comuns.

- **Classe:** `Softtan\Toolbox\Helpers\Format`
- **Substitui:** `FormatSupport`

```php
use Softtan\Toolbox\Helpers\Format;
```

---

## 📌 Sumário de Métodos

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| [`Format::cnpj(?string $cnpj)`](#formatcnpj) | `?string` | Aplica máscara em CNPJ (`00.000.000/0000-00`). |
| [`Format::telefone(?string $telefone)`](#formattelefone) | `?string` | Aplica máscara em telefone fixo (10 dígitos) ou celular (11 dígitos). |
| [`Format::bytes(int $bytes, int $precision = 2)`](#formatbytes) | `string` | Converte bytes para formato legível (`B`, `KB`, `MB`, `GB`, `TB`). |
| [`Format::moeda(mixed $valor, bool $simbolo = true)`](#formatmoeda) | `?string` | Formata valores numéricos para Real brasileiro (`R$ 1.500,50`). |
| [`Format::converterMoedaBd(?string $valor)`](#formatconvertermoedabd) | `?float` | Converte string de moeda (`R$ 1.500,50`) para `float` SQL (`1500.50`). |
| [`Format::cep(string $cep)`](#formatcep) | `string` | Formata string numérica como CEP (`00000-000`). |
| [`Format::limparLogradouro(string $logradouro, bool $manterOriginalSeVazio = true)`](#formatlimparlogradouro) | `string` | Remove anotações complementares secundárias de endereços (`s/n`, `lado ímpar`, etc.). |
| [`Format::somenteNumeros(string $str)`](#formatsomentenumeros) | `string` | Remove todos os caracteres não numéricos de uma cadeia de texto. |

---

## 📖 Referência Detalhada dos Métodos

### `Format::cnpj`

Aplica a máscara padrão de CNPJ (`00.000.000/0000-00`) sobre uma sequência numérica.

```php
public static function cnpj(?string $cnpj): ?string
```

#### Parâmetros
- `?string $cnpj`: Cadeia de caracteres com ou sem pontuação.

#### Retorno
- `?string`: Retorna o CNPJ formatado se possuir exatamente 14 dígitos; retorna o valor original limpo se tiver comprimento diferente de 14; retorna `null` se a entrada for vazia ou nula.

#### Exemplos

```php
Format::cnpj('11222333000181');
// Resultado: "11.222.333/0001-81"

Format::cnpj('11.222.333/0001-81');
// Resultado: "11.222.333/0001-81"

Format::cnpj('123'); // Quantidade inválida de dígitos
// Resultado: "123"

Format::cnpj(null);
// Resultado: null
```

---

### `Format::telefone`

Aplica a máscara adequada para telefones fixos (10 dígitos) ou celulares (11 dígitos com o nono dígito).

```php
public static function telefone(?string $telefone): ?string
```

#### Parâmetros
- `?string $telefone`: Sequência com o DDD e número de telefone.

#### Retorno
- `?string`:
  - 10 dígitos: `(00) 0000-0000`
  - 11 dígitos: `(00) 00000-0000`
  - Outros tamanhos: Devolve os dígitos numéricos sem alteração.
  - Vazio ou nulo: Retorna `null`.

#### Exemplos

```php
// Celular (11 dígitos)
Format::telefone('11999998888');
// Resultado: "(11) 99999-8888"

// Telefone Fixo (10 dígitos)
Format::telefone('1133334444');
// Resultado: "(11) 3333-4444"

// String já formatada
Format::telefone('(11) 99999-8888');
// Resultado: "(11) 99999-8888"

Format::telefone(null);
// Resultado: null
```

---

### `Format::bytes`

Converte uma quantidade inteira de bytes em representação humana arredondada com unidade binária correspondente (`B`, `KB`, `MB`, `GB`, `TB`).

```php
public static function bytes(int $bytes, int $precision = 2): string
```

#### Parâmetros
- `int $bytes`: Quantidade inteira de bytes (valores negativos são tratados como `0`).
- `int $precision`: Casas decimais para arredondamento (padrão: `2`).

#### Retorno
- `string`: String formatada com a unidade apropriada.

#### Exemplos

```php
Format::bytes(500);
// Resultado: "500 B"

Format::bytes(1024);
// Resultado: "1 KB"

Format::bytes(1048576);
// Resultado: "1 MB"

Format::bytes(1073741824);
// Resultado: "1 GB"

Format::bytes(1536000, 1);
// Resultado: "1.5 MB"
```

---

### `Format::moeda`

Converte um número (`int`, `float` ou string numérica) para o padrão monetário do Real Brasileiro (BRL), separando milhares com ponto e decimais com vírgula.

```php
public static function moeda(mixed $valor, bool $simbolo = true): ?string
```

#### Parâmetros
- `mixed $valor`: Valor numérico a ser formatado.
- `bool $simbolo`: Se `true`, adiciona o prefixo `'R$ '` (padrão: `true`).

#### Retorno
- `?string`: Valor monetário formatado ou `null` caso `$valor === null`.

#### Exemplos

```php
Format::moeda(1250.5);
// Resultado: "R$ 1.250,50"

Format::moeda(1250.5, false);
// Resultado: "1.250,50"

Format::moeda('0');
// Resultado: "R$ 0,00"

Format::moeda(null);
// Resultado: null
```

---

### `Format::converterMoedaBd`

Converte uma string formatada em moeda brasileira para um valor de ponto flutuante (`float`) pronto para ser persistido em colunas `DECIMAL` do banco de dados.

```php
public static function converterMoedaBd(?string $valor): ?float
```

#### Parâmetros
- `?string $valor`: String monetária (ex: `'R$ 1.500,50'`, `'1.500,50'`, `'250,00'`).

#### Retorno
- `?float`: Valor em `float` com ponto como separador decimal, ou `null` se vazio.

#### Exemplos

```php
Format::converterMoedaBd('R$ 1.500,50');
// Resultado: 1500.5

Format::converterMoedaBd('1.250.340,99');
// Resultado: 1250340.99

Format::converterMoedaBd('');
// Resultado: null
```

---

### `Format::cep`

Aplica a máscara padrão de CEP (`00000-000`) a uma cadeia de 8 dígitos numéricos.

```php
public static function cep(string $cep): string
```

#### Parâmetros
- `string $cep`: CEP de 8 dígitos contínuos (`"01001000"`) ou já mascarado (`"01001-000"`).

#### Retorno
- `string`: CEP formatado com traço (`00000-000`).

#### Exceções
- Lança `\InvalidArgumentException` se o CEP não possuir 8 dígitos numéricos ou o formato mascarado `00000-000`.

#### Exemplos

```php
Format::cep('01001000');
// Resultado: "01001-000"

Format::cep('01001-000');
// Resultado: "01001-000"

Format::cep('123');
// Lança InvalidArgumentException: "CEP inválido: '123'. Esperado '00000000' ou '00000-000'."
```

---

### `Format::limparLogradouro`

Remove sufixos e complementos cadastrais comuns de logradouros (como `s/n`, `lado ímpar`, `até 100`, `de 10 a 50`), normalizando o nome da via para consultas em serviços de CEP (Correios) ou geocodificação.

```php
public static function limparLogradouro(string $logradouro, bool $manterOriginalSeVazio = true): string
```

#### Parâmetros
- `string $logradouro`: Nome completo ou linha de endereço do logradouro.
- `bool $manterOriginalSeVazio`: Se `true`, devolve o texto original caso a higienização resulte em string vazia.

#### Retorno
- `string`: Logradouro limpo e desprovido de anotações secundárias.

#### Exemplos

```php
Format::limparLogradouro('Avenida Paulista, s/n');
// Resultado: "Avenida Paulista"

Format::limparLogradouro('Rua das Flores - lado ímpar');
// Resultado: "Rua das Flores"

Format::limparLogradouro('Rua XV de Novembro até 500');
// Resultado: "Rua XV de Novembro"

Format::limparLogradouro('Rua do Comércio de 1 a 100');
// Resultado: "Rua do Comércio"
```

---

### `Format::somenteNumeros`

Sanitiza qualquer string mantendo exclusivamente os caracteres numéricos (`0-9`).

```php
public static function somenteNumeros(string $str): string
```

#### Parâmetros
- `string $str`: Texto de entrada com dígitos, caracteres especiais ou letras.

#### Retorno
- `string`: String contendo apenas os números contidos na entrada.

#### Exemplos

```php
Format::somenteNumeros('CPF: 123.456.789-00');
// Resultado: "12345678900"

Format::somenteNumeros('Telefone: (11) 98765-4321');
// Resultado: "11987654321"

Format::somenteNumeros('Sem números aqui');
// Resultado: ""
```
