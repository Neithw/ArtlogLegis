# Gerenciamento de unidades de tramitação

## Decisão

O sistema possuirá um módulo interno para gerenciamento das unidades de tramitação.
As unidades representarão os setores, órgãos ou estruturas pelos quais uma proposição poderá tramitar.
Cada unidade poderá possuir usuários responsáveis pelo recebimento e encaminhamento de proposições.

## Estrutura

- unidades_tramitacao
- camaras
- users
- unidade_tramitacao_user

A tabela `unidades_tramitacao` possuirá:

- `camara_id`
- `nome`
- `sigla`
- `tipo`
- `descricao`
- `deleted_at`

A tabela intermediária `unidade_tramitacao_user` possuirá:

- `unidade_tramitacao_id`
- `user_id`

## Tipos

Inicialmente, uma unidade poderá ser classificada como:

- Secretaria
- Mesa Diretora
- Plenário
- Departamento
- Unidade Administrativa
- Órgão Externo
- Outro

## Regras

- Cada unidade de tramitação pertencerá a uma única Câmara.
- Uma Câmara poderá possuir várias unidades de tramitação.
- O nome da unidade será único dentro de cada Câmara.
- Câmaras diferentes poderão possuir unidades com o mesmo nome.
- A Câmara vinculada não poderá ser alterada após o cadastro.
- Uma unidade poderá possuir vários usuários.
- Um usuário poderá atuar em várias unidades.
- Usuários somente poderão ser vinculados a unidades de sua própria Câmara.
- O vínculo entre usuário e unidade não será obrigatório.
- Usuários não-root poderão acessar somente unidades de sua própria Câmara.
- O root possuirá acesso global e não dependerá de vínculo com unidades.
- Apenas Câmaras ativas poderão receber novas unidades.
- Unidades poderão ser arquivadas e posteriormente restauradas.
- Unidades arquivadas continuarão reservando seu nome dentro da Câmara.
- Não haverá campo `ativo`, pois a disponibilidade da unidade será controlada pelo arquivamento lógico.
- A exclusão definitiva não será permitida.

## Permissões

O módulo utilizará as seguintes permissões:

- `unidades-tramitacao:visualizar`
- `unidades-tramitacao:criar`
- `unidades-tramitacao:editar`
- `unidades-tramitacao:excluir`
- `unidades-tramitacao:restaurar`

## Integração com usuários

- As unidades de atuação serão selecionadas nos formulários de criação e edição de usuários.
- Somente unidades pertencentes à Câmara selecionada serão apresentadas.
- O vínculo com uma unidade definirá onde o usuário poderá receber e encaminhar proposições.