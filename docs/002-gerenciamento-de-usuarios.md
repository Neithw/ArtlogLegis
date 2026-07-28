# Gerenciamento de usuários

## Decisão

O sistema possuirá um módulo interno para gerenciamento de usuários.
Usuários comuns pertencerão a uma única Câmara.
Cada usuário possuirá um único papel.
A seleção de um papel carregará um pacote predefinido de permissões, que poderá ser personalizado antes de ser atribuído ao usuário.
Usuários poderão ser ativados, desativados e, quando autorizado, excluídos logicamente.
A conta de usuário representará o acesso ao sistema e não substituirá entidades específicas, como vereador, assessor ou servidor.

## Estrutura

- users
- camaras
- roles
- permissions
- permission_role
- permission_user

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
- Os papéis definirão pacotes padrão de permissões.
- As permissões efetivas serão atribuídas diretamente ao usuário.
- A troca de papel poderá recarregar o pacote padrão de permissões.
- As permissões poderão ser ajustadas individualmente antes do salvamento.
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
