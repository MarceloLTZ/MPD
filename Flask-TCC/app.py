import os
from datetime import datetime
from functools import wraps

from dotenv import load_dotenv
from flask import Flask, flash, redirect, render_template, request, session, url_for
import mysql.connector
from mysql.connector import Error
from werkzeug.security import check_password_hash, generate_password_hash

load_dotenv()

app = Flask(__name__)
app.secret_key = os.getenv("FLASK_SECRET_KEY", "dev-mpd-nexus")

DB_CONFIG = {
    "host": os.getenv("DB_HOST", "localhost"),
    "port": int(os.getenv("DB_PORT", "3306")),
    "user": os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME", "mpd_nexus"),
}


def get_db():
    return mysql.connector.connect(**DB_CONFIG)


def query(sql, params=(), fetchone=False, commit=False):
    conn = get_db()
    cur = conn.cursor(dictionary=True)
    try:
        cur.execute(sql, params)
        if commit:
            conn.commit()
            return cur.lastrowid
        return cur.fetchone() if fetchone else cur.fetchall()
    finally:
        cur.close()
        conn.close()


def login_required(view):
    @wraps(view)
    def wrapped(*args, **kwargs):
        if not session.get("user_id"):
            flash("Entre na sua conta para continuar.", "warning")
            return redirect(url_for("login"))
        return view(*args, **kwargs)
    return wrapped


@app.context_processor
def inject_globals():
    return {"ano": datetime.now().year}


@app.route("/")
def home():
    profissionais = query(
        """
        SELECT p.id, p.nome, p.especialidade, p.nota, p.cidade, p.bio
        FROM profissionais p
        WHERE p.ativo = 1
        ORDER BY p.nota DESC
        LIMIT 4
        """
    )
    servicos = query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome LIMIT 8")
    return render_template("index.html", profissionais=profissionais, servicos=servicos)


@app.route("/cadastro", methods=["GET", "POST"])
def cadastro():
    if request.method == "POST":
        nome = request.form.get("nome", "").strip()
        email = request.form.get("email", "").strip().lower()
        senha = request.form.get("senha", "")
        tipo = request.form.get("tipo", "cliente")

        if not nome or not email or len(senha) < 6:
            flash("Preencha os campos e use uma senha com pelo menos 6 caracteres.", "danger")
            return render_template("cadastro.html")

        existente = query("SELECT id FROM usuarios WHERE email=%s", (email,), fetchone=True)
        if existente:
            flash("Este e-mail já está cadastrado.", "warning")
            return render_template("cadastro.html")

        user_id = query(
            "INSERT INTO usuarios (nome,email,senha_hash,tipo) VALUES (%s,%s,%s,%s)",
            (nome, email, generate_password_hash(senha), tipo),
            commit=True,
        )
        session["user_id"] = user_id
        session["user_name"] = nome
        session["user_type"] = tipo
        flash("Conta criada com sucesso!", "success")
        return redirect(url_for("home"))

    return render_template("cadastro.html")


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        email = request.form.get("email", "").strip().lower()
        senha = request.form.get("senha", "")
        usuario = query("SELECT * FROM usuarios WHERE email=%s", (email,), fetchone=True)

        if usuario and check_password_hash(usuario["senha_hash"], senha):
            session["user_id"] = usuario["id"]
            session["user_name"] = usuario["nome"]
            session["user_type"] = usuario["tipo"]
            flash(f"Bem-vindo, {usuario['nome']}!", "success")
            return redirect(url_for("home"))

        flash("E-mail ou senha inválidos.", "danger")
    return render_template("login.html")


@app.route("/logout")
def logout():
    session.clear()
    flash("Você saiu da conta.", "info")
    return redirect(url_for("home"))


@app.route("/profissionais")
def profissionais():
    especialidade = request.args.get("especialidade", "").strip()
    cidade = request.args.get("cidade", "").strip()
    busca = request.args.get("q", "").strip()
    sql = "SELECT * FROM profissionais WHERE ativo=1"
    params = []
    if busca:
        sql += " AND (nome LIKE %s OR especialidade LIKE %s)"
        params.extend([f"%{busca}%", f"%{busca}%"])
    if especialidade:
        sql += " AND especialidade LIKE %s"
        params.append(f"%{especialidade}%")
    if cidade:
        sql += " AND cidade LIKE %s"
        params.append(f"%{cidade}%")
    sql += " ORDER BY nota DESC, nome"
    lista = query(sql, tuple(params))
    servicos = query("SELECT * FROM servicos WHERE ativo=1 ORDER BY nome")
    return render_template("profissionais.html", profissionais=lista, servicos=servicos)


