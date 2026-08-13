# Tramitação de proposições

## Decisão

O sistema permitirá encaminhar proposições protocoladas entre unidades de tramitação.
Cada movimentação representará um registro histórico do encaminhamento e do recebimento da proposição.
As tramitações não possuirão edição, arquivamento ou exclusão.
O fluxo será realizado em uma página própria, separada dos detalhes gerais da proposição.

## Estrutura

- tramitacoes
- proposicoes
- unidades_tramitacao
- users
- unidade_tramitacao_user

A tabela `tramitacoes` possuirá:

- `proposicao_id`
- `unidade_origem_id`
- `unidade_destino_id`
- `encaminhado_por_id`
- `recebido_por_id`
- `data_encaminhamento`
- `data_recebimento`
- `despacho`

## Regras

- Somente proposições protocoladas poderão tramitar.
- Cada tramitação pertencerá a uma única proposição.
- A unidade de destino deverá pertencer à mesma Câmara da proposição.
- Unidades arquivadas não poderão receber novos encaminhamentos.
- A primeira tramitação terá origem nula e será apresentada como originada no protocolo.
- O primeiro encaminhamento poderá ser realizado por um usuário da Câmara com a permissão necessária.
- Nas tramitações seguintes, a origem será o último destino recebido.
- Origem e destino deverão ser diferentes.
- Uma proposição não poderá possuir duas tramitações pendentes simultaneamente.
- Enquanto o recebimento não for confirmado, `recebido_por_id` e `data_recebimento` permanecerão nulos.
- Somente usuários vinculados à unidade de destino e com a permissão necessária poderão confirmar o recebimento.
- Após o recebimento, a unidade de destino passará a representar a localização atual.
- Somente usuários vinculados à unidade atual e com a permissão necessária poderão realizar um novo encaminhamento.
- O vínculo do usuário limitará a unidade a partir da qual ele poderá atuar, não as unidades disponíveis como destino.
- O encaminhamento e o recebimento registrarão automaticamente o usuário e o horário.
- A localização atual será obtida pelo histórico das tramitações.
- Tramitações formarão um histórico imutável e não utilizarão exclusão lógica.
- As operações utilizarão transações e bloqueio dos registros para evitar ações concorrentes duplicadas.
- Usuários não-root poderão visualizar somente tramitações de proposições pertencentes à sua Câmara.
- O root possuirá acesso global pelo Gate::before, independentemente de vínculo com unidades.

## Permissões

O módulo utilizará as seguintes permissões:

- `tramitacoes:visualizar`
- `tramitacoes:encaminhar`
- `tramitacoes:receber`

## Fluxo

O fluxo inicial será:

1. A proposição é protocolada.
2. A proposição é encaminhada para uma unidade.
3. A tramitação permanece aguardando recebimento.
4. Um usuário autorizado da unidade de destino confirma o recebimento.
5. A unidade de destino passa a representar a localização atual.
6. Um usuário autorizado da unidade atual poderá encaminhar a proposição para outra unidade.
7. Cada movimentação permanecerá registrada no histórico.