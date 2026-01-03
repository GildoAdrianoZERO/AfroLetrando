const API_URL = import.meta.env.VITE_API_URL;

export const getHealth = async () => {
  try {
    const response = await fetch(`${API_URL}/health`);
    if (!response.ok) throw new Error();
    return await response.json();
  } catch (error) {
    throw new Error('Falha na conexão');
  }
};