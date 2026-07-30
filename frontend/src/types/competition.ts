import type { Region } from './region';

export type Competition = {
  id: number;
  sportId: number;
  name: string;
  region: Region | null;
  isGlobal: boolean;
};

export type CreateCompetitionInput = {
  sportId: number;
  name: string;
  regionId?: number | null;
};
