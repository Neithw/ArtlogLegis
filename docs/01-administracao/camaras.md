# Gerenciamento de câmaras

## Decisão

O sistema possuirá um módulo interno para gerenciamento das Câmaras atendidas.
Cada Câmara representará uma instituição independente dentro do sistema.
As Câmaras poderão ser ativadas, desativadas e arquivadas logicamente.
O usuário root será global e poderá gerenciar todas as Câmaras.

As regras gerais de isolamento dos dados estão documentadas no arquivo `escopo-de-camara`.

## Estrutura

- camaras

A tabela `camaras` possuirá:

- `nome`
- `cnpj`
- `ativo`
- `deleted_at`

## Regras

- O nome da Câmara será obrigatório.
- O CNPJ será opcional.
- Quando informado, o CNPJ deverá possuir 14 dígitos e ser único.
- A situação da Câmara será armazenada no campo `ativo`.
- Uma Câmara inativa continuará registrada no sistema.
- A desativação não será equivalente ao arquivamento.
- O root poderá visualizar e gerenciar todas as Câmaras.
- As autorizações serão centralizadas na `CamaraPolicy`.
- Os dados institucionais relacionados não serão armazenados diretamente em `camaras`.

## Permissões

- `camaras:visualizar`
- `camaras:criar`
- `camaras:editar`
- `camaras:excluir`

## Exclusão lógica

- Câmaras serão arquivadas por meio de `SoftDeletes`.
- O arquivamento preencherá o campo `deleted_at` e preservará o registro no banco.
- Câmaras arquivadas não aparecerão nas consultas e listagens comuns.
- O CNPJ de uma Câmara arquivada continuará reservado.
- O arquivamento não excluirá automaticamente os registros vinculados.
- Não haverá exclusão física pelo fluxo administrativo comum.