require('dotenv').config();

const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: process.env.DB_HOST,
  port: process.env.DB_PORT,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

const connectDB = async () => {
  try {
    const connection = await pool.getConnection();
    await connection.ping();

    console.log('✅ MySQL conectado com sucesso!');
    console.log(`📊 Banco: ${process.env.DB_NAME}`);

    connection.release();
  } catch (error) {
    console.error('❌ Erro ao conectar ao MySQL:');
    console.error(error.message);
    throw error;
  }
};

module.exports = {
  pool,
  connectDB
};
