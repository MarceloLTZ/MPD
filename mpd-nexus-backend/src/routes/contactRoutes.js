const express = require('express');
const router = express.Router();
const {
  getContacts,
  addContact,
  toggleFavorite
} = require('../controllers/contactController');
const { protect } = require('../middleware/auth');

router.get('/', protect, getContacts);
router.post('/', protect, addContact);
router.put('/:id/favorite', protect, toggleFavorite);

module.exports = router;
