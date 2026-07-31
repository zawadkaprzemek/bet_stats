import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';

import {
  createBetSlip,
  deleteBetSlip,
  fetchBetSlip,
  fetchBetSlips,
  settleBetSlip,
  updateBetSlip,
} from '../api/bet-slips';
import type {
  BetSlip,
  BetSlipFilters,
  CreateBetSlipPayload,
  SettleBetSlipPayload,
  UpdateBetSlipPayload,
} from '../types/bet-slip';

export function betSlipsQueryKey(filters: BetSlipFilters = {}) {
  return ['bet-slips', filters] as const;
}

export function betSlipDetailsQueryKey(id: number) {
  return ['bet-slip', id] as const;
}

export function useBetSlips(filters: BetSlipFilters) {
  return useQuery({
    queryKey: betSlipsQueryKey(filters),
    queryFn: () => fetchBetSlips(filters),
  });
}

export function useBetSlip(id: number | null) {
  return useQuery({
    queryKey: id === null ? ['bet-slip', 'idle'] : betSlipDetailsQueryKey(id),
    queryFn: () => fetchBetSlip(id as number),
    enabled: id !== null,
  });
}

export function useCreateBetSlip() {
  const queryClient = useQueryClient();

  return useMutation<BetSlip, Error, CreateBetSlipPayload>({
    mutationFn: createBetSlip,
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['bet-slips'] });
    },
  });
}

export function useUpdateBetSlip() {
  const queryClient = useQueryClient();

  return useMutation<BetSlip, Error, { id: number; input: UpdateBetSlipPayload }>({
    mutationFn: ({ id, input }) => updateBetSlip(id, input),
    onSuccess: async (betSlip) => {
      await queryClient.invalidateQueries({ queryKey: ['bet-slips'] });
      await queryClient.invalidateQueries({ queryKey: betSlipDetailsQueryKey(betSlip.id) });
    },
  });
}

export function useDeleteBetSlip() {
  const queryClient = useQueryClient();

  return useMutation<void, Error, number>({
    mutationFn: deleteBetSlip,
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['bet-slips'] });
    },
  });
}

export function useSettleBetSlip() {
  const queryClient = useQueryClient();

  return useMutation<BetSlip, Error, { id: number; input: SettleBetSlipPayload }>({
    mutationFn: ({ id, input }) => settleBetSlip(id, input),
    onSuccess: async (betSlip) => {
      await queryClient.invalidateQueries({ queryKey: ['bet-slips'] });
      await queryClient.invalidateQueries({ queryKey: betSlipDetailsQueryKey(betSlip.id) });
    },
  });
}
