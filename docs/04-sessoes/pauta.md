# Pauta das sessões

## Decisão

Cada sessão possuirá uma única pauta, formada por itens vinculados a proposições legislativas.

A pauta será representada pelo Model `ItemPauta`, pois o vínculo possui informações e comportamentos próprios, como ordem, situação e usuário responsável pela inclusão.

A pauta poderá ser alterada somente enquanto a sessão estiver em preparação. A convocação oficializa e congela sua composição e ordem.

## Estrutura

A tabela `itens_pauta` possui:

- `sessao_id`
- `proposicao_id`
- `incluido_por_id`
- `ordem`
- `situacao`
- `timestamps`

As combinações entre sessão e proposição e entre sessão e ordem são únicas.
Todo item será criado inicialmente com a situação `pendente`. As futuras mudanças de situação serão controladas pelo fluxo operacional da sessão e da votação.
Itens de pauta não utilizam exclusão lógica. Durante a preparação, sua remoção é definitiva. Após a convocação, a pauta não poderá ser alterada.

## Relacionamentos

- Uma sessão possui vários itens de pauta.
- Um item de pauta pertence a uma sessão.
- Uma proposição pode aparecer em itens de pautas diferentes.
- Um item de pauta pertence a uma proposição.
- Cada item registra o usuário responsável pela inclusão.
- Um usuário pode ter incluído vários itens de pauta.

Embora sessões e proposições formem conceitualmente uma relação muitos-para-muitos, `ItemPauta` é tratado como uma entidade própria devido aos atributos e comportamentos do vínculo.

## Elegibilidade

Uma proposição somente poderá ser incluída quando:

- pertencer à mesma Câmara da sessão;
- pertencer à mesma legislatura da sessão;
- estiver protocolada;
- não estiver arquivada;
- ainda não estiver incluída na mesma sessão.

A mesma proposição poderá aparecer em sessões diferentes.

## Operações

Durante a preparação da sessão, usuários autorizados poderão:

- incluir uma proposição;
- remover uma proposição;
- mover um item para cima;
- mover um item para baixo.

Novos itens são adicionados ao final da pauta.

Ao remover um item, as ordens posteriores são reorganizadas para manter a sequência contínua.

A movimentação troca a ordem entre itens vizinhos. Uma ordem temporária é utilizada para evitar conflito com a restrição de unicidade entre sessão e ordem.

## Concorrência

As operações utilizam transações e bloqueio pessimista com `lockForUpdate()`.

A sessão é bloqueada antes da alteração para proteger:

- a situação atual;
- a verificação de duplicidade;
- o cálculo da próxima ordem;
- a remoção e reorganização;
- a troca de posições.

As restrições únicas do banco permanecem como proteção final.

## Autorização

O gerenciamento utiliza a permissão:

- `sessoes:gerenciar-pauta`

A `SessaoPolicy` combina:

- permissão do usuário;
- escopo da Câmara;
- situação da sessão.

A situação também é validada dentro da transação, garantindo que as regras do domínio sejam respeitadas inclusive para o usuário root.

## Interface

A pauta é exibida na página de detalhes da sessão.

Enquanto a sessão estiver em preparação, a interface permite:

- selecionar proposições elegíveis;
- remover itens;
- alterar sua ordem.

Após a convocação, a pauta permanece disponível somente para consulta.

A reordenação utiliza Alpine.js e `fetch()` para atualizar a interface sem recarregar a página. O Laravel continua responsável por validar, persistir e devolver a ordem oficial.

Os formulários tradicionais permanecem como fallback quando o JavaScript não estiver disponível.
