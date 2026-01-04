require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const validateEnv = require('./utils/validateEnv');
const publicRoutes = require('./routes/public');

validateEnv();

const app = express();

app.use(helmet());
app.use(morgan('dev'));
app.use(cors({ origin: process.env.FRONTEND_URL }));
app.use(express.json());
app.use('/api', publicRoutes);

app.get('/health', (req, res) => res.json({ status: 'ok' }));
app.get('/api', (req, res) => res.json({ status: 'api online' }));

app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: 'Erro interno no servidor' });
});

module.exports = app;