const express = require('express');
const { PrismaClient } = require('@prisma/client');
const router = express.Router();
const prisma = new PrismaClient();


router.get('/index', async (req, res) => {
  try {
    const recentProjects = await prisma.project.findMany({
      where: { status: 'PUBLISHED' },
      take: 5,
      orderBy: { publishedAt: 'desc' },
      include: { createdBy: { select: { name: true } } }
    });

    const topTags = await prisma.tag.findMany({
      include: { _count: { select: { projects: true } } },
      take: 10
    });

    res.json({ recentProjects, topTags });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

router.get('/projects', async (req, res) => {
  const { q, tag, page = 1, limit = 10 } = req.query;
  const skip = (page - 1) * limit;

  try {
    const where = {
      status: 'PUBLISHED',
      ...(q && {
        OR: [
          { title: { contains: q, mode: 'insensitive' } },
          { excerpt: { contains: q, mode: 'insensitive' } }
        ]
      }),
      ...(tag && { tags: { some: { tag: { slug: tag } } } })
    };

    const [projects, total] = await prisma.$transaction([
      prisma.project.findMany({
        where,
        skip: Number(skip),
        take: Number(limit),
        orderBy: { publishedAt: 'desc' },
        include: { createdBy: { select: { name: true } }, tags: { include: { tag: true } } }
      }),
      prisma.project.count({ where })
    ]);

    res.json({
      data: projects,
      meta: { total, page: Number(page), limit: Number(limit), totalPages: Math.ceil(total / limit) }
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});


router.get('/projects/:slug', async (req, res) => {
  try {
    const project = await prisma.project.findFirst({
      where: { slug: req.params.slug, status: 'PUBLISHED' },
      include: {
        createdBy: { select: { name: true, id: true } },
        tags: { include: { tag: true } }
      }
    });
    if (!project) return res.status(404).json({ error: 'Projeto não encontrado' });
    res.json(project);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;