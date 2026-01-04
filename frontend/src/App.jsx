import React, { useEffect, useState } from 'react';
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { ProtectedRoute } from './components/ProtectedRoute';
import { getHealth, getIndex } from './api/apiClient';

// Certifique-se de que estes arquivos já foram criados na pasta src/pages/
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ProjectDetail from './pages/ProjectDetail';

const Home = () => {
  const [status, setStatus] = useState('Verificando...');
  const [data, setData] = useState(null);

  useEffect(() => {
    getHealth().then(() => setStatus('API conectada ✅')).catch(() => setStatus('Erro ❌'));
    getIndex().then(setData).catch(console.error);
  }, []);

  return (
    <div style={{ padding: '20px' }}>
      <h1>Afro Letrando</h1>
      <p>Status: {status}</p>
      {data?.recentProjects?.map(p => (
        <div key={p.id}><Link to={`/projetos/${p.slug}`}>{p.title}</Link></div>
      ))}
    </div>
  );
};

const AuthStatus = () => {
  const auth = useAuth();
  if (!auth?.user) return <Link to="/login" style={{ marginLeft: '10px' }}>Entrar</Link>;
  return (
    <span style={{ marginLeft: '10px' }}>
      Olá, {auth.user.name} <button onClick={auth.logout}>Sair</button>
    </span>
  );
};

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <nav style={{ padding: '10px', background: '#eee' }}>
          <Link to="/">Home</Link> | <Link to="/admin">Admin</Link>
          <AuthStatus />
        </nav>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/projetos/:slug" element={<ProjectDetail />} />
          <Route path="/admin" element={
            <ProtectedRoute allowedRoles={['ADMIN', 'EDITOR']}>
              <div style={{ padding: '20px' }}><h1>Painel Admin</h1></div>
            </ProtectedRoute>
          } />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App; // Esta linha PRECISA existir