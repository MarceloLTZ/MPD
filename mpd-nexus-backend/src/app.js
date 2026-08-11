const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');
const errorHandler = require('./middleware/errorHandler');

dotenv.config();

const authRoutes = require('./routes/authRoutes');
const professionalRoutes = require('./routes/professionalRoutes');
const chatRoutes = require('./routes/chatRoutes');
const paymentRoutes = require('./routes/paymentRoutes');
const contactRoutes = require('./routes/contactRoutes');

const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cors({ origin: '*', credentials: true }));

app.use('/api/auth', authRoutes);
app.use('/api/professionals', professionalRoutes);
app.use('/api/chats', chatRoutes);
app.use('/api/payments', paymentRoutes);
app.use('/api/contacts', contactRoutes);

app.get('/api/health', (req, res) => {
  res.status(200).json({ status: 'OK', message: 'MPD Nexus API is running' });
});

app.use(errorHandler);

module.exports = app;
