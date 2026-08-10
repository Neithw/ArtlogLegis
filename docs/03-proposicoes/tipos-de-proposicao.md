# Gerenciamento de tipos de proposição

## Decisão

O sistema possuirá um módulo interno para gerenciamento de tipos de proposição.
Cada tipo de proposição pertencerá a uma única Câmara.
Os tipos serão utilizados futuramente para classificar as proposições legislativas.
A Câmara vinculada ao tipo não poderá ser alterada após o cadastro.
Os registros poderão ser arquivados e restaurados.

## Estrutura

- tipos_proposicao
- camaras

A tabela `tipos_proposicao` possuirá inicialmente:

- `camara_id`
- `nome`
- `deleted_at`

## Regras

- Cada tipo de proposição pertencerá a uma única Câmara.
- Uma Câmara poderá possuir vários tipos de proposição.
- O nome será obrigatório.
- O nome será único dentro da mesma Câmara.
- Câmaras diferentes poderão possuir tipos com o mesmo nome.
- A Câmara vinculada não poderá ser alterada após o cadastro.
- Tipos arquivados continuarão reservando o nome dentro da Câmara.
- Usuários não-root serão limitados à própria Câmara.
- O root poderá visualizar e gerenciar tipos de todas as Câmaras.
- Não haverá exclusão definitiva.

## Permissões

- `tipos-proposicao:visualizar`
- `tipos-proposicao:criar`
- `tipos-proposicao:editar`
- `tipos-proposicao:excluir`
- `tipos-proposicao:restaurar`