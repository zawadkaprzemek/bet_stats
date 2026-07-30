import { apiFetch } from './client';
import type { Region } from '../types/region';

export function fetchRegions(): Promise<Region[]> {
  return apiFetch<Region[]>('/regions');
}
