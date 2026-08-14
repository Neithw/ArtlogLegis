# Gerenciamento de câmaras

## Decisão

O sistema possuirá um módulo interno para gerenciamento das Câmaras atendidas.
Cada Câmara representará uma instituição independente dentro do sistema.
As Câmaras poderão ser ativadas e desativadas, mas não serão arquivadas ou excluídas pelo fluxo administrativo comum.
O usuário root será global e poderá gerenciar todas as Câmaras.

As regras gerais de isolamento dos dados estão documentadas no arquivo `escopo-de-camara`.

## Estrutura

- camaras

A tabela `camaras` possuirá:

- `nome`
- `cnpj`
- `ativo`

## Regras

- O nome da Câmara será obrigatório.
- O CNPJ será opcional.
- Quando informado, o CNPJ deverá possuir 14 dígitos e ser único.
- O CNPJ será armazenado apenas com números e formatado na interface.
- Novas Câmaras serão cadastradas como ativas.
- A situação da Câmara será armazenada no campo `ativo`.
- O status não será alterado pelo formulário comum de edição.
- Uma Câmara inativa continuará registrada e visível para o root.
- A desativação preservará todos os registros e relacionamentos da Câmara.
- Usuários vinculados a uma Câmara inativa não poderão acessar o sistema.
- Sessões de usuários vinculados a uma Câmara desativada serão encerradas na próxima requisição.
- O root não será afetado pela desativação de uma Câmara.
- O root poderá visualizar e gerenciar todas as Câmaras.
- Usuários não-root somente poderão visualizar e editar a própria Câmara quando possuírem as permissões necessárias.
- As autorizações serão centralizadas na `CamaraPolicy`.
- Os dados institucionais relacionados não serão armazenados diretamente em `camaras`.

## Permissões

- `camaras:visualizar`
- `camaras:editar`

A criação, desativação e reativação de Câmaras são operações exclusivas do root e, por isso, não possuem permissões próprias no catálogo do RBAC.

## Controle de status

- A desativação será uma ação própria e não fará parte da edição comum.
- Uma Câmara desativada poderá ser reativada pelo root.
- A desativação bloqueará o acesso dos usuários vinculados, mas não removerá dados ou relacionamentos.
- Não haverá páginas de Câmaras arquivadas nem ações de restauração.

## Remoção

- Câmaras não serão arquivadas por meio de SoftDeletes.
- Não haverá rota ou ação de exclusão no fluxo administrativo comum.
- A remoção estrutural de uma Câmara será excepcional, administrativa e realizada fora do fluxo normal do sistema.