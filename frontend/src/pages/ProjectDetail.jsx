import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { getProject } from '../api/apiClient';

const ProjectDetail = () => {
  const { slug } = useParams();
  const [project, setProject] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getProject(slug)
      .then((data) => {
        setProject(data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("Erro ao buscar projeto:", err);
        setLoading(false);
      });
  }, [slug]);

  if (loading) return <p>Carregando artigo acadêmico...</p>;
  if (!project) return <p>Artigo não encontrado. <Link to="/">Voltar</Link></p>;

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto', padding: '20px', fontFamily: 'serif' }}>
      <Link to="/">← Voltar para o Índice</Link>
      
      {project.coverImageUrl && (
        <img src={project.coverImageUrl} alt={project.title} style={{ width: '100%', marginTop: '20px' }} />
      )}
      
      <h1 style={{ fontSize: '2.5rem', marginBottom: '10px' }}>{project.title}</h1>
      
      <p style={{ color: '#666', fontStyle: 'italic' }}>
        Por: {project.createdBy?.name} | Publicado em: {new Date(project.publishedAt).toLocaleDateString()}
      </p>

      <hr />

      <div 
        style={{ lineHeight: '1.6', fontSize: '1.1rem', marginTop: '30px' }}
        dangerouslySetInnerHTML={{ __html: project.content }} 
      />

      {project.pdfUrl && (
        <div style={{ marginTop: '40px', padding: '20px', backgroundColor: '#f9f9f9', border: '1px solid #ddd' }}>
          <strong>Documento Original:</strong><br />
          <a href={project.pdfUrl} target="_blank" rel="noreferrer">📄 Baixar PDF do Artigo</a>
        </div>
      )}
    </div>
  );
};

export default ProjectDetail;