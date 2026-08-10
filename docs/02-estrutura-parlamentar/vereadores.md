# Gerenciamento de vereadores

## Decisão

O sistema possuirá um módulo interno para gerenciamento de vereadores.
Cada vereador representará a pessoa parlamentar cadastrada institucionalmente em uma Câmara.
A conta de acesso será representada separadamente pela entidade User.
O vínculo entre vereador e usuário será opcional.
Dados de legislatura, partido, período de exercício, afastamentos e substituições não serão armazenados diretamente em `vereadores`.
O vínculo temporal entre vereador e legislatura será realizado por meio da entidade Mandato.
O histórico partidário será registrado por meio de filiações partidárias vinculadas ao mandato.

## Estrutura

- vereadores
- camaras
- users

A tabela `vereadores` possuirá inicialmente:

- `camara_id`
- `user_id`
- `nome`
- `nome_parlamentar`
- `email_institucional`
- `telefone_institucional`
- `biografia`
- `foto_path`
- `deleted_at`

## Regras

- Uma Câmara poderá possuir vários vereadores.
- Cada vereador pertencerá a uma única Câmara.
- Um vereador poderá possuir uma conta de acesso vinculada.
- O vínculo com uma conta de acesso será opcional.
- Uma conta de acesso poderá estar vinculada a apenas um vereador.
- A conta vinculada deverá pertencer à mesma Câmara do vereador.
- A Câmara vinculada não poderá ser alterada após o cadastro.
- O vereador poderá existir sem uma conta de acesso.
- A conta de acesso poderá existir sem um vereador vinculado.
- Dados temporais de exercício parlamentar não serão armazenados diretamente no vereador.
- Legislatura e período de exercício serão tratados pela entidade Mandato.
- O vínculo partidário será histórico e realizado por meio das filiações partidárias do mandato.
- Afastamentos e substituições terão estruturas próprias futuramente.
- O root poderá gerenciar vereadores de todas as Câmaras.
- Usuários não-root somente poderão gerenciar vereadores da própria Câmara.
- As autorizações serão centralizadas na `VereadorPolicy`.
- As permissões utilizarão o padrão `modulo:acao`.

## Permissões

- `vereadores:visualizar`
- `vereadores:criar`
- `vereadores:editar`
- `vereadores:excluir`

## Exclusão lógica

- Vereadores serão excluídos logicamente por meio de `SoftDeletes`.
- A exclusão preencherá o campo `deleted_at` e preservará o registro no banco.
- Vereadores excluídos não aparecerão nas consultas e listagens comuns.
- A conta vinculada a um vereador excluído continuará reservada.
- A exclusão será impedida quando existirem mandatos vinculados, inclusive arquivados.