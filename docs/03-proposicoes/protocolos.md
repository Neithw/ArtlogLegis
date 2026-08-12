# Fluxo de protocolação

## Decisão

O sistema possuirá um fluxo interno para protocolação de proposições.
O protocolo será uma ação realizada sobre uma proposição em rascunho e não possuirá entidade ou tabela própria.
Ao ser protocolada, a proposição receberá numeração oficial, data de protocolo e o usuário responsável pela ação.
O protocolo será definitivo e não poderá ser desfeito.
A tramitação da proposição será implementada em etapa futura.

## Estrutura

O fluxo utilizará:

- proposicoes
- camaras
- tipos_proposicao
- legislaturas
- mandatos
- users

A tabela `proposicoes` possuirá os campos de protocolo:

- `numero`
- `ano`
- `data_protocolo`
- `protocolado_por_id`

## Regras

- Somente proposições em situação `rascunho` poderão ser protocoladas.
- A Câmara da proposição deverá estar ativa.
- A legislatura e o tipo de proposição deverão estar disponíveis e pertencer à mesma Câmara.
- O mandato do autor deverá estar disponível, pertencer à legislatura selecionada e representar um vereador da mesma Câmara.
- Ementa, assunto e texto integral serão obrigatórios para protocolar.
- O número, o ano, a data e o usuário responsável serão definidos pelo sistema.
- A numeração será anual e independente por Câmara e tipo de proposição.
- A sequência será reiniciada a cada ano.
- Números anteriormente utilizados não poderão ser reutilizados.
- A geração da numeração será realizada em transação e protegida contra protocolos simultâneos.
- O protocolo alterará a situação da proposição de `rascunho` para `protocolada`.
- Uma proposição protocolada não poderá retornar à situação de rascunho.
- Proposições protocoladas não poderão ser editadas ou arquivadas.
- Usuários não-root serão limitados às proposições da própria Câmara.
- O root possuirá acesso global, respeitando as regras definitivas do protocolo.

## Permissões

- `proposicoes:protocolar`