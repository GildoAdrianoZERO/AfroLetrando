import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const LoginPage = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await fetch(`${import.meta.env.VITE_API_URL}/api/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Falha no login');

      login(data.user, data.token); // Salva no contexto e localStorage
      
      // Redirect baseado na role
      if (['ADMIN', 'EDITOR'].includes(data.user.role)) {
        navigate('/admin');
      } else {
        navigate('/');
      }
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px', border: '1px solid #ddd' }}>
      <h2>Login Acadêmico</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: '10px' }}>
          <label>E-mail:</label><br />
          <input type="email" style={{ width: '100%' }} required
            onChange={(e) => setEmail(e.target.value)} />
        </div>
        <div style={{ marginBottom: '10px' }}>
          <label>Senha:</label><br />
          <input type="password" style={{ width: '100%' }} required
            onChange={(e) => setPassword(e.target.value)} />
        </div>
        <button type="submit" style={{ width: '100%', padding: '10px', background: '#eab308', border: 'none' }}>
          Entrar
        </button>
      </form>
      <p>Não tem conta? <Link to="/register">Cadastre-se</Link></p>
    </div>
  );
};

export default LoginPage;