import { useQuery } from '@tanstack/react-query';

import { fetchSports } from '../api/sports';

export function useSports() {
  return useQuery({
    queryKey: ['sports'],
    queryFn: fetchSports,
    staleTime: 5 * 60_000,
  });
}
