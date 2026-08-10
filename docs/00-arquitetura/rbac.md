# RBAC

## Decisão

Cada usuário possuirá um único papel (Role).
Os papéis representarão perfis e carregarão pacotes predefinidos de permissões mantidos no back-end, no `UserController`.
Esses pacotes funcionarão apenas como predefinições aplicadas ao formulário.
As permissões efetivas serão atribuídas diretamente aos usuários e poderão ser personalizadas antes do salvamento.

## Estrutura

- users
- roles
- permissoes
- permissao_user

## Regras

- Permissões representam ações do sistema no padrão `modulo:acao`.
- Um papel poderá estar associado a vários usuários.
- A seleção de um papel carregará seu pacote padrão de permissões.
- As permissões carregadas poderão ser ajustadas antes de serem atribuídas ao usuário.
- As permissões efetivas serão vinculadas diretamente ao usuário.
- A alteração de um pacote no código não atualizará automaticamente as permissões dos usuários já cadastrados.
- O usuário root terá autorização global no sistema.
