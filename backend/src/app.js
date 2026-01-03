require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const validateEnv = require('./utils/validateEnv');

validateEnv();

const app = express();

app.use(helmet());
app.use(morgan('dev'));
app.use(cors({ origin: process.env.FRONTEND_URL }));
app.use(express.json());

// Rotas Base
app.get('/health', (req, res) => res.json({ status: 'ok' }));
app.get('/api', (req, res) => res.json({ status: 'api online' }));

// Error Handler
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: 'Erro interno no servidor' });
});

module.exports = app;