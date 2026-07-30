import { z } from 'zod';

export const tagSchema = z.object({
  name: z.string().trim().min(2, 'Use at least 2 characters.').max(120, 'Use at most 120 characters.'),
  emoji: z
    .string()
    .trim()
    .max(16, 'Use at most 16 characters.')
    .optional()
    .or(z.literal('')),
  color: z
    .string()
    .trim()
    .regex(/^#[0-9A-Fa-f]{6}$/, 'Use a valid hex color like #22C55E.')
    .optional()
    .or(z.literal('')),
});

export type TagFormInput = z.infer<typeof tagSchema>;
