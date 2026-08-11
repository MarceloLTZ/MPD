const mongoose = require('mongoose');

const paymentSchema = new mongoose.Schema({
  userId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  professionalId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Professional',
    required: true
  },
  amount: {
    type: Number,
    required: true,
    min: 0
  },
  currency: {
    type: String,
    default: 'BRL'
  },
  paymentMethod: {
    type: String,
    enum: ['credit_card', 'debit_card', 'pix', 'boleto', 'paypal'],
    required: true
  },
  paymentStatus: {
    type: String,
    enum: ['pending', 'processing', 'completed', 'failed', 'refunded'],
    default: 'pending'
  },
  transactionId: {
    type: String,
    unique: true,
    sparse: true
  },
  paymentDetails: {
    cardLast4: String,
    cardBrand: String,
    pixCode: String,
    boletoUrl: String,
    boletoBarcode: String
  },
  serviceDetails: {
    serviceName: String,
    serviceDate: Date,
    description: String
  },
  receiptUrl: String,
  refundReason: String,
  refundedAt: Date
}, {
  timestamps: true
});

module.exports = mongoose.model('Payment', paymentSchema);
