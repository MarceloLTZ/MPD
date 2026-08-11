const express = require('express');
const router = express.Router();
const {
  getAllProfessionals,
  getProfessional,
  updateProfessional,
  addService
} = require('../controllers/professionalController');
const { protect } = require('../middleware/auth');

router.get('/', getAllProfessionals);
router.get('/:id', getProfessional);
router.put('/:id', protect, updateProfessional);
router.post('/:id/services', protect, addService);

module.exports = router;
