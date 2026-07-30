import { apiFetch } from './client';
import type { Tag, TagInput } from '../types/tag';

export function fetchTags(): Promise<Tag[]> {
  return apiFetch<Tag[]>('/tags');
}

export function createTag(input: TagInput): Promise<Tag> {
  return apiFetch<Tag>('/tags', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  });
}

export function updateTag(id: number, input: TagInput): Promise<Tag> {
  return apiFetch<Tag>(`/tags/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  });
}

export function deleteTag(id: number): Promise<void> {
  return apiFetch<void>(`/tags/${id}`, {
    method: 'DELETE',
  });
}
