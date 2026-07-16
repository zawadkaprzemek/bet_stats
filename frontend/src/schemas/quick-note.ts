import { z } from 'zod';

export const quickNoteSchema = z.object({
  title: z.string().trim().min(3, 'Use at least 3 characters.').max(64, 'Use at most 64 characters.'),
});

export type QuickNoteInput = z.infer<typeof quickNoteSchema>;
