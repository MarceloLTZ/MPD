const mongoose = require('mongoose');

const contactSchema = new mongoose.Schema({
  userId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  contactId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  professionalId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Professional',
    required: true
  },
  type: {
    type: String,
    enum: ['professional', 'client'],
    default: 'professional'
  },
  status: {
    type: String,
    enum: ['active', 'blocked', 'archived'],
    default: 'active'
  },
  lastInteraction: {
    type: Date,
    default: Date.now
  },
  notes: {
    type: String,
    trim: true
  },
  favorite: {
    type: Boolean,
    default: false
  }
}, {
  timestamps: true
});

module.exports = mongoose.model('Contact', contactSchema);
