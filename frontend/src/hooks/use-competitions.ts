import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';

import { createCompetition, fetchCompetitions } from '../api/competitions';
import type { Competition, CreateCompetitionInput } from '../types/competition';

export function competitionsQueryKey(sportId: number) {
  return ['competitions', sportId] as const;
}

export function useCompetitions(sportId: number | null) {
  return useQuery({
    queryKey: sportId === null ? ['competitions', 'idle'] : competitionsQueryKey(sportId),
    queryFn: () => fetchCompetitions(sportId as number),
    enabled: sportId !== null,
    staleTime: 60_000,
  });
}

export function useCreateCompetition() {
  const queryClient = useQueryClient();

  return useMutation<Competition, Error, CreateCompetitionInput>({
    mutationFn: createCompetition,
    onSuccess: async (competition) => {
      await queryClient.invalidateQueries({
        queryKey: competitionsQueryKey(competition.sportId),
      });
    },
  });
}
