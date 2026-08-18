const { pool } = require('../config/database');

exports.getAllProfessionals = async (req, res) => {
  try {
    const [professionals] = await pool.execute(`
      SELECT
        idProfissional AS id,
        Nome AS name,
        Email AS email,
        Telefone AS phone,
        FotoPerfilUrl AS profileImage,
        BioDescricao AS description,
        RaioAtendimentoKm AS serviceRadiusKm,
        ValorHoraBase AS hourlyRate,
        NotaMedia AS rating,
        StatusConta AS status,
        Disponivel AS available
      FROM Profissional
      WHERE StatusConta <> 'Inativo'
      ORDER BY NotaMedia DESC, Nome ASC
    `);

    return res.status(200).json({
      success: true,
      count: professionals.length,
      professionals: professionals.map(p => ({
        ...p,
        available: Boolean(p.available)
      }))
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

exports.getProfessional = async (req, res) => {
  try {
    const { id } = req.params;

    const [rows] = await pool.execute(`
      SELECT
        idProfissional AS id,
        Nome AS name,
        Email AS email,
        Telefone AS phone,
        FotoPerfilUrl AS profileImage,
        BioDescricao AS description,
        RaioAtendimentoKm AS serviceRadiusKm,
        ValorHoraBase AS hourlyRate,
        NotaMedia AS rating,
        StatusConta AS status,
        Disponivel AS available,
        CriadoEm AS createdAt
      FROM Profissional
      WHERE idProfissional = ?
      LIMIT 1
    `, [id]);

    if (rows.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Professional not found'
      });
    }

    const professional = rows[0];

    const [services] = await pool.execute(`
      SELECT
        s.idServico AS id,
        s.NomeServico AS name,
        s.Descricao AS description,
        s.PrecoBase AS basePrice
      FROM Profissional_Especialidades pe
      INNER JOIN Servico s
        ON s.idServico = pe.Servico_idServico
      WHERE pe.Profissional_idProfissional = ?
      ORDER BY s.NomeServico
    `, [id]);

    professional.available = Boolean(professional.available);
    professional.services = services;

    return res.status(200).json({
      success: true,
      professional
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

exports.updateProfessional = async (req, res) => {
  try {
    const { id } = req.params;

    if (
      req.user.userType !== 'professional' ||
      Number(req.user.id) !== Number(id)
    ) {
      return res.status(403).json({
        success: false,
        message: 'Not authorized to update this professional'
      });
    }

    const {
      name,
      phone,
      profileImage,
      description,
      serviceRadiusKm,
      hourlyRate,
      available
    } = req.body;

    const [existing] = await pool.execute(
      'SELECT idProfissional FROM Profissional WHERE idProfissional = ? LIMIT 1',
      [id]
    );

    if (existing.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Professional not found'
      });
    }

    await pool.execute(`
      UPDATE Profissional
      SET
        Nome = COALESCE(?, Nome),
        Telefone = COALESCE(?, Telefone),
        FotoPerfilUrl = COALESCE(?, FotoPerfilUrl),
        BioDescricao = COALESCE(?, BioDescricao),
        RaioAtendimentoKm = COALESCE(?, RaioAtendimentoKm),
        ValorHoraBase = COALESCE(?, ValorHoraBase),
        Disponivel = COALESCE(?, Disponivel)
      WHERE idProfissional = ?
    `, [
      name ?? null,
      phone ?? null,
      profileImage ?? null,
      description ?? null,
      serviceRadiusKm ?? null,
      hourlyRate ?? null,
      available === undefined ? null : Boolean(available),
      id
    ]);

    const [rows] = await pool.execute(`
      SELECT
        idProfissional AS id,
        Nome AS name,
        Email AS email,
        Telefone AS phone,
        FotoPerfilUrl AS profileImage,
        BioDescricao AS description,
        RaioAtendimentoKm AS serviceRadiusKm,
        ValorHoraBase AS hourlyRate,
        NotaMedia AS rating,
        StatusConta AS status,
        Disponivel AS available
      FROM Profissional
      WHERE idProfissional = ?
    `, [id]);

    rows[0].available = Boolean(rows[0].available);

    return res.status(200).json({
      success: true,
      professional: rows[0]
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

exports.addService = async (req, res) => {
  try {
    const { id } = req.params;
    const { serviceId } = req.body;

    if (
      req.user.userType !== 'professional' ||
      Number(req.user.id) !== Number(id)
    ) {
      return res.status(403).json({
        success: false,
        message: 'Not authorized'
      });
    }

    if (!serviceId) {
      return res.status(400).json({
        success: false,
        message: 'serviceId is required'
      });
    }

    const [professional] = await pool.execute(
      'SELECT idProfissional FROM Profissional WHERE idProfissional = ? LIMIT 1',
      [id]
    );

    if (professional.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Professional not found'
      });
    }

    const [service] = await pool.execute(
      'SELECT idServico, NomeServico FROM Servico WHERE idServico = ? LIMIT 1',
      [serviceId]
    );

    if (service.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Service not found'
      });
    }

    await pool.execute(`
      INSERT IGNORE INTO Profissional_Especialidades
      (Profissional_idProfissional, Servico_idServico)
      VALUES (?, ?)
    `, [id, serviceId]);

    return res.status(200).json({
      success: true,
      message: 'Service added successfully',
      service: {
        id: service[0].idServico,
        name: service[0].NomeServico
      }
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
