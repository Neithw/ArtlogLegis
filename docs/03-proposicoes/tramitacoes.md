# Tramitação de proposições

## Decisão

O sistema permitirá encaminhar proposições protocoladas entre unidades de tramitação.
Cada movimentação representará um registro histórico do encaminhamento e do recebimento da proposição.
As tramitações não possuirão edição, arquivamento ou exclusão.
O fluxo inicial será realizado diretamente na página de detalhes da proposição.

## Estrutura

- tramitacoes
- proposicoes
- unidades_tramitacao
- users

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
- Nas tramitações seguintes, a origem será o último destino recebido.
- Origem e destino deverão ser diferentes.
- Uma proposição não poderá possuir duas tramitações pendentes simultaneamente.
- Enquanto o recebimento não for confirmado, `recebido_por_id` e `data_recebimento` permanecerão nulos.
- O encaminhamento e o recebimento registrarão automaticamente o usuário e o horário.
- A localização atual será obtida pelo histórico das tramitações.
- Tramitações formarão um histórico imutável e não utilizarão exclusão lógica.
- As operações utilizarão transações e bloqueio dos registros para evitar ações concorrentes duplicadas.
- Usuários não-root poderão operar somente sobre proposições de sua Câmara.
- O root possuirá acesso global pelo `Gate::before`.
- Neste momento, qualquer usuário da Câmara com a permissão necessária poderá confirmar o recebimento.

## Permissões

O módulo utilizará as seguintes permissões:

- `tramitacoes:visualizar`
- `tramitacoes:encaminhar`
- `tramitacoes:receber`

## Fluxo

O fluxo inicial será:

1. A proposição é protocolada.
2. A proposição é encaminhada para uma unidade.
3. A tramitação permanece pendente.
4. O recebimento é confirmado.
5. A unidade de destino passa a representar a localização atual.
6. A proposição poderá ser encaminhada novamente.