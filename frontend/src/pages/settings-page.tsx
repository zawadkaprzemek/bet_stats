import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';

import { TagSelector } from '../components/tag-selector';
import { useCreateTag, useDeleteTag, useTags, useUpdateTag } from '../hooks/use-tags';
import { tagSchema, type TagFormInput } from '../schemas/tag';
import type { Tag } from '../types/tag';

function normalizeTagInput(values: TagFormInput) {
  return {
    name: values.name.trim(),
    emoji: values.emoji?.trim() || null,
    color: values.color?.trim().toUpperCase() || null,
  };
}

type TagFormCardProps = {
  submitLabel: string;
  title: string;
  description: string;
  initialValues?: TagFormInput;
  isPending: boolean;
  errorMessage: string | null;
  onSubmit: (values: TagFormInput) => Promise<void>;
  onCancel?: () => void;
};

function TagFormCard({
  submitLabel,
  title,
  description,
  initialValues,
  isPending,
  errorMessage,
  onSubmit,
  onCancel,
}: TagFormCardProps) {
  const form = useForm<TagFormInput>({
    resolver: zodResolver(tagSchema),
    values: initialValues ?? {
      name: '',
      emoji: '',
      color: '',
    },
  });

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <div className="max-w-xl">
        <p className="text-sm font-medium text-cyan-300">{title}</p>
        <p className="mt-2 text-sm leading-6 text-slate-300">{description}</p>
      </div>

      <form
        onSubmit={form.handleSubmit(async (values) => {
          await onSubmit(values);
          form.reset({
            name: '',
            emoji: '',
            color: '',
          });
        })}
        className="mt-6 grid gap-4 md:grid-cols-3"
      >
        <label className="space-y-2 md:col-span-1">
          <span className="text-sm text-slate-200">Name</span>
          <input
            type="text"
            {...form.register('name')}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            placeholder="Eg. Bankroll"
          />
          {form.formState.errors.name ? (
            <p className="text-sm text-rose-300">{form.formState.errors.name.message}</p>
          ) : null}
        </label>

        <label className="space-y-2">
          <span className="text-sm text-slate-200">Emoji</span>
          <input
            type="text"
            {...form.register('emoji')}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            placeholder="Eg. 🎯"
          />
          {form.formState.errors.emoji ? (
            <p className="text-sm text-rose-300">{form.formState.errors.emoji.message}</p>
          ) : null}
        </label>

        <label className="space-y-2">
          <span className="text-sm text-slate-200">Color</span>
          <input
            type="text"
            {...form.register('color')}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm uppercase text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            placeholder="#22C55E"
          />
          {form.formState.errors.color ? (
            <p className="text-sm text-rose-300">{form.formState.errors.color.message}</p>
          ) : null}
        </label>

        <div className="md:col-span-3 flex flex-wrap items-center gap-3">
          <button
            type="submit"
            disabled={isPending}
            className="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {submitLabel}
          </button>
          {onCancel ? (
            <button
              type="button"
              onClick={onCancel}
              className="rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:border-white/20 hover:text-white"
            >
              Cancel
            </button>
          ) : null}
          {errorMessage ? <p className="text-sm text-rose-300">{errorMessage}</p> : null}
        </div>
      </form>
    </section>
  );
}

