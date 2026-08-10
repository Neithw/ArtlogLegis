# Escopo de Câmara

## Decisão

Usuários comuns pertencerão a uma única Câmara.
Os dados institucionais serão isolados por `camara_id`, impedindo que um usuário acesse ou altere registros internos de outra Câmara.
O usuário root será global e poderá atuar sobre todas as Câmaras.

## Estrutura

- users
- camaras
- `camara_id` nas entidades com escopo institucional

## Regras

- Usuários não-root serão limitados à própria Câmara.
- O usuário root poderá visualizar e gerenciar dados de todas as Câmaras.
- O root não precisará estar vinculado a uma Câmara.
- As permissões definirão quais ações o usuário poderá executar.
- O camara_id definirá sobre quais dados o usuário poderá executar essas ações.
- O escopo será aplicado no back-end e não dependerá apenas da interface.
- Cadastros realizados por usuários não-root receberão a Câmara do usuário autenticado.
- Relacionamentos entre entidades institucionais deverão respeitar a mesma Câmara.