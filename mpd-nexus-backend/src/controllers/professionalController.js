const Professional = require('../models/Professional');
const User = require('../models/User');

exports.getAllProfessionals = async (req, res) => {
  try {
    const { specialty, city, rating, search } = req.query;
    
    let query = { isActive: true };
    
    if (specialty) {
      query.specialty = { $regex: specialty, $options: 'i' };
    }
    if (city) {
      query['location.city'] = { $regex: city, $options: 'i' };
    }
    if (rating) {
      query.rating = { $gte: Number(rating) };
    }
    if (search) {
      query.$or = [
        { title: { $regex: search, $options: 'i' } },
        { specialty: { $regex: search, $options: 'i' } },
        { description: { $regex: search, $options: 'i' } }
      ];
    }

    const professionals = await Professional.find(query)
      .populate('userId', 'name email profileImage')
      .sort({ rating: -1 });

    res.status(200).json({
      success: true,
      count: professionals.length,
      professionals
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.getProfessional = async (req, res) => {
  try {
    const professional = await Professional.findById(req.params.id)
      .populate('userId', 'name email phone profileImage');

    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    res.status(200).json({
      success: true,
      professional
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.updateProfessional = async (req, res) => {
  try {
    let professional = await Professional.findById(req.params.id);
    
    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    if (professional.userId.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Not authorized' });
    }

    professional = await Professional.findByIdAndUpdate(
      req.params.id,
      req.body,
      { new: true, runValidators: true }
    );

    res.status(200).json({
      success: true,
      professional
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};

exports.addService = async (req, res) => {
  try {
    const professional = await Professional.findById(req.params.id);
    
    if (!professional) {
      return res.status(404).json({ message: 'Professional not found' });
    }

    if (professional.userId.toString() !== req.user.id) {
      return res.status(403).json({ message: 'Not authorized' });
    }

    professional.services.push(req.body);
    await professional.save();

    res.status(201).json({
      success: true,
      services: professional.services
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error' });
  }
};
