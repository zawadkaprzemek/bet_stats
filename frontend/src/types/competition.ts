import type { Region } from './region';
import type { Sport } from './sport';

export type Competition = {
  id: number;
  sportId: number;
  name: string;
  sport: Sport;
  region: Region | null;
  isGlobal: boolean;
};

export type CreateCompetitionInput = {
  sportId: number;
  name: string;
  regionId?: number | null;
};
