# Gerenciamento de sessões

## Decisão

O sistema possuirá um módulo interno para gerenciamento de sessões legislativas.
Cada sessão pertencerá a uma Câmara e a uma legislatura.
O usuário responsável pelo cadastro será registrado automaticamente.
Toda sessão será cadastrada inicialmente em preparação, sem número e ano oficiais.
A numeração e o ano oficiais serão atribuídos durante o fluxo de convocação.
Sessões não poderão ser arquivadas ou excluídas, preservando seu valor histórico.
O ciclo inicial da sessão contempla convocação, abertura, suspensão, retomada, encerramento e cancelamento.
Pauta, presença e votações serão implementadas em etapas próprias.

## Estrutura

- sessoes
- camaras
- legislaturas
- users
- sessao_eventos

A tabela `sessoes` possuirá:

- `camara_id`
- `legislatura_id`
- `criado_por_id`
- `numero`
- `ano`
- `data_hora_inicio_previsto`
- `tipo`
- `local`
- `situacao`

A tabela `sessao_eventos` possuirá:

- `sessao_id`
- `executado_por_id`
- `acao`
- `situacao_anterior`
- `situacao_nova`
- `observacao`
- `timestamps`

## Tipos

Inicialmente, uma sessão poderá ser classificada como:

- Ordinária
- Extraordinária
- Solene
- Especial
- Audiência Pública

## Situações

O ciclo de vida previsto utilizará as seguintes situações:

- `em_preparacao`
- `convocada`
- `aberta`
- `suspensa`
- `encerrada`
- `cancelada`

Toda sessão será cadastrada inicialmente como `em_preparacao`.
As demais situações serão atribuídas exclusivamente pelas ações do ciclo de vida.

## Regras

- Cada sessão pertencerá a uma única Câmara.
- A legislatura deverá pertencer à mesma Câmara da sessão.
- Novas sessões somente poderão ser cadastradas para Câmaras ativas.
- A legislatura não poderá estar arquivada no momento do cadastro ou da edição.
- A data e o horário previstos serão obrigatórios.
- A data prevista deverá estar dentro do período da legislatura.
- O tipo deverá existir na lista definida pelo sistema.
- O local será opcional.
- O usuário responsável pelo cadastro será definido automaticamente no back-end.
- Usuários não-root serão vinculados automaticamente à própria Câmara.
- O root poderá selecionar uma Câmara e possuirá acesso global ao módulo.
- A Câmara vinculada não poderá ser alterada após o cadastro.
- Toda sessão será cadastrada inicialmente com a situação `em_preparacao`.
- A situação não poderá ser escolhida manualmente.
- `numero` e `ano` permanecerão nulos até a convocação.
- A combinação entre legislatura, tipo, ano e número será única.
- Somente sessões em preparação poderão ser editadas.
- Sessões convocadas não poderão retornar à preparação.
- Tipos e situações serão armazenados como strings e controlados pelo Model.
- Vínculos históricos com legislaturas e usuários arquivados serão preservados.
- Sessões não utilizarão exclusão lógica ou exclusão definitiva.
- A convocação e as demais mudanças de situação utilizarão ações e permissões próprias.

## Ciclo de vida

As mudanças de situação são realizadas exclusivamente por ações específicas do sistema:

- sessões em preparação podem ser convocadas;
- sessões convocadas podem ser abertas;
- sessões abertas podem ser suspensas ou encerradas;
- sessões suspensas podem ser retomadas;
- sessões permitidas pela regra de transição podem ser canceladas;
- sessões encerradas ou canceladas são estados finais;
- a situação não pode ser alterada diretamente pelos formulários de cadastro e edição.

A convocação atribui o número e o ano oficiais da sessão, respeitando a sequência definida para a Câmara.

Cada mudança de situação gera um evento contendo:

- ação executada;
- situação anterior;
- nova situação;
- usuário responsável;
- observação ou justificativa, quando aplicável;
- data e horário da execução.

Os eventos são preservados como histórico permanente e apresentados do mais recente para o mais antigo.

## Permissões

O módulo utiliza permissões específicas para consulta, manutenção e mudanças de situação:

- `sessoes:visualizar`
- `sessoes:criar`
- `sessoes:editar`
- `sessoes:convocar`
- `sessoes:abrir`
- `sessoes:suspender`
- `sessoes:retomar`
- `sessoes:encerrar`
- `sessoes:cancelar`

As Policies combinam a permissão do usuário com o escopo da Câmara e a situação atual da sessão.