@app.route("/profissional/<int:profissional_id>")
def profissional(profissional_id):
    p = query("SELECT * FROM profissionais WHERE id=%s AND ativo=1", (profissional_id,), fetchone=True)
    if not p:
        flash("Profissional não encontrado.", "warning")
        return redirect(url_for("profissionais"))
    avaliacoes = query(
        """SELECT a.*, u.nome AS cliente_nome
           FROM avaliacoes a JOIN usuarios u ON u.id=a.usuario_id
           WHERE a.profissional_id=%s ORDER BY a.criado_em DESC LIMIT 6""",
        (profissional_id,),
    )
    return render_template("profissional.html", profissional=p, avaliacoes=avaliacoes)


@app.route("/mapa")
def mapa():
    lista = query("SELECT id,nome,especialidade,cidade,nota,latitude,longitude FROM profissionais WHERE ativo=1")
    return render_template("mapa.html", profissionais=lista)


@app.route("/solicitar/<int:profissional_id>", methods=["GET", "POST"])
@login_required
def solicitar(profissional_id):
    p = query("SELECT * FROM profissionais WHERE id=%s", (profissional_id,), fetchone=True)
    servicos = query("SELECT * FROM servicos WHERE ativo=1 ORDER BY nome")
    if not p:
        return redirect(url_for("profissionais"))

    if request.method == "POST":
        descricao = request.form.get("descricao", "").strip()
        endereco = request.form.get("endereco", "").strip()
        servico_id = request.form.get("servico_id")
        data_desejada = request.form.get("data_desejada") or None
        if not descricao or not endereco:
            flash("Descreva o serviço e informe o endereço.", "danger")
        else:
            solicitacao_id = query(
                """INSERT INTO solicitacoes
                   (usuario_id, profissional_id, servico_id, descricao, endereco, data_desejada, status, valor)
                   VALUES (%s,%s,%s,%s,%s,%s,'aguardando',NULL)""",
                (session["user_id"], profissional_id, servico_id or None, descricao, endereco, data_desejada),
                commit=True,
            )
            flash("Solicitação enviada. Para o TCC, o orçamento pode ser confirmado pelo profissional no banco.", "success")
            return redirect(url_for("detalhe_solicitacao", solicitacao_id=solicitacao_id))
    return render_template("solicitar.html", profissional=p, servicos=servicos)


@app.route("/meus-servicos")
@login_required
def meus_servicos():
    itens = query(
        """SELECT s.*, p.nome AS profissional_nome, sv.nome AS servico_nome
           FROM solicitacoes s
           JOIN profissionais p ON p.id=s.profissional_id
           LEFT JOIN servicos sv ON sv.id=s.servico_id
           WHERE s.usuario_id=%s ORDER BY s.criado_em DESC""",
        (session["user_id"],),
    )
    return render_template("meus_servicos.html", itens=itens)


@app.route("/solicitacao/<int:solicitacao_id>")
@login_required
def detalhe_solicitacao(solicitacao_id):
    item = query(
        """SELECT s.*, p.nome AS profissional_nome, p.especialidade, sv.nome AS servico_nome
           FROM solicitacoes s JOIN profissionais p ON p.id=s.profissional_id
           LEFT JOIN servicos sv ON sv.id=s.servico_id
           WHERE s.id=%s AND s.usuario_id=%s""",
        (solicitacao_id, session["user_id"]),
        fetchone=True,
    )
    if not item:
        flash("Serviço não encontrado.", "warning")
        return redirect(url_for("meus_servicos"))
    return render_template("solicitacao.html", item=item)


