const Payment = require('../models/Payment');
const Professional = require('../models/Professional');

exports.createPayment = async (req, res) => {
  try {
    const {
      professionalId,
      amount,
      paymentMethod,
      serviceDetails,
      currency
    } = req.body;

    const professional = await Professional.findById(professionalId);
    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    const transactionId = `MPD-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

    const payment = await Payment.create({
      userId: req.user.id,
      professionalId,
      amount,
      currency: currency || 'BRL',
      paymentMethod,
      transactionId,
      serviceDetails,
      paymentStatus: 'pending'
    });

    res.status(201).json({
      success: true,
      payment,
      paymentDetails: {
        transactionId,
        status: 'pending'
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.getUserPayments = async (req, res) => {
  try {
    const { status } = req.query;
    let query = { userId: req.user.id };
    if (status) query.paymentStatus = status;

    const payments = await Payment.find(query)
      .populate('professionalId', 'title specialty')
      .sort({ createdAt: -1 });

    res.status(200).json({
      success: true,
      count: payments.length,
      payments
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};
