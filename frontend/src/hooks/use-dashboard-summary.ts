import { useQuery } from '@tanstack/react-query';

import { fetchDashboardSummary } from '../api/dashboard';
import type { DashboardFilters } from '../types/dashboard';

export function dashboardSummaryQueryKey(filters: DashboardFilters = {}) {
  return ['dashboard-summary', filters] as const;
}

export function useDashboardSummary(filters: DashboardFilters) {
  return useQuery({
    queryKey: dashboardSummaryQueryKey(filters),
    queryFn: () => fetchDashboardSummary(filters),
  });
}
