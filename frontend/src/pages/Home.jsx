import { useEffect, useState } from 'react';
import { getHealth } from "../api/apiClient";

const Home = () => {
  const [status, setStatus] = useState('Verificando...');
  const apiUrl = import.meta.env.VITE_API_URL;

  useEffect(() => {
    getHealth()
      .then(() => setStatus('API conectada ✅'))
      .catch(() => setStatus('Erro ao conectar API ❌'));
  }, []);

  return (
    <div style={{ fontFamily: 'sans-serif', padding: '40px' }}>
      <h1>Afro Letrando</h1>
      <p>Status do sistema: <strong>{status}</strong></p>
      <small>Conectado em: {apiUrl}</small>
    </div>
  );
};

export default Home;