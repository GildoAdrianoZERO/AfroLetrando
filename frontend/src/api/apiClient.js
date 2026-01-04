const API_URL = import.meta.env.VITE_API_URL;

// Helper para injetar o Token em rotas protegidas
const getHeaders = () => {
  const token = localStorage.getItem('afroletrando_token');
  const headers = { 'Content-Type': 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;
  return headers;
};

const handleResponse = async (res) => {
  if (!res.ok) {
    const errorData = await res.json().catch(() => ({}));
    throw new Error(errorData.error || 'Erro na requisição');
  }
  return res.json();
};


export const getHealth = () => fetch(`${API_URL}/health`).then(handleResponse);

export const getIndex = () => fetch(`${API_URL}/api/index`).then(handleResponse);

export const getProject = (slug) => fetch(`${API_URL}/api/projects/${slug}`).then(handleResponse);


export const loginUser = (credentials) => 
  fetch(`${API_URL}/api/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(credentials)
  }).then(handleResponse);

export const registerUser = (userData) => 
  fetch(`${API_URL}/api/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(userData)
  }).then(handleResponse);

export const getMe = () => 
  fetch(`${API_URL}/api/auth/me`, {
    headers: getHeaders()
  }).then(handleResponse);