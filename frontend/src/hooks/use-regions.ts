import { useQuery } from '@tanstack/react-query';

import { fetchRegions } from '../api/regions';

export function regionsQueryKey() {
  return ['regions'] as const;
}

export function useRegions() {
  return useQuery({
    queryKey: regionsQueryKey(),
    queryFn: fetchRegions,
    staleTime: 60_000,
  });
}
