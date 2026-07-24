# RBAC e escopo de Câmara

## Decisão

O sistema utilizará papéis e permissões em relações muitos-para-muitos.

Usuários comuns pertencerão inicialmente a uma única Câmara.

O usuário com papel root será global e poderá possuir `camara_id` null.

## Estrutura

- users
- camaras
- roles
- permissions
- role_user
- permission_role

## Regras

- Papéis representam funções exercidas.
- Permissões representam ações do sistema.
- O RBAC controla quais ações podem ser executadas.
- O `camara_id` controla sobre quais dados o usuário pode atuar.
- Usuários não-root não poderão acessar dados internos de outra Câmara.