# Artlog Legis

Sistema web para gerenciamento das atividades administrativas, parlamentares e legislativas de Câmaras Municipais.

O projeto busca centralizar informações institucionais, organizar o processo legislativo e estabelecer uma base segura para a futura disponibilização de dados públicos.

## Sobre o projeto

O Artlog Legis foi desenvolvido com uma arquitetura multi-Câmara, permitindo que diferentes instituições utilizem a mesma aplicação com isolamento de dados.

Com exceção do usuário root, que possui atuação global, cada usuário pertence a uma Câmara e possui permissões específicas de acesso.

A aplicação está sendo construída de forma modular, acompanhando o fluxo real de uma Câmara Municipal: desde sua estrutura administrativa e parlamentar até o cadastro, protocolo e tramitação de proposições legislativas.

## Principais recursos

- gerenciamento de Câmaras e usuários;
- controle de acesso baseado em papéis e permissões;
- isolamento de dados por Câmara;
- gerenciamento de legislaturas, vereadores, partidos e mandatos;
- cadastro de tipos de proposição;
- elaboração e protocolo de proposições legislativas;
- tramitação interna entre unidades administrativas;
- arquivamento lógico e restauração de registros;
- aplicação de regras de autorização por meio de Policies;
- gerenciamento do ciclo de vida das sessões legislativas;
- registro auditável das mudanças de situação das sessões;

## Arquitetura

O sistema adota uma estrutura multi-Câmara:

- usuários comuns acessam somente os dados da Câmara à qual pertencem;
- o usuário root possui acesso administrativo global;
- papéis representam conjuntos padrão de permissões;
- permissões individuais determinam o acesso efetivo de cada usuário;
- Policies centralizam as regras de autorização;
- o escopo institucional é aplicado nas consultas e validações.

## Tecnologias

- PHP
- Laravel
- Blade
- Tailwind CSS
- Alpine.js
- MySQL
- Docker

## Documentação

A documentação está organizada por domínio:

- [Arquitetura](docs/00-arquitetura) – RBAC, autorização e escopo de Câmara.
- [Administração](docs/01-administracao) – Câmaras e usuários.
- [Estrutura parlamentar](docs/02-estrutura-parlamentar) – Legislaturas, vereadores, partidos e mandatos.
- [Proposições](docs/03-proposicoes) – Tipos de proposição, elaboração, protocolo e tramitação.
- [Sessões](docs/04-sessoes) – Cadastro, ciclo de vida e histórico das sessões legislativas.

Cada módulo possui sua própria documentação, com decisões de modelagem, relacionamentos, regras de negócio e critérios de autorização.

## Estado atual

O projeto está em desenvolvimento.

A base administrativa e parlamentar está consolidada. Os fluxos de elaboração, protocolo e tramitação de proposições estão implementados, assim como o ciclo inicial das sessões legislativas, incluindo convocação, abertura, suspensão, retomada, encerramento, cancelamento e histórico de eventos.

## Objetivo

Construir uma plataforma capaz de acompanhar o ciclo legislativo municipal de forma organizada, segura e rastreável, servindo tanto à gestão interna quanto à futura transparência pública.