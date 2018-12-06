const { Client } = require('pg');
const { database } = require('../config.json');

const client = new Client(database);
client.connect();

module.exports = client;
