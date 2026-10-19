# Data e Hora (`DateTime`)

O helper `DateTime` padroniza a formatação e conversão de datas e horas para os padrões convencionais do Brasil (`dd/mm/aaaa` e `dd/mm/aaaa HH:mm`), oferecendo interoperabilidade com `Carbon`, objetos nativos `DateTimeInterface` e strings de data.

- **Classe:** `Softtan\Toolbox\Helpers\DateTime`
- **Substitui:** `DateTimeSupport`

```php
use Softtan\Toolbox\Helpers\DateTime;
```

---

## 📌 Sumário de Métodos

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| [`DateTime::dataBr(mixed $data)`](#datetimedatabr) | `?string` | Formata uma data para o padrão brasileiro `dd/mm/aaaa` (`d/m/Y`). |
| [`DateTime::dataHoraBr(mixed $data)`](#datetimedatahorabr) | `?string` | Formata uma data e hora para o padrão brasileiro `dd/mm/aaaa HH:mm` (`d/m/Y H:i`). |

---

## 📖 Referência Detalhada dos Métodos

### `DateTime::dataBr`

Converte qualquer representação válida de data para uma string formatada em `d/m/Y`.

```php
public static function dataBr(mixed $data): ?string
```

#### Parâmetros
- `mixed $data`: Instância de `Carbon\Carbon`, `DateTimeInterface` (`DateTime`, `DateTimeImmutable`), string parseável (ISO 8601, SQL `YYYY-MM-DD`, etc.) ou `null`.

#### Retorno
- `?string`: Data formatada no padrão brasileiro (`25/12/2026`) ou `null` se a entrada for vazia ou nula.

#### Exemplos

```php
// Com string no formato SQL (Y-m-d)
DateTime::dataBr('2026-10-12');
// Resultado: "12/10/2026"

// Com string ISO completa
DateTime::dataBr('2026-12-31T23:59:59.000000Z');
// Resultado: "31/12/2026"

// Com instância do Carbon
DateTime::dataBr(\Carbon\Carbon::now());
// Resultado: "12/10/2026"

// Com DateTimeImmutable nativo do PHP
DateTime::dataBr(new \DateTimeImmutable('2026-05-01'));
// Resultado: "01/05/2026"

// Com valores nulos ou vazios
DateTime::dataBr(null); // null
DateTime::dataBr('');   // null
```

---

### `DateTime::dataHoraBr`

Converte qualquer representação válida de data e hora para uma string formatada em `d/m/Y H:i`.

```php
public static function dataHoraBr(mixed $data): ?string
```

#### Parâmetros
- `mixed $data`: Instância de `Carbon\Carbon`, `DateTimeInterface`, string parseável ou `null`.

#### Retorno
- `?string`: Data e hora formatadas (`25/12/2026 14:30`) ou `null` se a entrada for vazia ou nula.

#### Exemplos

```php
// Com string SQL (Y-m-d H:i:s)
DateTime::dataHoraBr('2026-10-12 18:45:00');
// Resultado: "12/10/2026 18:45"

// Com string ISO
DateTime::dataHoraBr('2026-10-12T14:30:00Z');
// Resultado: "12/10/2026 14:30"

// Com Carbon
DateTime::dataHoraBr(\Carbon\Carbon::create(2026, 1, 15, 9, 5));
// Resultado: "15/01/2026 09:05"

// Tratamento seguro de nulos
DateTime::dataHoraBr(null); // null
DateTime::dataHoraBr('');   // null
```

---

## 💡 Uso em Views Blade

Ideal para formatação rápida de timestamps em tabelas de auditoria e listagens:

```blade
<td>{{ \Softtan\Toolbox\Helpers\DateTime::dataBr($inscricao->data_nascimento) }}</td>
<td>{{ \Softtan\Toolbox\Helpers\DateTime::dataHoraBr($inscricao->created_at) }}</td>
```