function TagList({
  tags,
  onEdit,
  onDelete,
  isDeleting,
}: {
  tags: Tag[];
  onEdit: (tag: Tag) => void;
  onDelete: (tagId: number) => Promise<void>;
  isDeleting: boolean;
}) {
  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <div className="flex items-center justify-between gap-4">
        <div>
          <p className="text-sm font-medium text-cyan-300">Visible tags</p>
          <h2 className="mt-2 text-2xl font-semibold text-white">System and personal labels</h2>
        </div>
      </div>

      <div className="mt-6 grid gap-4">
        {tags.map((tag) => (
          <article
            key={tag.id}
            className="rounded-[24px] border border-white/10 bg-slate-950/50 p-5"
          >
            <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div className="space-y-3">
                <div className="flex flex-wrap items-center gap-3">
                  <span
                    className="inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em]"
                    style={{
                      borderColor: tag.color ?? 'rgba(148, 163, 184, 0.25)',
                      color: tag.color ?? '#CBD5E1',
                    }}
                  >
                    {tag.isSystem ? 'System' : 'Private'}
                  </span>
                  {tag.color ? (
                    <span className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.22em] text-slate-400">
                      <span
                        className="h-3 w-3 rounded-full"
                        style={{ backgroundColor: tag.color }}
                      />
                      {tag.color}
                    </span>
                  ) : null}
                </div>

                <div>
                  <h3 className="text-lg font-semibold text-white">
                    {tag.emoji ? `${tag.emoji} ` : ''}
                    {tag.name}
                  </h3>
                  <p className="mt-1 text-sm text-slate-400">{tag.slug}</p>
                </div>
              </div>

              {!tag.isSystem ? (
                <div className="flex items-center gap-3">
                  <button
                    type="button"
                    onClick={() => {
                      onEdit(tag);
                    }}
                    className="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-cyan-300 hover:text-white"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    disabled={isDeleting}
                    onClick={async () => {
                      await onDelete(tag.id);
                    }}
                    className="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:border-rose-300 hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    Delete
                  </button>
                </div>
              ) : null}
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

export function SettingsPage() {
  const tagsQuery = useTags();
  const createTagMutation = useCreateTag();
  const updateTagMutation = useUpdateTag();
  const deleteTagMutation = useDeleteTag();

  const [editingTag, setEditingTag] = useState<Tag | null>(null);
  const [selectedTagIds, setSelectedTagIds] = useState<number[]>([]);

  const tags = tagsQuery.data ?? [];

  return (
    <div className="space-y-6">
      <section className="rounded-[32px] border border-white/10 bg-slate-900/70 p-6 shadow-[0_25px_80px_rgba(15,23,42,0.45)] backdrop-blur sm:p-8">
        <p className="text-xs uppercase tracking-[0.45em] text-cyan-300/70">tag system</p>
        <div className="mt-4 max-w-3xl">
          <h2 className="text-3xl font-semibold text-white sm:text-4xl">Personal taxonomy for bets and filters.</h2>
          <p className="mt-4 text-base leading-7 text-slate-300">
            System tags are shared across the app. Private tags belong only to the current user and
            can be edited or deleted here.
          </p>
        </div>
      </section>

      {editingTag ? (
        <TagFormCard
          title="Edit private tag"
          description="Update the label, emoji or accent color. System tags stay read-only for regular users."
          submitLabel={updateTagMutation.isPending ? 'Saving...' : 'Save changes'}
          initialValues={{
            name: editingTag.name,
            emoji: editingTag.emoji ?? '',
            color: editingTag.color ?? '',
          }}
          isPending={updateTagMutation.isPending}
          errorMessage={updateTagMutation.error instanceof Error ? updateTagMutation.error.message : null}
          onCancel={() => {
            setEditingTag(null);
          }}
          onSubmit={async (values) => {
            await updateTagMutation.mutateAsync({
              id: editingTag.id,
              input: normalizeTagInput(values),
            });
            setEditingTag(null);
          }}
        />
      ) : (
        <TagFormCard
          title="Create private tag"
          description="Create labels for recurring bet themes, strategies or workflows."
          submitLabel={createTagMutation.isPending ? 'Creating...' : 'Create tag'}
          isPending={createTagMutation.isPending}
          errorMessage={createTagMutation.error instanceof Error ? createTagMutation.error.message : null}
          onSubmit={async (values) => {
            await createTagMutation.mutateAsync(normalizeTagInput(values));
          }}
        />
      )}

      {tagsQuery.error instanceof Error ? (
        <section className="rounded-[28px] border border-rose-400/20 bg-rose-950/30 p-6 text-sm text-rose-200">
          {tagsQuery.error.message}
        </section>
      ) : null}

      {tagsQuery.isLoading ? (
        <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 text-sm text-slate-300">
          Loading tags...
        </section>
      ) : (
        <>
          <TagList
            tags={tags}
            isDeleting={deleteTagMutation.isPending}
            onEdit={(tag) => {
              setEditingTag(tag);
            }}
            onDelete={async (tagId) => {
              await deleteTagMutation.mutateAsync(tagId);
              setSelectedTagIds((currentSelectedTagIds) =>
                currentSelectedTagIds.filter((selectedTagId) => selectedTagId !== tagId),
              );
            }}
          />

          <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
            <p className="text-sm font-medium text-cyan-300">Tag selector</p>
            <h2 className="mt-2 text-2xl font-semibold text-white">Selection preview for future bet forms</h2>
            <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
              This component is ready to be reused when coupon and bet forms are introduced.
            </p>

            <div className="mt-6">
              <TagSelector
                tags={tags}
                selectedTagIds={selectedTagIds}
                onChange={setSelectedTagIds}
              />
            </div>
          </section>
        </>
      )}
    </div>
  );
}
