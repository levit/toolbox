# Utilitários Web e de Navegação (`Api`, `View`, `System`, `Notification`, `Menu`, `Log`)

Esta seção documenta os helpers voltados ao ecossistema web, controladores, templates Blade, ambiente de execução e respostas HTTP.

---

## 🚀 Helper `Api`

Padroniza respostas JSON consistentes para APIs RESTful, com flags booleanas de status, mensagens descritivas, payloads de dados e códigos de status HTTP adequados.

- **Classe:** `Softtan\Toolbox\Helpers\Api`
- **Substitui:** `ApiSupport`

```php
use Softtan\Toolbox\Helpers\Api;
```

### `Api::respostaSucesso`

Retorna uma resposta HTTP JSON padronizada para operações bem-sucedidas.

```php
public static function respostaSucesso(
    mixed $dados = null,
    string $mensagem = 'Operação realizada com sucesso',
    int $codigo = 200
): \Illuminate\Http\JsonResponse
```

#### Estrutura do JSON Gerado
```json
{
    "sucesso": true,
    "mensagem": "Operação realizada com sucesso",
    "dados": { ... }
}
```

#### Exemplo em Controller
```php
public function store(SalvarUsuarioRequest $request)
{
    $usuario = Usuario::create($request->validated());

    return Api::respostaSucesso(
        dados: $usuario,
        mensagem: 'Usuário cadastrado com sucesso!',
        codigo: 201
    );
}
```

---

### `Api::respostaErro`

Retorna uma resposta HTTP JSON padronizada para falhas de requisição ou erros de validação.

```php
public static function respostaErro(
    string $mensagem = 'Erro ao processar requisição',
    int $codigo = 400,
    mixed $erros = null
): \Illuminate\Http\JsonResponse
```

#### Estrutura do JSON Gerado
```json
{
    "sucesso": false,
    "mensagem": "Erro ao processar requisição",
    "erros": [ ... ]
}
```

#### Exemplo
```php
if (! $pagamentoProcessado) {
    return Api::respostaErro(
        mensagem: 'Não foi possível autorizar o pagamento junto ao gateway.',
        codigo: 422,
        erros: ['saldo' => 'Saldo insuficiente no cartão.']
    );
}
```

---

## 🖥️ Helper `View`

Facilita parâmetros de renderização de telas e paginação dinâmica.

- **Classe:** `Softtan\Toolbox\Helpers\View`
- **Substitui:** `ViewSupport`

```php
use Softtan\Toolbox\Helpers\View;
```

### `View::itensPorPagina`

Obtém e sanitiza a quantidade de itens por página a partir do parâmetro `per_page` da query string da requisição HTTP atual.

```php
public static function itensPorPagina(int $padrao = 15): int
```

#### Exemplo em Controller
```php
public function index()
{
    $usuarios = Usuario::paginate(View::itensPorPagina(20));

    return view('usuarios.index', compact('usuarios'));
}
```

---

## ⚙️ Helper `System`

Fornece diagnóstico rápido do ambiente de execução e inspeção contextual de rotas.

- **Classe:** `Softtan\Toolbox\Helpers\System`
- **Substitui:** `SystemSupport`

```php
use Softtan\Toolbox\Helpers\System;
```

| Método | Retorno | Descrição |
| :--- | :--- | :--- |
| `System::emProducao()` | `bool` | Retorna `true` se a aplicação estiver rodando em ambiente `'production'`. |
| `System::emDesenvolvimento()` | `bool` | Retorna `true` se o ambiente atual for `'local'` ou `'development'`. |
| `System::rotaAtiva()` | `?string` | Retorna o nome da rota atualmente correspondida pela requisição HTTP (`request()->route()->getName()`). |
| `System::parametroRota(string $parametro, mixed $padrao = null)` | `mixed` | Obtém o valor de um parâmetro da rota atual (ex: `'id'`, `'uuid'`). |

#### Exemplos

```php
if (System::emProducao()) {
    // Desabilitar exibição de stacktraces detalhados
}

$rota = System::rotaAtiva(); // Ex: "cargos.edit"
$cargoId = System::parametroRota('cargo'); // Ex: 42
```

---

## 🔔 Helper `Notification`

