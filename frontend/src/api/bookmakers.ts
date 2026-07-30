import { apiFetch } from './client';
import type { Bookmaker } from '../types/bookmaker';

export function fetchBookmakers(): Promise<Bookmaker[]> {
  return apiFetch<Bookmaker[]>('/bookmakers');
}
