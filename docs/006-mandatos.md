# Gerenciamento de vereadores

## Decisão

O sistema possuirá um módulo interno para gerenciamento de mandatos.
Cada mandato representará o vínculo institucional entre um vereador e uma legislatura.
O mandato possuirá período próprio de início e término.
A Câmara do mandato será determinada pela legislatura vinculada e não será armazenada diretamente em `mandatos`.
Dados de partido, afastamentos, substituições e suplência não serão armazenados diretamente em `mandatos` nesta etapa.

## Estrutura

- mandatos
- vereadores
- legislaturas

A tabela `mandatos` possuirá inicialmente:

- `vereador_id`
- `legislatura_id`
- `data_inicio`
- `data_fim`
- `deleted_at`

## Regras

- Cada mandato pertencerá a um único vereador.
- Cada mandato pertencerá a uma única legislatura.
- Um vereador poderá possuir vários mandatos.
- Uma legislatura poderá possuir vários mandatos.
- O vereador e a legislatura vinculados deverão pertencer à mesma Câmara.
- O mandato não possuirá `camara_id`.
- A Câmara será determinada por meio da legislatura.
- A data de início será obrigatória.
- A data de término será opcional enquanto não houver encerramento registrado.
- O período do mandato deverá estar contido dentro do período da legislatura.
- A data de término não poderá ser anterior à data de início.
- Vereador e legislatura não poderão ser alterados após o cadastro do mandato.
- Não será permitido cadastrar mais de um mandato não arquivado para o mesmo vereador na mesma legislatura.
- O mandato não possuirá campo de status ou ativo.
- Partido, afastamentos, substituições e suplência serão tratados futuramente em estruturas próprias.
- O root poderá gerenciar mandatos de todas as Câmaras.
- Usuários não-root somente poderão gerenciar mandatos vinculados à própria Câmara.
- O escopo de Câmara será determinado por meio da legislatura do mandato.
- As autorizações serão centralizadas na `MandatoPolicy`.
- As permissões utilizarão o padrão `modulo:acao`.

## Permissões

- `mandatos:visualizar`
- `mandatos:criar`
- `mandatos:editar`
- `mandatos:excluir`
- `mandatos:restaurar`

## Exclusão lógica

- Mandatos serão arquivados por meio de `SoftDeletes`.
- A exclusão preencherá o campo `deleted_at` e preservará o registro no banco.
- Mandatos arquivados não aparecerão nas consultas e listagens comuns.
- Mandatos arquivados poderão ser consultados em uma listagem própria.
- Usuários autorizados poderão restaurar mandatos arquivados.
- Os relacionamentos históricos com vereadores e legislaturas serão preservados mesmo quando essas entidades estiverem arquivadas.
- Não haverá exclusão física de mandatos neste módulo.