Dispara notificações temporárias (toasts flash) na sessão para exibição imediata na próxima renderização de tela.

- **Classe:** `Softtan\Toolbox\Helpers\Notification`
- **Substitui:** `NotificationSupport`

```php
use Softtan\Toolbox\Helpers\Notification;
```

### `Notification::toast`

```php
public static function toast(string $message, string $type = 'info'): void
```

- Salva a mensagem na sessão sob a chave `toast.{$type}` (ex: `toast.success`, `toast.danger`, `toast.warning`, `toast.info`).

#### Exemplo em Controller
```php
public function destroy(Cargo $cargo)
{
    $cargo->delete();
    Notification::toast('Cargo excluído com sucesso!', 'success');

    return redirect()->route('cargos.list');
}
```

#### Exemplo de Renderização no Blade
```blade
@if(session()->has('toast.success'))
    <div class="alert alert-success">{{ session('toast.success') }}</div>
@endif
```

---

## 🧭 Helper `Menu`

Fornece detecção contextual inteligente de rotas ativas, controle de classes CSS de menus e geração de atributos de acessibilidade (ARIA).

- **Classe:** `Softtan\Toolbox\Helpers\Menu`
- **Substitui:** `MenuSupport`

```php
use Softtan\Toolbox\Helpers\Menu;
```

### Inteligência Contextual de Rotas (`isRouteActive`)

O método `Menu::isRouteActive` vai muito além de uma simples comparação de strings:
1. **Correspondência direta ou curinga:** Suporta `'cargos.list'` ou `'cargos.*'`.
2. **Regra de CRUD (`*.list`):** Quando um menu aponta para `'cargos.list'`, ele é considerado ativo em **todas** as telas filhas do CRUD (`cargos.create`, `cargos.edit`, `cargos.show`, `cargos.importar`), evitando que o menu lateral se desative durante a edição de um registro!
3. **Regra de Módulos Operacionais (`*.index`):** Mantém ativo para rotas filhas do recurso, isolando menus irmãos como `dashboard.*`.
4. **Isolamento de exceções:** Menus de gestão de módulos não ativam indevidamente nas landings dos próprios módulos.

```php
public static function isRouteActive(string $routePattern, ?string $currentRoute = null): bool
```

---

### Métodos de Classes e Acessibilidade

#### `Menu::setActive`
Retorna a classe CSS correspondente (`'active'` por padrão) se a rota estiver ativa:

```php
public static function setActive(
    string $routePattern,
    string $activeClass = 'active',
    string $inactiveClass = '',
    ?string $currentRoute = null
): string
```

#### `Menu::setActiveAria`
Retorna os atributos HTML ARIA para menus ativos ou expandidos:
- Se ativo: `aria-expanded="true" aria-current="page"`
- Se inativo: `aria-expanded="false"`

```php
public static function setActiveAria(string $routePattern, ?string $currentRoute = null): string
```

#### `Menu::getMenu`
Retorna `aria-current="page"` para a rota atual ou string vazia:

```php
public static function getMenu(?string $currentRoute = null): string
```

#### Exemplo em Template Blade
```blade
<li class="nav-item">
    <a href="{{ route('cargos.list') }}"
       class="nav-link {{ \Softtan\Toolbox\Helpers\Menu::setActive('cargos.list') }}"
       {!! \Softtan\Toolbox\Helpers\Menu::setActiveAria('cargos.list') !!}>
        <i class="bi bi-briefcase"></i> Cargos
    </a>
</li>
```

---

## 📝 Helper `Log`

Encapsula chamadas ao subsistema de logs do Laravel com definição dinâmica de níveis e contexto associado.

- **Classe:** `Softtan\Toolbox\Helpers\Log`
- **Substitui:** `LogSupport`

```php
use Softtan\Toolbox\Helpers\Log;
```

### `Log::registrar`

```php
public static function registrar(string $mensagem, string $nivel = 'info', array $contexto = []): void
```

#### Exemplo
```php
Log::registrar(
    mensagem: 'Falha na comunicação com webservice de emissão de boletos',
    nivel: 'error',
    contexto: ['instituicao_id' => 12, 'tentativa' => 3]
);
```
