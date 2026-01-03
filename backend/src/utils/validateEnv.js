const validateEnv = () => {
  const required = ['DATABASE_URL', 'JWT_SECRET', 'FRONTEND_URL'];
  const missing = required.filter((key) => !process.env[key]);

  if (missing.length > 0) {
    console.error(`❌ Erro: Variáveis de ambiente ausentes: ${missing.join(', ')}`);
    process.exit(1);
  }
};

module.exports = validateEnv;