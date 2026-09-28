# MPD Nexus — TCC

Versão do MPD Nexus criada para o TCC com **Python + Flask + MySQL**, mantendo o código simples para estudo e apresentação.

## Visual

A interface foi aproximada do protótipo `index site.html`: tema escuro, laranja como destaque, header com busca, cards de categorias e profissionais, autenticação, chat, mapa, pagamento e feedback com a mesma linguagem visual.

## O que funciona

- Home responsiva
- Cadastro e login com senha em hash
- Sessão de usuário
- Busca e filtro de profissionais pelo MySQL
- Perfil e avaliações
- Mapa com profissionais e localização do navegador
- Solicitação de serviço
- Histórico de serviços
- Chat simples persistido no MySQL
- Pagamento demonstrativo (PIX/crédito/débito)
- Comprovante
- Feedback com atualização da nota média

> O pagamento é propositalmente simulado. O projeto não recebe nem armazena dados reais de cartão.

## Tecnologias

- Python 3
- Flask
- MySQL
- HTML + Jinja
- CSS
- JavaScript
- Bootstrap Icons
- Leaflet + OpenStreetMap para o mapa

## Como rodar

1. Instale Python 3.11+ e MySQL.
2. No MySQL Workbench, execute `database/main.sql`.
3. Copie `.env.example` para `.env` e ajuste usuário/senha do MySQL.
4. Abra o terminal na pasta do projeto.
5. Crie o ambiente virtual:

```bash
python -m venv .venv
```

6. Ative no Windows:

```bash
.venv\Scripts\activate
```

7. Instale as dependências:

```bash
pip install -r requirements.txt
```

8. Rode:

```bash
python app.py
```

9. Acesse `http://127.0.0.1:5000`.

## Estrutura

- `app.py`: rotas e regras principais do sistema.
- `templates/`: páginas HTML usando Jinja.
- `static/css/style.css`: identidade visual.
- `static/js/main.js`: menu responsivo e comportamentos simples.
- `static/js/mapa.js`: mapa e GPS do navegador.
- `database/main.sql`: banco simplificado e dados de demonstração.

## Fluxo do protótipo

Home → Cadastro/Login → Profissionais/Mapa → Perfil → Solicitação/Chat → Pagamento → Comprovante → Feedback.

## Escopo acadêmico

Esta versão demonstra a integração entre interface, backend e banco de dados. Recursos externos que exigiriam serviços de terceiros — como gateway de pagamento real, verificação documental e rastreamento em tempo real — são tratados como evolução futura.
