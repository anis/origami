const express = require('express');
const bodyParser = require('body-parser');
const routes = require('./routes/index');

const app = express();

// body-parser
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// define the routes
Object.keys(routes).forEach((path) => {
    const verbs = Object.keys(routes[path]);
    verbs.forEach((verb) => {
        app[verb](path, routes[path][verb]);
    });
});

// start the server
app.listen(3000, async () => {
    console.log('Up and running');
});
