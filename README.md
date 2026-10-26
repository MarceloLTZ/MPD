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

## Banco de dados

O arquivo `database/banco.sql` contém todo o banco do projeto e cria:

- usuarios
- profissionais
- servicos
- solicitacoes
- mensagens
- pagamentos
- avaliacoes

Ele também insere categorias e profissionais de demonstração.

O banco **não é criado automaticamente no seu computador só por baixar o projeto**. Você precisa importar o arquivo SQL uma vez.

## Como rodar com XAMPP

1. Instale e abra o XAMPP.
2. Inicie `Apache` e `MySQL`.
3. Copie a pasta do projeto para:

```text
C:\xampp\htdocs\MPD_Nexus
```

4. Abra o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

5. Vá em **Importar** e escolha:

```text
database/banco.sql
```

6. Depois abra:

```text
http://localhost/MPD_Nexus/
```

## Conexão MySQL

O arquivo `conexao.php` está configurado para o padrão do XAMPP:

```php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "mpd_nexus";
```

Se seu MySQL tiver senha, altere somente esse arquivo.

## O que funciona

- cadastro
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
