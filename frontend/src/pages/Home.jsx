import React, { useEffect, useState } from 'react';
import { getIndex } from '../api/apiClient';
import { Link } from 'react-router-dom';

const Home = () => {
  const [data, setData] = useState(null);

  useEffect(() => {
    getIndex().then(setData).catch(console.error);
  }, []);

  if (!data) return <p>Carregando periódico...</p>;

  return (
    <div>
      <h1>Destaques Recentes</h1>
      <div className="grid">
        {data.recentProjects.map(p => (
          <div key={p.id} className="card">
            <h3>{p.title}</h3>
            <p>{p.excerpt}</p>
            <Link to={`/projetos/${p.slug}`}>Leia mais</Link>
          </div>
        ))}
      </div>
      <aside>
        <h2>Tags Populares</h2>
        {data.topTags.map(t => (
          <Link key={t.id} to={`/projetos?tag=${t.slug}`}>{t.name} ({t._count.projects})</Link>
        ))}
      </aside>
    </div>
  );
};

export default Home;