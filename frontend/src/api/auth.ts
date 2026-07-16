import { apiFetch } from './client';
import type { AuthUser } from '../types/auth';

type LoginPayload = {
  email: string;
  password: string;
};

export function login(payload: LoginPayload): Promise<AuthUser> {
  return apiFetch<AuthUser>('/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  });
}

export function logout(): Promise<void> {
  return apiFetch<void>('/logout', {
    method: 'POST',
  });
}

export function fetchCurrentUser(): Promise<AuthUser> {
  return apiFetch<AuthUser>('/me');
}
