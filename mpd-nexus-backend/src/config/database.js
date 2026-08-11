const mongoose = require('mongoose');

const connectDB = async () => {
  try {
    const mongoURI = process.env.MONGODB_URI;

    if (!mongoURI) {
      throw new Error('MONGODB_URI não foi definida no arquivo .env');
    }

    const conn = await mongoose.connect(mongoURI, {
      serverSelectionTimeoutMS: 5000,
    });

    console.log('✅ MongoDB conectado com sucesso!');
    console.log(`📊 Host: ${conn.connection.host}`);
    console.log(`📊 Database: ${conn.connection.name}`);

    return conn;
  } catch (error) {
    console.error('❌ Erro ao conectar ao MongoDB:');
    console.error(error.message);

    throw error;
  }
};

module.exports = connectDB;
