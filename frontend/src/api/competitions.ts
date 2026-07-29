import { apiFetch } from './client';
import type { Competition, CreateCompetitionInput } from '../types/competition';

export function fetchCompetitions(sportId: number): Promise<Competition[]> {
  return apiFetch<Competition[]>(`/competitions?sport=${sportId}`);
}

export function createCompetition(input: CreateCompetitionInput): Promise<Competition> {
  return apiFetch<Competition>('/competitions', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  });
}
