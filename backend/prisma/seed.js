const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function main() {
  const user = await prisma.user.upsert({
    where: { email: 'admin@afroletrando.com' },
    update: {},
    create: {
      name: 'Admin Afro Letrando',
      email: 'admin@afroletrando.com',
      passwordHash: 'hash_fake', // Task 3 tratará auth
      role: 'ADMIN'
    }
  });

  await prisma.project.create({
    data: {
      title: 'A Importância da Literatura Afro-Brasileira',
      slug: 'literatura-afro-brasileira',
      excerpt: 'Uma análise sobre o impacto social das obras negras.',
      content: '<p>Conteúdo completo do artigo acadêmico...</p>',
      status: 'PUBLISHED',
      publishedAt: new Date(),
      createdById: user.id,
      tags: {
        create: [
          { tag: { create: { name: 'Educação', slug: 'educacao' } } },
          { tag: { create: { name: 'História', slug: 'historia' } } }
        ]
      }
    }
  });
  console.log('✅ Seed finalizado!');
}

main().catch(console.error).finally(() => prisma.$disconnect());