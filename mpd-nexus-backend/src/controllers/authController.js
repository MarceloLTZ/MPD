const { validationResult } = require('express-validator');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { pool } = require('../config/database');

const generateToken = (id, userType) => {
  return jwt.sign(
    { id, userType },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRE }
  );
};

const formatUser = (user, userType) => {
  if (userType === 'professional') {
    return {
      id: user.idProfissional,
      name: user.Nome,
      email: user.Email,
      phone: user.Telefone,
      cpf: user.CPF_CNPJ,
      userType: 'professional',
      isProfessional: true,
      profileImage: user.FotoPerfilUrl,
      description: user.BioDescricao,
      rating: user.NotaMedia,
      available: Boolean(user.Disponivel),
      status: user.StatusConta
    };
  }

  return {
    id: user.ID_Clientes,
    name: user.Nome,
    email: user.Email,
    phone: user.Telefone,
    cpf: user.CPF,
    userType: 'client',
    isProfessional: false,
    profileImage: user.FotoPerfilUrl,
    active: Boolean(user.Ativo)
  };
};

exports.register = async (req, res) => {
  try {
    const errors = validationResult(req);

    if (!errors.isEmpty()) {
      return res.status(400).json({
        success: false,
        errors: errors.array()
      });
    }

    const {
      name,
      email,
      password,
      phone,
      cpf,
      userType = 'client',
      description
    } = req.body;

    const normalizedEmail = email.toLowerCase().trim();
    const normalizedType =
      userType === 'professional' ? 'professional' : 'client';

    const [clientes] = await pool.execute(
      'SELECT ID_Clientes FROM Clientes WHERE Email = ? OR CPF = ? LIMIT 1',
      [normalizedEmail, cpf]
    );

    const [profissionais] = await pool.execute(
      'SELECT idProfissional FROM Profissional WHERE Email = ? OR CPF_CNPJ = ? LIMIT 1',
      [normalizedEmail, cpf]
    );

    if (clientes.length > 0 || profissionais.length > 0) {
      return res.status(400).json({
        success: false,
        message: 'Email ou CPF/CNPJ já cadastrado'
      });
    }

    const senhaHash = await bcrypt.hash(password, 10);

    let id;
    let user;

    if (normalizedType === 'professional') {
      const [result] = await pool.execute(
        `INSERT INTO Profissional
        (Nome, CPF_CNPJ, Email, SenhaHash, Telefone, BioDescricao)
        VALUES (?, ?, ?, ?, ?, ?)`,
        [
          name,
          cpf,
          normalizedEmail,
          senhaHash,
          phone,
          description || null
        ]
      );

      id = result.insertId;

      const [rows] = await pool.execute(
        `SELECT
          idProfissional,
          Nome,
          CPF_CNPJ,
          Email,
          Telefone,
          FotoPerfilUrl,
          BioDescricao,
          NotaMedia,
          Disponivel,
          StatusConta
        FROM Profissional
        WHERE idProfissional = ?`,
        [id]
      );

      user = formatUser(rows[0], 'professional');
    } else {
      const [result] = await pool.execute(
        `INSERT INTO Clientes
        (Nome, CPF, Email, SenhaHash, Telefone)
        VALUES (?, ?, ?, ?, ?)`,
        [
          name,
          cpf,
          normalizedEmail,
          senhaHash,
          phone
        ]
      );

      id = result.insertId;

      const [rows] = await pool.execute(
        `SELECT
          ID_Clientes,
          Nome,
          CPF,
          Email,
          Telefone,
          FotoPerfilUrl,
          Ativo
        FROM Clientes
        WHERE ID_Clientes = ?`,
        [id]
      );

      user = formatUser(rows[0], 'client');
    }

    const token = generateToken(id, normalizedType);

    return res.status(201).json({
      success: true,
      token,
      user
    });

  } catch (error) {
    console.error(error);

    return res.status(500).json({
      success: false,
      message: 'Server error',
      error: error.message
    });
  }
};

exports.login = async (req, res) => {
  try {
    const errors = validationResult(req);

    if (!errors.isEmpty()) {
      return res.status(400).json({
        success: false,
        errors: errors.array()
      });
    }

    const { email, password } = req.body;
    const normalizedEmail = email.toLowerCase().trim();

    let user;
    let userType;

    const [clientes] = await pool.execute(
      'SELECT * FROM Clientes WHERE Email = ? LIMIT 1',
      [normalizedEmail]
    );

    if (clientes.length > 0) {
      user = clientes[0];
      userType = 'client';

      if (!user.Ativo) {
        return res.status(403).json({
          success: false,
          message: 'Conta desativada'
        });
      }
    } else {
      const [profissionais] = await pool.execute(
        'SELECT * FROM Profissional WHERE Email = ? LIMIT 1',
        [normalizedEmail]
      );

      if (profissionais.length === 0) {
        return res.status(401).json({
          success: false,
          message: 'Email ou senha inválidos'
        });
      }

      user = profissionais[0];
      userType = 'professional';

      if (
        user.StatusConta === 'Suspenso' ||
        user.StatusConta === 'Inativo'
      ) {
        return res.status(403).json({
          success: false,
          message: 'Conta indisponível'
        });
      }
    }

    const passwordMatch = await bcrypt.compare(
      password,
      user.SenhaHash
    );

    if (!passwordMatch) {
      return res.status(401).json({
        success: false,
        message: 'Email ou senha inválidos'
      });
    }

    const id =
      userType === 'professional'
        ? user.idProfissional
        : user.ID_Clientes;

    const token = generateToken(id, userType);

    return res.status(200).json({
      success: true,
      token,
      user: formatUser(user, userType)
    });

  } catch (error) {
    console.error(error);

    return res.status(500).json({
      success: false,
      message: 'Server error',
      error: error.message
    });
  }
};

exports.getMe = async (req, res) => {
  return res.status(200).json({
    success: true,
    user: req.user
  });
};
