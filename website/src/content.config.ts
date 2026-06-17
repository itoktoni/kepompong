import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const blogCollection = defineCollection({
  loader: glob({ pattern: '**/*.{md,mdx}', base: './src/content/blog' }),
  schema: z.object({
    title: z.string(),
    description: z.string(),
    pubDate: z.date(),
    updatedDate: z.date().optional(),
    author: z.string().default('Kepompong Team'),
    category: z.string(),
    tags: z.array(z.string()),
    image: z.object({
      url: z.string(),
      alt: z.string()
    }).optional(),
    draft: z.boolean().default(false),
  }),
});

export const collections = {
  blog: blogCollection,
  lms: defineCollection({
    loader: glob({ pattern: '**/*.{md,mdx}', base: './src/content/lms' }),
    schema: z.object({
      title: z.string(),
      description: z.string(),
      category: z.string(),
      level: z.string().default('pemula'),
      duration: z.string().optional(),
      image: z.object({ url: z.string(), alt: z.string() }).optional(),
      draft: z.boolean().default(false),
    }),
  }),
};