@app.route("/chat/<int:profissional_id>", methods=["GET", "POST"])
@login_required
def chat(profissional_id):
    p = query("SELECT * FROM profissionais WHERE id=%s", (profissional_id,), fetchone=True)
    if not p:
        return redirect(url_for("profissionais"))
    if request.method == "POST":
        mensagem = request.form.get("mensagem", "").strip()
        if mensagem:
            query(
                "INSERT INTO mensagens (usuario_id,profissional_id,autor,mensagem) VALUES (%s,%s,'cliente',%s)",
                (session["user_id"], profissional_id, mensagem), commit=True
            )
            # resposta simulada, simples para demonstração do TCC
            query(
                "INSERT INTO mensagens (usuario_id,profissional_id,autor,mensagem) VALUES (%s,%s,'profissional',%s)",
                (session["user_id"], profissional_id, "Recebi sua mensagem. Posso analisar o serviço e combinar o melhor horário."),
                commit=True
            )
        return redirect(url_for("chat", profissional_id=profissional_id))
    mensagens = query(
        "SELECT * FROM mensagens WHERE usuario_id=%s AND profissional_id=%s ORDER BY criado_em, id",
        (session["user_id"], profissional_id),
    )
    return render_template("chat.html", profissional=p, mensagens=mensagens)


@app.route("/pagamento/<int:solicitacao_id>", methods=["GET", "POST"])
@login_required
def pagamento(solicitacao_id):
    item = query(
        """SELECT s.*, p.nome AS profissional_nome, sv.nome AS servico_nome
           FROM solicitacoes s JOIN profissionais p ON p.id=s.profissional_id
           LEFT JOIN servicos sv ON sv.id=s.servico_id
           WHERE s.id=%s AND s.usuario_id=%s""",
        (solicitacao_id, session["user_id"]), fetchone=True
    )
    if not item:
        return redirect(url_for("meus_servicos"))
    if request.method == "POST":
        metodo = request.form.get("metodo", "pix")
        valor = item["valor"] or 249.90
        pagamento_id = query(
            "INSERT INTO pagamentos (solicitacao_id,metodo,valor,status) VALUES (%s,%s,%s,'aprovado')",
            (solicitacao_id, metodo, valor), commit=True
        )
        query("UPDATE solicitacoes SET status='pago', valor=%s WHERE id=%s", (valor, solicitacao_id), commit=True)
        return redirect(url_for("comprovante", pagamento_id=pagamento_id))
    return render_template("pagamento.html", item=item)


@app.route("/comprovante/<int:pagamento_id>")
@login_required
def comprovante(pagamento_id):
    pg = query(
        """SELECT pg.*, s.usuario_id, p.nome AS profissional_nome, sv.nome AS servico_nome
           FROM pagamentos pg JOIN solicitacoes s ON s.id=pg.solicitacao_id
           JOIN profissionais p ON p.id=s.profissional_id
           LEFT JOIN servicos sv ON sv.id=s.servico_id
           WHERE pg.id=%s AND s.usuario_id=%s""",
        (pagamento_id, session["user_id"]), fetchone=True
    )
    if not pg:
        return redirect(url_for("meus_servicos"))
    return render_template("comprovante.html", pagamento=pg)


@app.route("/feedback/<int:profissional_id>", methods=["GET", "POST"])
@login_required
def feedback(profissional_id):
    p = query("SELECT * FROM profissionais WHERE id=%s", (profissional_id,), fetchone=True)
    if not p:
        return redirect(url_for("profissionais"))
    if request.method == "POST":
        nota = int(request.form.get("nota", "5"))
        comentario = request.form.get("comentario", "").strip()
        nota = max(1, min(5, nota))
        query(
            "INSERT INTO avaliacoes (usuario_id,profissional_id,nota,comentario) VALUES (%s,%s,%s,%s)",
            (session["user_id"], profissional_id, nota, comentario), commit=True
        )
        media = query("SELECT ROUND(AVG(nota),1) media FROM avaliacoes WHERE profissional_id=%s", (profissional_id,), fetchone=True)
        query("UPDATE profissionais SET nota=%s WHERE id=%s", (media["media"] or 5, profissional_id), commit=True)
        flash("Obrigado pela avaliação!", "success")
        return redirect(url_for("profissional", profissional_id=profissional_id))
    return render_template("feedback.html", profissional=p)


@app.errorhandler(Error)
def database_error(error):
    return render_template("erro.html", mensagem="Não foi possível conectar ao MySQL. Confira o .env e importe database/main.sql."), 500


if __name__ == "__main__":
    app.run(debug=True)
