# MPD Nexus - versão simples para TCC

Esta versão foi feita para ser fácil de entender e explicar na banca.

## Tecnologias usadas

- PHP puro
- MySQL
- HTML
- CSS
- JavaScript

Não usa Flask, Node.js, MongoDB, React, Bootstrap ou framework PHP.

## Estrutura

```text
MPD_Nexus_TCC_PHP_SIMPLES/
├── index.php
├── login.php
├── cadastro.php
├── logout.php
├── profissionais.php
├── profissional.php
├── solicitar.php
├── meus_servicos.php
├── chat.php
├── mapa.php
├── pagamento.php
├── comprovante.php
├── feedback.php
├── config.php          (dados do banco, contatos e imagens)
├── conexao.php
├── funcoes.php
├── includes/
│   ├── inicio_html.php
│   ├── cabecalho.php
│   └── rodape.php
├── css/
│   └── style.css
├── js/
│   └── script.js
└── database/
    └── banco.sql
```

## Banco de dados na nuvem (MySQL) - sem recriar usuários

As linguagens continuam as mesmas (PHP, MySQL, HTML, CSS, JS). O que muda é só **onde** o MySQL fica.

1. Crie um MySQL gratuito em um provedor (ex.: Aiven, TiDB Cloud ou Clever Cloud).
2. No painel do provedor, copie: host, porta, usuário, senha e nome do banco.
3. Importe `database/banco_nuvem.sql` **uma única vez** (para MySQL local/XAMPP use o `database/banco.sql`, que também cria o banco `mpd_nexus`) (pelo Adminer/phpMyAdmin do provedor ou pelo comando `mysql`).
   O arquivo não apaga nada: pode ser importado de novo sem perder usuários.
4. Informe os dados do banco de um destes jeitos:
   - **Variáveis de ambiente** (recomendado, a senha não vai para o GitHub): `DB_HOST`, `DB_PORTA`, `DB_USUARIO`, `DB_SENHA`, `DB_BANCO` e `DB_SSL=1`.
   - **Direto no `config.php`**, trocando os valores e `'ssl'` por `true` (só se o repositório for privado).

Pronto: os usuários cadastrados ficam salvos na nuvem, em qualquer computador que rodar o site.
Para usar o MySQL do XAMPP, deixe `host` como `localhost` e `ssl` como `false`.

## Imagens por link

Nenhuma imagem fica salva no projeto.
- Foto de capa e foto dos serviços: links no `config.php` e na coluna `imagem_url` da tabela `servicos`.
- Foto dos profissionais: coluna `foto_url` da tabela `profissionais`.
Se um link quebrar, o site mostra um avatar com as iniciais, sem erro.

## Mapa real (como nos apps de transporte)

A página **Localização** usa Leaflet + OpenStreetMap (gratuitos, sem chave de API): mapa real com os profissionais, sua posição, **Traçar rota** com distância e tempo de carro (OSRM) e botão para abrir a rota no Google Maps. Na tela **Solicitar serviço**, o botão "Usar minha localização atual" preenche o endereço. O navegador pede permissão de localização (em `localhost` ou `https` funciona).

## Conta e segurança

- Senha guardada com `password_hash()` (nunca em texto puro) e campo de confirmar senha.
- Confirmação de e-mail, "esqueci minha senha" e reenvio de link (links de uso único; o banco guarda só o hash do token).
- "Manter conectado" (30 dias) e "Mostrar senha".
- E-mail: deixe `host` vazio em `config.php` para o **modo teste** (o link aparece na tela). Para enviar de verdade, configure o SMTP (ex.: Gmail com senha de app).

## O que funciona

- cadastro com confirmação de senha
- login e logout
- senha usando `password_hash()`
- pesquisa de profissionais
- perfil do profissional
- solicitação de serviço
- histórico de serviços
- chat salvo no MySQL
- localização do navegador e cálculo aproximado de distância
- pagamento demonstrativo
- comprovante demonstrativo
- avaliação do profissional

## Sobre o pagamento

O pagamento é apenas uma simulação para o TCC. O sistema não pede nem salva número de cartão.

## Sobre o chat

Para a demonstração, depois que o cliente envia uma mensagem, o sistema cria uma resposta automática do profissional. Isso está indicado por comentário dentro de `chat.php` e pode ser explicado na banca como uma simulação do fluxo.

## CSS

O CSS foi deixado propositalmente simples:

```css
.card-profissional {
    padding: 20px;
    background-color: #18181d;
    border: 1px solid #25252c;
    border-radius: 14px;
}
```

Não existem `:root`, variáveis CSS ou código minificado.
