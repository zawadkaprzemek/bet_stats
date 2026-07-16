import { appEnv } from '../utils/env';

const API_URL = appEnv.apiUrl;

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status?: number,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

type ApiErrorPayload = {
  message?: string;
};

export async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${API_URL}${path}`, {
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      ...(init?.headers ?? {}),
    },
    ...init,
  });

  if (!response.ok) {
    let errorMessage = `API request failed for ${path}`;

    try {
      const payload = (await response.clone().json()) as ApiErrorPayload;

      if (payload.message) {
        errorMessage = payload.message;
      }
    } catch {
      // Ignore non-JSON error bodies and keep the generic message.
    }

    throw new ApiError(errorMessage, response.status);
  }

  if (response.status === 204) {
    return undefined as T;
  }

  return (await response.json()) as T;
}
