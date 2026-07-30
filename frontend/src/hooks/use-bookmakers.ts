import { useQuery } from '@tanstack/react-query';

import { fetchBookmakers } from '../api/bookmakers';

export function useBookmakers() {
  return useQuery({
    queryKey: ['bookmakers'],
    queryFn: fetchBookmakers,
    staleTime: 5 * 60_000,
  });
}
