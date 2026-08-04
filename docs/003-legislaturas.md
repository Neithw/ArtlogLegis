# Gerenciamento de legislaturas

## Decisão

O sistema possuirá um módulo interno para gerenciamento de legislaturas.
Cada legislatura representará um período institucional de uma Câmara.
A legislatura estabelecerá o contexto temporal da atividade parlamentar.
Dados de vereadores, partidos, mandatos, afastamentos e substituições não serão armazenados diretamente em `legislaturas`.
O vínculo entre vereador e legislatura será realizado futuramente por meio da entidade Mandato.

## Estrutura

- legislaturas
- camaras

A tabela `legislaturas` possuirá inicialmente:

- `camara_id`
- `numero`
- `data_inicio`
- `data_fim`
- `deleted_at`

## Regras

- Uma Câmara poderá possuir várias legislaturas.
- Cada legislatura pertencerá a uma única Câmara.
- O número da legislatura será único dentro de cada Câmara.
- Câmaras diferentes poderão possuir legislaturas com o mesmo número.
- A data de término deverá ser posterior à data de início.
- Legislaturas da mesma Câmara não poderão possuir períodos sobrepostos.
- A Câmara vinculada não poderá ser alterada após o cadastro.
- A situação da legislatura será calculada por suas datas.
- Não será armazenado um campo de status ou ativo.
- Uma legislatura poderá ser futura, estar em andamento ou estar encerrada.
- Uma legislatura encerrada continuará sendo um registro histórico válido.
- O root poderá gerenciar legislaturas de todas as Câmaras.
- Usuários não-root somente poderão gerenciar legislaturas da própria Câmara.
- As autorizações serão centralizadas na `LegislaturaPolicy`.
- As permissões utilizarão o padrão `modulo:acao`.

## Permissões

- `legislaturas:visualizar`
- `legislaturas:criar`
- `legislaturas:editar`
- `legislaturas:excluir`

## Exclusão lógica

- Legislaturas serão excluídas logicamente por meio de `SoftDeletes`.
- A exclusão preencherá o campo `deleted_at` e preservará o registro no banco.
- Legislaturas excluídas não aparecerão nas consultas e listagens comuns.
- O número de uma legislatura excluída continuará reservado para a respectiva Câmara.
- Futuramente, a exclusão será impedida quando existirem mandatos ou outros registros legislativos vinculados.
