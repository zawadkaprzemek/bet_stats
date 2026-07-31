import { apiFetch } from './client';
import type { DashboardFilters, DashboardSummaryResponse } from '../types/dashboard';

function buildQueryString(filters: DashboardFilters) {
  const params = new URLSearchParams();

  Object.entries(filters).forEach(([key, value]) => {
    if (value === '' || value === undefined) {
      return;
    }

    params.set(key, String(value));
  });

  const query = params.toString();

  return query ? `?${query}` : '';
}

export function fetchDashboardSummary(filters: DashboardFilters = {}): Promise<DashboardSummaryResponse> {
  return apiFetch<DashboardSummaryResponse>(`/dashboard/summary${buildQueryString(filters)}`);
}
