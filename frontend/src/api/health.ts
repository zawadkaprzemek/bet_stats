import { apiFetch } from './client';
import type { HealthStatus } from '../types/health';

export function fetchHealthStatus(): Promise<HealthStatus> {
  return apiFetch<HealthStatus>('/health');
}
