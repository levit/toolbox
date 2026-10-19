# 🏷️ Guia de Criação e Gerenciamento de Tags no Git

Este guia explica como versionar a biblioteca **Softtan Toolbox** utilizando tags do Git e o padrão **Semantic Versioning (SemVer)** para que o Composer e os projetos consumidores consigam baixar e atualizar o pacote corretamente.

---

## 📌 1. O que é uma Tag no Git?

Uma **tag** é um marcador estático que aponta para um commit específico no histórico do Git. No ecossistema PHP/Composer, as tags definem as versões oficiais da sua biblioteca que estarão disponíveis para instalação (ex: `v1.0.0`, `v1.1.0`, `v2.0.0`).

---

## 🚀 2. Passo a Passo: Criando sua Primeira Tag

### Passo 1: Garanta que todas as alterações estão commitadas
Antes de criar uma tag, certifique-se de que a sua árvore de trabalho está limpa:

```bash
git status
```

Se houver arquivos pendentes:
```bash
git add .
git commit -m "feat: preparando release v1.0.0"
```

### Passo 2: Criar a Tag Anotada (Recomendado)
Sempre dê preferência para **tags anotadas** (`-a`), pois elas guardam o autor, a data e a descrição do lançamento:

```bash
git tag -a v1.0.0 -m "Release v1.0.0: Versão inicial da biblioteca Softtan Toolbox"
```

> **Dica**: Por convenção no PHP e Composer, utiliza-se o prefixo `v` seguido do número da versão (ex: `v1.0.0` ou `1.0.0`). O Composer aceita ambos perfeitamente.

### Passo 3: Enviar a Tag para o Repositório Remoto (GitHub / GitLab)
Por padrão, o comando `git push` normal **não** envia tags. Você deve enviar a tag explicitamente:

```bash
# Enviar uma tag específica:
git push origin v1.0.0

# Ou enviar todas as tags locais pendentes de uma vez:
git push origin --tags
```

---

## 🔄 3. Versionamento Semântico (SemVer)

Adote a convenção `vMAJOR.MINOR.PATCH`:

| Tipo | Quando usar? | Exemplo anterior | Novo exemplo |
| :--- | :--- | :--- | :--- |
| **PATCH** | Correção de bugs retrocompatíveis | `v1.0.0` | `v1.0.1` |
| **MINOR** | Novas funcionalidades retrocompatíveis | `v1.0.1` | `v1.1.0` |
| **MAJOR** | Mudanças que quebram compatibilidade (*breaking changes*) | `v1.1.0` | `v2.0.0` |

---

## 🛠️ 4. Comandos Úteis do Dia a Dia

### Listar tags existentes
```bash
# Listar apenas os nomes das tags
git tag

# Listar tags com suas respectivas mensagens
git tag -n
```

### Visualizar informações detalhadas de uma tag
```bash
git show v1.0.0
```

### Criar tag apontando para um commit antigo
Caso você tenha esquecido de criar a tag no momento do commit:
```bash
git tag -a v1.0.0 <hash_do_commit> -m "Release retroativa v1.0.0"
```

---

## ⚠️ 5. Como Corrigir / Deletar uma Tag

Se você criou uma tag por engano ou com o número errado:

### 1. Deletar no ambiente local:
```bash
git tag -d v1.0.0
```

### 2. Deletar no repositório remoto (GitHub / GitLab):
```bash
git push origin --delete v1.0.0
```

> **Atenção**: Evite deletar ou mover tags que já foram publicadas e baixadas por outros projetos, pois isso pode quebrar o cache do Composer nos clientes. Em vez disso, lance uma versão subsequente (ex: `v1.0.1`).

---

## 📦 6. Como os outros projetos usam a Tag criada

Assim que a tag for enviada para o remoto e registrada no Packagist (ou configurada via repositório VCS):

1. **No projeto que consome a biblioteca:**
   ```bash
   composer require softtan/toolbox:^1.0
   ```

2. **Para atualizar para uma nova tag lançada (ex: v1.0.1 ou v1.1.0):**
   ```bash
   composer update softtan/toolbox
   ```

