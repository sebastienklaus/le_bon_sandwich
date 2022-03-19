const express = require('express');
const user = require('../services/user');
const router = new express.Router();
 
router.get('/', async (req, res, next) => {
  let options = { 
    "id": req.query.id,
  };


  try {
    const result = await user.listUser(options);
    res.status(result.status || 200).send(result.data);
  }
  catch (err) {
    return res.status(500).send({
      error: err || 'Something went wrong.'
    });
  }
});

module.exports = router;