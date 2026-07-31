import { z } from 'zod';

export const betSlipFormSchema = z.object({
  name: z.string().trim().max(120, 'Use at most 120 characters.').optional().or(z.literal('')),
  description: z
    .string()
    .trim()
    .min(1, 'Describe the whole coupon before saving.')
    .max(5000, 'Keep the description under 5000 characters.'),
  bookmakerId: z.number().int().positive('Select a bookmaker.'),
  stake: z.string().trim().regex(/^\d+(?:\.\d{1,2})?$/, 'Enter a valid amount like 50.00.'),
  totalOdds: z.string().trim().regex(/^\d+(?:\.\d{1,3})?$/, 'Enter odds like 5.820.'),
  stakeType: z.enum(['cash', 'freebet', 'bonus']),
  timing: z.enum(['pre_match', 'live', 'hybrid']),
  selectionsCount: z
    .string()
    .trim()
    .regex(/^\d*$/, 'Use a whole number.')
    .optional()
    .or(z.literal('')),
  taxPercent: z.string().trim().regex(/^\d+(?:\.\d{1,2})?$/, 'Enter a tax percentage like 12.00.'),
  competitionIds: z.array(z.number().int().positive()),
  tagIds: z.array(z.number().int().positive()),
  placedAt: z.string().trim().min(1, 'Select the placed date and time.'),
});

export const betSlipSettlementSchema = z.object({
  status: z.enum(['pending', 'won', 'lost', 'void', 'cashout', 'partial_cashout']),
  usePredictedPayout: z.boolean(),
  actualPayout: z
    .string()
    .trim()
    .regex(/^\d*(?:\.\d{1,2})?$/, 'Enter a valid payout like 125.50.')
    .optional()
    .or(z.literal('')),
  settledAt: z.string().trim().optional().or(z.literal('')),
});

export type BetSlipFormValues = z.infer<typeof betSlipFormSchema>;
export type BetSlipSettlementValues = z.infer<typeof betSlipSettlementSchema>;
