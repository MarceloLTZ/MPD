const express = require('express');
const router = express.Router();
const {
  createPayment,
  getUserPayments
} = require('../controllers/paymentController');
const { protect } = require('../middleware/auth');

router.post('/', protect, createPayment);
router.get('/', protect, getUserPayments);

module.exports = router;
