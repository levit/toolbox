# Validações Brasileiras (`Validate`)

O helper `Validate` fornece validação algorítmica estrita para documentos e dados geográficos brasileiros, com verificação de dígitos verificadores e rejeição de sequências inválidas.

- **Classe:** `Softtan\Toolbox\Helpers\Validate`
- **Substitui:** `ValidateSupport`

```php
use Softtan\Toolbox\Helpers\Validate;
```

---

## 📌 Sumário de Métodos

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| [`Validate::cpf(?string $cpf)`](#validatecpf) | `bool` | Valida algoritmo e dígitos verificadores de CPF brasileiro. |
| [`Validate::cnpj(?string $cnpj)`](#validatecnpj) | `bool` | Valida algoritmo e dígitos verificadores de CNPJ brasileiro. |
| [`Validate::uf(?string $uf)`](#validateuf) | `string` | Valida e normaliza a sigla de uma Unidade Federativa (UF). |

---

## 📖 Referência Detalhada dos Métodos

### `Validate::cpf`

Valida se um número de CPF é válido de acordo com o cálculo matemático dos dois dígitos verificadores (Módulo 11).

- **Limpeza automática:** Remove pontuações e caracteres não numéricos antes de validar.
- **Rejeição de sequências:** Rejeita imediatamente combinações de dígitos idênticos repetidos (como `000.000.000-00`, `111.111.111-11`, etc.).
- **Tolerância a formatos:** Aceita strings com ou sem máscara.

```php
public static function cpf(?string $cpf): bool
```

#### Parâmetros
- `?string $cpf`: Número do CPF com ou sem máscara.

#### Retorno
- `bool`: Retorna `true` se o CPF for válido e seus dígitos verificadores forem matematicamente consistentes; `false` se for nulo, vazio, tiver comprimento inadequado ou dígitos inválidos.

#### Exemplos

```php
// CPF válido com pontuação
Validate::cpf('529.982.247-25'); // true

// CPF válido somente números
Validate::cpf('52998224725'); // true

// Sequência repetida de dígitos
Validate::cpf('111.111.111-11'); // false
Validate::cpf('000.000.000-00'); // false

// Dígitos verificadores incorretos
Validate::cpf('529.982.247-00'); // false

// Entradas vazias ou nulas
Validate::cpf('');   // false
Validate::cpf(null); // false
```

---

### `Validate::cnpj`

Valida se um número de CNPJ é autêntico de acordo com os critérios matemáticos da Receita Federal do Brasil, utilizando o cálculo dos pesos para o primeiro e segundo dígitos verificadores.

- **Limpeza automática:** Sanitiza a entrada removendo pontos, barras e traços.
- **Rejeição de sequências:** Rejeita sequências de 14 dígitos repetidos.
- **Tolerância a formatos:** Aceita com formatação (`00.000.000/0000-00`) ou apenas números.

```php
public static function cnpj(?string $cnpj): bool
```

#### Parâmetros
- `?string $cnpj`: Número do CNPJ com ou sem formatação.

#### Retorno
- `bool`: Retorna `true` se o CNPJ for matematicamente autêntico; `false` se for nulo, vazio, com tamanho divergente de 14 dígitos ou com dígitos verificadores incorretos.

#### Exemplos

```php
// CNPJ válido com pontuação
Validate::cnpj('11.222.333/0001-81'); // true

// CNPJ válido apenas números
Validate::cnpj('11222333000181'); // true

// Sequências inválidas
Validate::cnpj('00000000000000'); // false
Validate::cnpj('11111111111111'); // false

// Dígitos verificadores incorretos
Validate::cnpj('11.222.333/0001-99'); // false

// Nulo ou vazio
Validate::cnpj(null); // false
```

---

### `Validate::uf`

Valida se uma sigla corresponde a um dos 26 estados da federação brasileira ou ao Distrito Federal. Além da validação, o método normaliza o texto, removendo espaços e convertendo para maiúsculo.

```php
public static function uf(?string $uf): string
```

#### Lista de UFs Homologadas
`AC`, `AL`, `AP`, `AM`, `BA`, `CE`, `DF`, `ES`, `GO`, `MA`, `MT`, `MS`, `MG`, `PA`, `PB`, `PR`, `PE`, `PI`, `RJ`, `RN`, `RS`, `RO`, `RR`, `SC`, `SP`, `SE`, `TO`.

#### Parâmetros
- `?string $uf`: Sigla informada (ex: `'SP'`, `'rj'`, `' mg '`).

#### Retorno
- `string`: Sigla normalizada com exatamente 2 caracteres maiúsculos.

#### Exceções
- Lança `\InvalidArgumentException` se a entrada for vazia, nula ou se não corresponder a um estado brasileiro válido.

#### Exemplos

```php
// Normalização automática
Validate::uf('sp');   // "SP"
Validate::uf(' rj '); // "RJ"
Validate::uf('MG');   // "MG"

// Entradas inválidas disparam exceção
try {
    Validate::uf('XX');
} catch (\InvalidArgumentException $e) {
    // "UF informada é inválida: 'XX'"
}

try {
    Validate::uf('');
} catch (\InvalidArgumentException $e) {
    // "Parâmetro UF não pode ser vazio"
}
```

---

## 💡 Uso em FormRequests do Laravel

O helper pode ser facilmente integrado em validações customizadas do Laravel:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Softtan\Toolbox\Helpers\Validate;

class SalvarEmpresaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! Validate::cnpj($value)) {
                        $fail('O CNPJ informado é inválido.');
                    }
                },
            ],
            'uf' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    try {
                        Validate::uf($value);
                    } catch (\InvalidArgumentException) {
                        $fail('A Unidade Federativa informada não existe.');
                    }
                },
            ],
        ];
    }
}
```
