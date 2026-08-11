require('dotenv').config();

const app = require('./src/app');
const { connectDB } = require('./src/config/database');

const PORT = process.env.PORT || 3000;

const startServer = async () => {
  try {
    await connectDB();

    const server = app.listen(PORT, () => {
      console.log(`🚀 Server running on port ${PORT}`);
      console.log(`📡 API URL: http://localhost:${PORT}/api`);
    });

    process.on('unhandledRejection', (err) => {
      console.log('UNHANDLED REJECTION! 💥 Shutting down...');
      console.log(err.name, err.message);

      server.close(() => process.exit(1));
    });
  } catch (error) {
    console.error('❌ Não foi possível iniciar o servidor.');
    console.error(error.message);
    process.exit(1);
  }
};

startServer();
