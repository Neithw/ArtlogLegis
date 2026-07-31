# RBAC e escopo de Câmara

## Decisão

Cada usuário possuirá um único papel (Role).
Os papéis representam perfis. Os pacotes padrão de permissões serão definidos no back-end, no `UserController`.
Os pacotes funcionarão apenas como predefinições aplicadas ao formulário e não como permissões herdadas permanentemente.
As permissões efetivas serão atribuídas diretamente aos usuários e poderão ser personalizadas.
Usuários comuns pertencerão inicialmente a uma única Câmara.

## Estrutura

- users
- camaras
- roles
- permissoes
- permissao_user

## Regras

- Permissões representam ações do sistema no padrão modulo:acao.
- Um papel poderá estar associado a vários usuários.
- Cada papel poderá possuir um pacote padrão de permissões definido no `UserController` por meio de seu código.
- As permissões efetivas serão vinculadas diretamente ao usuário.
- A seleção de um papel carregará seu pacote padrão de permissões.
- As permissões carregadas poderão ser ajustadas antes de serem atribuídas ao usuário.
- A alteração de um pacote no código não atualizará automaticamente as permissões dos usuários já salvos.
- O RBAC controla quais ações podem ser executadas.
- O camara_id controla sobre quais dados o usuário pode atuar.
- Usuários não-root não poderão acessar dados internos de outra Câmara.