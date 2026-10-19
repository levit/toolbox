# Serialização JSON (`Json`)

O helper `Json` padroniza a serialização e desserialização de estruturas de dados no formato JSON com suporte nativo e legível a caracteres **Unicode** e conversão direta para arrays associativos.

- **Classe:** `Softtan\Toolbox\Helpers\Json`
- **Substitui:** `JsonSupport`

```php
use Softtan\Toolbox\Helpers\Json;
```

---

## 📌 Sumário de Métodos

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| [`Json::encode(mixed $valor)`](#jsonencode) | `string` | Serializa dados para JSON formatado com Unicode (`JSON_UNESCAPED_UNICODE \| JSON_PRETTY_PRINT`). |
| [`Json::decode(?string $json)`](#jsondecode) | `?array` | Desserializa string JSON diretamente para array associativo do PHP. |

---

## 📖 Referência Detalhada dos Métodos

### `Json::encode`

Converte qualquer estrutura de dados (arrays associativos, escalares, objetos com propriedades públicas ou implementando `JsonSerializable`) em uma string JSON formatada.

#### Diferenciais em relação ao `json_encode` nativo do PHP:
1. **Preservação de Acentos e Emojis (`JSON_UNESCAPED_UNICODE`):** Caracteres como `ã`, `é`, `ç` ou emojis não são convertidos para sequências hexadecimais como `\u00e3`, gerando arquivos e respostas muito mais legíveis e compactas.
2. **Indentação Automática (`JSON_PRETTY_PRINT`):** Formata com quebras de linha e espaçamentos limpos, ideal para visualização em logs, depuração e arquivos de configuração.

```php
public static function encode(mixed $valor): string
```

#### Parâmetros
- `mixed $valor`: Valor a ser serializado.

#### Retorno
- `string`: String JSON gerada.

#### Exemplos

```php
$dados = [
    'cidade' => 'São Paulo',
    'descricao' => 'Ação de capacitação profissional',
    'status' => 'Concluído ✅',
    'participantes' => 120,
];

echo Json::encode($dados);
```

**Saída gerada:**
```json
{
    "cidade": "São Paulo",
    "descricao": "Ação de capacitação profissional",
    "status": "Concluído ✅",
    "participantes": 120
}
```

> **Nota:** Com o `json_encode()` padrão do PHP, a palavra "São Paulo" seria codificada como `"S\u00e3o Paulo"`. O `Json::encode` elimina esse comportamento indesejado.

---

### `Json::decode`

Desserializa uma string JSON para um array associativo do PHP de forma segura.

```php
public static function decode(?string $json): ?array
```

#### Parâmetros
- `?string $json`: String JSON a ser decodificada.

#### Retorno
- `?array`: Retorna o array associativo decodificado, ou `null` caso a entrada seja nula, vazia ou inválida.

#### Exemplos

```php
$jsonString = '{"id": 10, "usuario": "admin", "ativo": true}';

$array = Json::decode($jsonString);
/*
[
    "id" => 10,
    "usuario" => "admin",
    "ativo" => true,
]
*/

// Tratamento seguro de nulos ou vazios
Json::decode(null); // Retorna null
Json::decode('');   // Retorna null
```

---

## 💡 Cenários de Aplicação

- **Armazenamento de payloads em colunas JSON:** Padronização antes de salvar em bancos MySQL, PostgreSQL ou SQLite.
- **Auditoria e Logs:** Geração de snapshots estruturados e legíveis de entidades e eventos em disco.
- **Exportação de Configurações:** Geração de manifests e arquivos de manifesto legíveis para humanos.
