const Chat = require('../models/Chat');
const Message = require('../models/Message');
const Professional = require('../models/Professional');
const User = require('../models/User');

exports.getChats = async (req, res) => {
  try {
    const chats = await Chat.find({
      'participants.userId': req.user.id,
      isActive: true
    })
    .populate('participants.userId', 'name email profileImage')
    .populate('professionalId', 'title specialty rating')
    .populate('lastMessage')
    .sort({ updatedAt: -1 });

    res.status(200).json({
      success: true,
      count: chats.length,
      chats
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.getChatMessages = async (req, res) => {
  try {
    const { chatId } = req.params;
    const { page = 1, limit = 50 } = req.query;

    const chat = await Chat.findById(chatId);
    if (!chat) {
      return res.status(404).json({ message: 'Chat not found' });
    }

    const isParticipant = chat.participants.some(
      p => p.userId.toString() === req.user.id
    );
    if (!isParticipant) {
      return res.status(403).json({ message: 'Not authorized' });
    }

    const messages = await Message.find({ chatId, isDeleted: false })
      .sort({ createdAt: -1 })
      .skip((page - 1) * limit)
      .limit(parseInt(limit))
      .populate('sender.userId', 'name email profileImage');

    await Message.updateMany(
      {
        chatId,
        'sender.userId': { $ne: req.user.id },
        isRead: false
      },
      { isRead: true, readAt: Date.now() }
    );

    res.status(200).json({
      success: true,
      messages: messages.reverse(),
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: await Message.countDocuments({ chatId, isDeleted: false })
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.sendMessage = async (req, res) => {
  try {
    const { chatId } = req.params;
    const { content, messageType, attachments } = req.body;

    const chat = await Chat.findById(chatId);
    if (!chat) {
      return res.status(404).json({ message: 'Chat not found' });
    }

    const participant = chat.participants.find(
      p => p.userId.toString() === req.user.id
    );
    if (!participant) {
      return res.status(403).json({ message: 'Not authorized' });
    }

    const user = await User.findById(req.user.id);
    const userType = user.isProfessional ? 'professional' : 'client';

    const message = await Message.create({
      chatId,
      sender: {
        userId: req.user.id,
        userType
      },
      content,
      messageType: messageType || 'text',
      attachments: attachments || [],
      status: 'sent'
    });

    chat.lastMessage = message._id;
    chat.updatedAt = Date.now();
    await chat.save();

    await message.populate('sender.userId', 'name email profileImage');

    res.status(201).json({
      success: true,
      message
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.createChat = async (req, res) => {
  try {
    const { professionalId } = req.body;

    const professional = await Professional.findById(professionalId);
    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    const existingChat = await Chat.findOne({
      'participants.userId': req.user.id,
      professionalId,
      isActive: true
    });

    if (existingChat) {
      return res.status(200).json({
        success: true,
        chat: existingChat
      });
    }

    const chat = await Chat.create({
      participants: [
        {
          userId: req.user.id,
          userType: 'client'
        },
        {
          userId: professional.userId,
          userType: 'professional'
        }
      ],
      professionalId
    });

    await chat.populate('participants.userId', 'name email profileImage');
    await chat.populate('professionalId', 'title specialty');

    res.status(201).json({
      success: true,
      chat
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};
