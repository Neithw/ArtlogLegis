# Gerenciamento de mandatos

## Decisão

O sistema possuirá um módulo interno para gerenciamento de mandatos.
Cada mandato representará o vínculo institucional entre um vereador e uma legislatura.
O mandato possuirá período próprio de início e término.
A Câmara do mandato será determinada pela legislatura vinculada e não será armazenada diretamente em `mandatos`.
O histórico partidário será registrado separadamente por meio de filiações partidárias vinculadas ao mandato.
Afastamentos, substituições e suplência não serão armazenados diretamente em `mandatos`.

## Estrutura

- mandatos
- vereadores
- legislaturas
- filiacoes_partidarias
- partidos

A tabela `mandatos` possuirá:

- `vereador_id`
- `legislatura_id`
- `data_inicio`
- `data_fim`
- `deleted_at`

A tabela `filiacoes_partidarias` possuirá:

- `mandato_id`
- `partido_id`
- `data_inicio`
- `data_fim`
- `deleted_at`

## Regras

- Cada mandato pertencerá a um único vereador.
- Cada mandato pertencerá a uma única legislatura.
- Um vereador poderá possuir apenas um mandato por legislatura.
- Um vereador poderá possuir vários mandatos em legislaturas diferentes.
- Uma legislatura poderá possuir vários mandatos.
- A combinação entre `vereador_id` e `legislatura_id` será única no banco.
- Mandatos arquivados continuarão reservando essa combinação.
- O vereador e a legislatura vinculados deverão pertencer à mesma Câmara.
- O mandato não possuirá `camara_id`.
- A Câmara será determinada por meio da legislatura.
- Novos mandatos somente poderão ser cadastrados para Câmaras ativas.
- A data de início será obrigatória.
- A data de término será opcional enquanto não houver encerramento registrado.
- O período do mandato deverá estar contido dentro do período da legislatura.
- A data de término não poderá ser anterior à data de início.
- Vereador e legislatura não poderão ser alterados após o cadastro do mandato.
- O mandato não possuirá campo de status ou ativo.
- Afastamentos, substituições e suplência serão tratados futuramente em estruturas próprias.
- O root poderá gerenciar mandatos de todas as Câmaras.
- Usuários não-root somente poderão gerenciar mandatos vinculados à própria Câmara.
- O escopo de Câmara será determinado por meio da legislatura do mandato.
- As autorizações serão centralizadas na `MandatoPolicy`.
- As permissões utilizarão o padrão `modulo:acao`.

## Filiações partidárias

- Todo novo mandato será cadastrado com uma filiação partidária inicial.
- A filiação partidária pertencerá a um mandato e a um partido.
- O partido não será armazenado diretamente em `mandatos`.
- Um mandato poderá possuir várias filiações partidárias ao longo do tempo.
- As filiações representarão períodos históricos e não poderão se sobrepor.
- Uma troca partidária encerrará a filiação atual no dia anterior à troca e criará uma nova filiação na data informada.
- O mesmo partido poderá voltar a aparecer futuramente no mesmo mandato.
- A filiação mais recente representará o partido atual do mandato.
- O início da primeira filiação acompanhará o início do mandato.
- O término da última filiação acompanhará o término do mandato.
- Alterações intermediárias permanecerão preservadas como histórico.
- Somente partidos não arquivados poderão ser utilizados em novas filiações.
- Partidos arquivados continuarão visíveis no histórico existente.
- A troca partidária utilizará a mesma autorização de edição do mandato.

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
- Os relacionamentos históricos com vereadores e legislaturas serão preservados.
- As filiações partidárias permanecerão vinculadas ao mandato arquivado.
- Não haverá exclusão física de mandatos neste módulo.