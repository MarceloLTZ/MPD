const mongoose = require('mongoose');

const professionalSchema = new mongoose.Schema({
  userId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  title: {
    type: String,
    required: [true, 'Please provide a professional title']
  },
  specialty: {
    type: String,
    required: [true, 'Please provide a specialty']
  },
  description: {
    type: String,
    required: [true, 'Please provide a description']
  },
  services: [{
    name: String,
    description: String,
    price: Number,
    duration: Number
  }],
  experience: {
    type: Number,
    default: 0
  },
  rating: {
    type: Number,
    default: 0,
    min: 0,
    max: 5
  },
  totalReviews: {
    type: Number,
    default: 0
  },
  availability: [{
    day: {
      type: String,
      enum: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
    },
    startTime: String,
    endTime: String,
    isAvailable: Boolean
  }],
  location: {
    address: String,
    city: String,
    state: String,
    zipCode: String,
    coordinates: {
      lat: Number,
      lng: Number
    }
  },
  portfolio: [{
    title: String,
    description: String,
    images: [String],
    date: Date
  }],
  certifications: [{
    name: String,
    issuer: String,
    date: Date,
    expiryDate: Date
  }],
  isVerified: {
    type: Boolean,
    default: false
  },
  isActive: {
    type: Boolean,
    default: true
  }
}, {
  timestamps: true
});

module.exports = mongoose.model('Professional', professionalSchema);
