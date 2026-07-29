import { apiFetch } from './client';
import type { Sport } from '../types/sport';

export function fetchSports(): Promise<Sport[]> {
  return apiFetch<Sport[]>('/sports');
}
