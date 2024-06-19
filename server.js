const express = require('express');
const jsonServer = require('json-server');
const path = require('path');

const app = express();
const router = jsonServer.router(path.join(__dirname, 'db.json'));
const middlewares = jsonServer.defaults();
const port = process.env.PORT || 3000;

app.use(middlewares);
app.use(router);
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
