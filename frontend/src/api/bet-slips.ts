import { apiFetch } from './client';
import type {
  BetSlip,
  BetSlipFilters,
  BetSlipListResponse,
  CreateBetSlipPayload,
  SettleBetSlipPayload,
  UpdateBetSlipPayload,
} from '../types/bet-slip';

function buildQueryString(filters: BetSlipFilters) {
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

export function fetchBetSlips(filters: BetSlipFilters = {}): Promise<BetSlipListResponse> {
  return apiFetch<BetSlipListResponse>(`/bet-slips${buildQueryString(filters)}`);
}

export function fetchBetSlip(id: number): Promise<BetSlip> {
  return apiFetch<BetSlip>(`/bet-slips/${id}`);
}

export function createBetSlip(input: CreateBetSlipPayload): Promise<BetSlip> {
  return apiFetch<BetSlip>('/bet-slips', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(input),
  });
}

export function updateBetSlip(id: number, input: UpdateBetSlipPayload): Promise<BetSlip> {
  return apiFetch<BetSlip>(`/bet-slips/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(input),
  });
}

export function deleteBetSlip(id: number): Promise<void> {
  return apiFetch<void>(`/bet-slips/${id}`, {
    method: 'DELETE',
  });
}

export function settleBetSlip(id: number, input: SettleBetSlipPayload): Promise<BetSlip> {
  return apiFetch<BetSlip>(`/bet-slips/${id}/settle`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(input),
  });
}
