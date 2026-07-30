import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';

import { createTag, deleteTag, fetchTags, updateTag } from '../api/tags';
import type { Tag, TagInput } from '../types/tag';

const TAGS_QUERY_KEY = ['tags'];

export function useTags() {
  return useQuery({
    queryKey: TAGS_QUERY_KEY,
    queryFn: fetchTags,
    staleTime: 60_000,
  });
}

export function useCreateTag() {
  const queryClient = useQueryClient();

  return useMutation<Tag, Error, TagInput>({
    mutationFn: createTag,
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: TAGS_QUERY_KEY });
    },
  });
}

export function useUpdateTag() {
  const queryClient = useQueryClient();

  return useMutation<Tag, Error, { id: number; input: TagInput }>({
    mutationFn: ({ id, input }) => updateTag(id, input),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: TAGS_QUERY_KEY });
    },
  });
}

export function useDeleteTag() {
  const queryClient = useQueryClient();

  return useMutation<void, Error, number>({
    mutationFn: deleteTag,
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: TAGS_QUERY_KEY });
    },
  });
}
