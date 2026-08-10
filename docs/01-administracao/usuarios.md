# Gerenciamento de usuários

## Decisão

O sistema possuirá um módulo interno para gerenciamento de usuários.
Usuários comuns pertencerão a uma única Câmara.
Cada usuário possuirá um único papel.
A seleção de um papel carregará um pacote predefinido de permissões mantido no back-end, no `UserController`, que poderá ser personalizado antes de ser atribuído ao usuário.
Usuários poderão ser ativados, desativados e, quando autorizado, excluídos logicamente.
A conta de usuário representará o acesso ao sistema e não substituirá entidades específicas, como vereador, assessor ou servidor.

## Estrutura

- users
- camaras
- roles
- permissoes
- permissao_user

A tabela `users` deverá possuir inicialmente:

- `camara_id`
- `role_id`
- `name`
- `email`
- `password`
- `ativo`

## Regras

- Um usuário poderá possuir apenas um papel.
- Um papel poderá estar associado a vários usuários.
- Os pacotes padrão serão definidos no `UserController` e associados aos códigos dos papéis.
- As permissões efetivas serão atribuídas diretamente ao usuário.
- A troca de papel poderá recarregar o pacote padrão de permissões.
- As permissões poderão ser ajustadas individualmente antes do salvamento.
- A alteração de um pacote no código não modificará automaticamente as permissões de usuários já cadastrados.
- O root poderá gerenciar usuários de todas as Câmaras.
- Usuários não-root somente poderão gerenciar usuários da própria Câmara.
- Usuários comuns deverão possuir uma Câmara vinculada.
- O root poderá possuir `camara_id` null.
- Usuários inativos não poderão acessar o sistema.
- Um usuário não poderá desativar a própria conta.
- Usuários comuns não poderão atribuir o papel root.
- A alteração de papéis deverá ser restrita a usuários autorizados.
- A senha não será exibida nem exigida durante uma edição comum.
- A exclusão física de usuários não será permitida pelo fluxo administrativo comum.
- A exclusão de usuários será lógica e restrita ao root.
- Gerentes poderão ativar e desativar usuários quando possuírem as permissões necessárias.
- Dados parlamentares e institucionais não serão armazenados diretamente em `users`.

## Permissões

- `usuarios:visualizar`
- `usuarios:criar`
- `usuarios:editar`
- `usuarios:desativar`
- `usuarios:reativar`

A exclusão lógica de usuários é restrita ao root e não possui uma permissão `usuarios:excluir` no catálogo do RBAC.

## Controle de status

- O status de um usuário não será alterado pela edição comum.
- A desativação exigirá a permissão `usuarios:desativar`.
- A reativação exigirá a permissão `usuarios:reativar`.
- Um usuário não poderá desativar a própria conta.
- Contas root não poderão ser desativadas pelo fluxo administrativo comum.
- Usuários inativos não poderão iniciar uma nova sessão.
- Um usuário já autenticado que seja desativado terá sua sessão encerrada na próxima requisição.

## Exclusão lógica

- Usuários serão excluídos logicamente por meio de `SoftDeletes`.
- A exclusão preencherá o campo `deleted_at` e preservará o registro no banco.
- Usuários excluídos não aparecerão nas consultas e listagens comuns.
- Usuários excluídos não poderão se autenticar.
- A exclusão será restrita ao root.
- Contas root não poderão ser excluídas pelo fluxo administrativo comum.
- Um usuário não poderá excluir a própria conta.
- As permissões associadas ao usuário serão preservadas na exclusão lógica.