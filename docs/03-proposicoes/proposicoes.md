# Gerenciamento de proposições

## Decisão

O sistema possuirá um módulo interno para gerenciamento de proposições.
Nesta etapa, cada proposição representará o cadastro inicial de um rascunho legislativo.
Cada proposição pertencerá a uma Câmara, a uma legislatura e a um tipo de proposição.
A autoria principal será vinculada a um mandato, enquanto o usuário responsável pelo cadastro será registrado separadamente.
O fluxo de protocolação será documentado separadamente.
Tramitação, documentos, assinaturas e votações serão implementados em etapas futuras.
Proposições poderão ser arquivadas e restauradas quando autorizado.

## Estrutura

- proposicoes
- camaras
- legislaturas
- tipos de proposição
- mandatos
- users

A tabela `proposicoes` possuirá inicialmente:

- `camara_id`
- `legislatura_id`
- `tipo_proposicao_id`
- `autor_mandato_id`
- `criado_por_id`
- `ementa`
- `texto_integral`
- `assunto`
- `area_tematica`
- `palavras_chave`
- `situacao`
- `deleted_at`

## Regras

- Cada proposição pertencerá a uma única Câmara.
- A legislatura deverá pertencer à mesma Câmara da proposição.
- O tipo de proposição deverá pertencer à mesma Câmara da proposição.
- O mandato do autor deverá pertencer à mesma Câmara e à mesma legislatura da proposição.
- O autor principal representará o vereador autor da proposição por meio de seu mandato.
- O usuário autenticado responsável pelo cadastro será definido no back-end.
- Toda proposição será cadastrada inicialmente com a situação `rascunho`.
- A situação não poderá ser escolhida manualmente nesta etapa.
- Proposições arquivadas poderão ser restauradas quando autorizado.
- Vínculos históricos arquivados serão preservados e continuarão disponíveis para consulta.
- Usuários não-root poderão acessar somente proposições da própria Câmara.
- O root possuirá acesso global ao módulo.
- Somente proposições em rascunho poderão ser editadas ou arquivadas.
- A protocolação e sua numeração serão tratadas em fluxo próprio.
- Tramitação, documentos, assinaturas e votações serão tratados futuramente.
- O mandato do autor deverá estar vigente na data da criação, atualização e protocolação da proposição.
- Um mandato será considerado vigente quando sua data de início for anterior ou igual à data de referência e sua data de término for nula ou posterior ou igual à data de referência.
- Mandatos futuros, encerrados ou arquivados não poderão ser atribuídos a novas proposições.
- Rascunhos vinculados a mandatos que deixaram de estar vigentes deverão receber um autor elegível antes de serem atualizados ou protocolados.
- Proposições já protocoladas preservarão sua autoria histórica mesmo após o encerramento ou arquivamento do mandato.

## Permissões

- `proposicoes:visualizar`
- `proposicoes:criar`
- `proposicoes:editar`
- `proposicoes:excluir`
- `proposicoes:restaurar`