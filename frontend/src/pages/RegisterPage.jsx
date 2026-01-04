import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';

const RegisterPage = () => {
  const [formData, setFormData] = useState({ name: '', email: '', password: '' });
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await fetch(`${import.meta.env.VITE_API_URL}/api/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Erro ao registrar');

      alert('Conta criada com sucesso! Faça login.');
      navigate('/login');
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px', border: '1px solid #ddd' }}>
      <h2>Criar Conta - Afro Letrando</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: '10px' }}>
          <label>Nome:</label><br />
          <input type="text" style={{ width: '100%' }} required
            onChange={(e) => setFormData({ ...formData, name: e.target.value })} />
        </div>
        <div style={{ marginBottom: '10px' }}>
          <label>E-mail:</label><br />
          <input type="email" style={{ width: '100%' }} required
            onChange={(e) => setFormData({ ...formData, email: e.target.value })} />
        </div>
        <div style={{ marginBottom: '10px' }}>
          <label>Senha:</label><br />
          <input type="password" style={{ width: '100%' }} required
            onChange={(e) => setFormData({ ...formData, password: e.target.value })} />
        </div>
        <button type="submit" style={{ width: '100%', padding: '10px', background: '#eab308', border: 'none' }}>
          Registrar
        </button>
      </form>
      <p>Já tem conta? <Link to="/login">Entre aqui</Link></p>
    </div>
  );
};

export default RegisterPage;