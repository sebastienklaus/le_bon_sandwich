module.exports = function (app) {
  /*
  * Routes
  */
  app.use('/user', require('./routes/user.route'));

};
