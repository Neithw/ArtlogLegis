# Gerenciamento de unidades de tramitação

## Decisão

O sistema possuirá um módulo interno para gerenciamento das unidades de tramitação.
As unidades representarão os setores, órgãos ou estruturas pelos quais uma proposição poderá tramitar.
Neste momento, o módulo manterá apenas o cadastro das unidades.
O vínculo das unidades com as movimentações de uma proposição será implementado no fluxo de tramitação.

## Estrutura

- unidades_tramitacao
- camaras

A tabela `unidades_tramitacao` possuirá:

- `camara_id`
- `nome`
- `sigla`
- `tipo`
- `descricao`
- `deleted_at`

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
- Usuários não-root poderão acessar somente unidades de sua própria Câmara.
- O root possuirá acesso global às unidades.
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

## Fluxo futuro

Quando o fluxo de tramitação for implementado, as unidades poderão representar a origem e o destino de cada movimentação realizada em uma proposição.