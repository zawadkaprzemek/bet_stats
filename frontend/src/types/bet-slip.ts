import type { Bookmaker } from './bookmaker';
import type { Tag } from './tag';

export type BetSlipStatus = 'pending' | 'won' | 'lost' | 'void' | 'cashout' | 'partial_cashout';
export type BetSlipStakeType = 'cash' | 'freebet' | 'bonus';
export type BetSlipTiming = 'pre_match' | 'live' | 'hybrid';

export type BetSlipCompetition = {
  id: number;
  name: string;
  sport: {
    id: number;
    name: string;
    slug: string;
    emoji: string | null;
  };
  region: {
    id: number;
    name: string;
    slug: string;
    emoji: string | null;
  } | null;
  isGlobal: boolean;
};

export type BetSlip = {
  id: number;
  name: string | null;
  description: string;
  bookmaker: Bookmaker;
  stake: string;
  totalOdds: string;
  stakeType: BetSlipStakeType;
  timing: BetSlipTiming;
  status: BetSlipStatus;
  selectionsCount: number | null;
  taxPercent: string;
  predictedPayout: string;
  actualPayout: string | null;
  profit: string | null;
  roi: string | null;
  placedAt: string;
  settledAt: string | null;
  competitions: BetSlipCompetition[];
  tags: Tag[];
};

export type CreateBetSlipPayload = {
  name?: string | null;
  description: string;
  bookmakerId: number;
  stake: string;
  totalOdds: string;
  stakeType: BetSlipStakeType;
  timing: BetSlipTiming;
  selectionsCount?: number | null;
  taxPercent: string;
  competitionIds: number[];
  tagIds: number[];
  placedAt: string;
};

export type UpdateBetSlipPayload = CreateBetSlipPayload;

export type SettleBetSlipPayload = {
  status: BetSlipStatus;
  actualPayout?: string | null;
  settledAt?: string | null;
};

export type BetSlipFilters = {
  page?: number;
  perPage?: number;
  status?: BetSlipStatus | '';
  bookmaker?: number | '';
  stakeType?: BetSlipStakeType | '';
  timing?: BetSlipTiming | '';
  competition?: number | '';
  tag?: number | '';
  placedFrom?: string;
  placedTo?: string;
  search?: string;
};

export type BetSlipListResponse = {
  items: BetSlip[];
  pagination: {
    page: number;
    perPage: number;
    total: number;
  };
};
