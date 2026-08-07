# Gerenciamento de vereadores

## Decisão

O sistema possuirá um módulo interno para gerenciamento de partidos.
Partidos serão entidades globais e não pertencerão a uma Câmara específica.
Cada partido representará o cadastro atual de uma agremiação partidária.
O histórico de alterações de nome, sigla ou número eleitoral não será versionado inicialmente.
O vínculo entre partido, vereador e legislatura será realizado futuramente por meio da entidade Mandato

## Estrutura

- partidos

A tabela `vereadores` possuirá inicialmente:

- `nome`
- `sigla`
- `numero_eleitoral`
- `deleted_at`

## Regras

- Partidos serão globais e não possuirão `camara_id`.
- O nome será obrigatório e único.
- A sigla será obrigatória e única.
- A sigla será normalizada para letras maiúsculas antes da validação.
- O número eleitoral será opcional.
- Quando informado, o número eleitoral deverá ser único.
- O cadastro representará os dados atuais do partido.
- Alterações históricas de nome, sigla ou número eleitoral não serão versionadas inicialmente.
- Partidos não possuirão vínculo direto com vereadores.
- Partido, vereador, legislatura e período de exercício serão relacionados futuramente pela entidade `Mandato`.
- O root poderá criar, editar, arquivar e restaurar partidos.
- Usuários não-root poderão apenas visualizar partidos quando possuírem a permissão correspondente.
- As autorizações serão centralizadas na `PartidoPolicy`.
- As permissões utilizarão o padrão `modulo:acao`.

## Permissões

- `partidos:visualizar`

## Exclusão lógica

- Partidos serão arquivados por meio de SoftDeletes.
- A exclusão preencherá o campo deleted_at e preservará o registro no banco.
- Partidos arquivados não aparecerão nas consultas e listagens comuns.
- Nome, sigla e número eleitoral de um partido arquivado continuarão reservados.
- Um partido arquivado deverá ser restaurado em vez de ser cadastrado novamente.
- O root poderá consultar a listagem de partidos arquivados e restaurá-los.
- Não haverá exclusão física de partidos neste módulo.