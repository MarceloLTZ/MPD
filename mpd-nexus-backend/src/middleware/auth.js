const jwt = require('jsonwebtoken');
const { pool } = require('../config/database');

const protect = async (req, res, next) => {
  let token;

  if (
    req.headers.authorization &&
    req.headers.authorization.startsWith('Bearer ')
  ) {
    token = req.headers.authorization.split(' ')[1];
  }

  if (!token) {
    return res.status(401).json({
      success: false,
      message: 'Not authorized, no token'
    });
  }

  try {
    const decoded = jwt.verify(
      token,
      process.env.JWT_SECRET
    );

    if (!decoded.id || !decoded.userType) {
      return res.status(401).json({
        success: false,
        message: 'Invalid token'
      });
    }

    if (decoded.userType === 'professional') {
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
        WHERE idProfissional = ?
        LIMIT 1`,
        [decoded.id]
      );

      if (rows.length === 0) {
        return res.status(401).json({
          success: false,
          message: 'User not found'
        });
      }

      const user = rows[0];

      req.user = {
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

    } else if (decoded.userType === 'client') {
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
        WHERE ID_Clientes = ?
        LIMIT 1`,
        [decoded.id]
      );

      if (rows.length === 0 || !rows[0].Ativo) {
        return res.status(401).json({
          success: false,
          message: 'User not found'
        });
      }

      const user = rows[0];

      req.user = {
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

    } else {
      return res.status(401).json({
        success: false,
        message: 'Invalid user type'
      });
    }

    next();

  } catch (error) {
    console.error(error);

    return res.status(401).json({
      success: false,
      message: 'Not authorized, token failed'
    });
  }
};

module.exports = { protect };
