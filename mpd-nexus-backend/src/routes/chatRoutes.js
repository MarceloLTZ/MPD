const express = require('express');
const router = express.Router();
const {
  getChats,
  getChatMessages,
  sendMessage,
  createChat
} = require('../controllers/chatController');
const { protect } = require('../middleware/auth');

router.get('/', protect, getChats);
router.post('/', protect, createChat);
router.get('/:chatId/messages', protect, getChatMessages);
router.post('/:chatId/messages', protect, sendMessage);

module.exports = router;
