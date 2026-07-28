# RBAC e escopo de Câmara

## Decisão

Cada usuário possuirá um único papel.
Os papéis representam perfis e possuem pacotes padrão de permissões.
As permissões efetivas serão atribuídas diretamente aos usuários e poderão ser personalizadas.
Usuários comuns pertencerão inicialmente a uma única Câmara.

## Estrutura

- users
- camaras
- roles
- permissions
- permission_role
- permission_user

## Regras

- Permissões representam ações do sistema no padrão modulo:acao.
- Um papel poderá estar associado a vários usuários.
- Um papel poderá possuir várias permissões predefinidas.
- As permissões efetivas serão vinculadas diretamente ao usuário.
- A seleção de um papel carregará seu pacote padrão de permissões.
- As permissões carregadas poderão ser ajustadas antes de serem atribuídas ao usuário.
- O RBAC controla quais ações podem ser executadas.
- O camara_id controla sobre quais dados o usuário pode atuar.
- Usuários não-root não poderão acessar dados internos de outra Câmara.