const Contact = require('../models/Contact');
const Professional = require('../models/Professional');

exports.getContacts = async (req, res) => {
  try {
    const contacts = await Contact.find({ userId: req.user.id, status: 'active' })
      .populate('contactId', 'name email profileImage')
      .populate('professionalId', 'title specialty rating')
      .sort({ favorite: -1, lastInteraction: -1 });

    res.status(200).json({
      success: true,
      count: contacts.length,
      contacts
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.addContact = async (req, res) => {
  try {
    const { professionalId } = req.body;
    
    const professional = await Professional.findById(professionalId);
    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    const existingContact = await Contact.findOne({
      userId: req.user.id,
      professionalId
    });

    if (existingContact) {
      return res.status(400).json({ message: 'Contact already exists' });
    }

    const contact = await Contact.create({
      userId: req.user.id,
      contactId: professional.userId,
      professionalId,
      type: 'professional'
    });

    res.status(201).json({
      success: true,
      contact
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.toggleFavorite = async (req, res) => {
  try {
    const contact = await Contact.findById(req.params.id);
    if (!contact) {
      return res.status(404).json({ message: 'Contact not found' });
    }

    if (contact.userId.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Not authorized' });
    }

    contact.favorite = !contact.favorite;
    await contact.save();

    res.status(200).json({
      success: true,
      contact
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};
