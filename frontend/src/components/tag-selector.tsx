import type { Tag } from '../types/tag';

type TagSelectorProps = {
  tags: Tag[];
  selectedTagIds: number[];
  onChange: (nextSelectedTagIds: number[]) => void;
};

export function TagSelector({ tags, selectedTagIds, onChange }: TagSelectorProps) {
  const toggleTag = (tagId: number) => {
    onChange(
      selectedTagIds.includes(tagId)
        ? selectedTagIds.filter((selectedTagId) => selectedTagId !== tagId)
        : [...selectedTagIds, tagId],
    );
  };

  return (
    <div className="flex flex-wrap gap-3">
      {tags.map((tag) => {
        const isSelected = selectedTagIds.includes(tag.id);

        return (
          <button
            key={tag.id}
            type="button"
            onClick={() => {
              toggleTag(tag.id);
            }}
            className="rounded-full border px-4 py-2 text-sm font-medium transition"
            style={{
              borderColor: tag.color ?? (isSelected ? '#67E8F9' : 'rgba(148, 163, 184, 0.3)'),
              backgroundColor: isSelected ? 'rgba(8, 145, 178, 0.18)' : 'rgba(15, 23, 42, 0.6)',
              color: '#E2E8F0',
            }}
          >
            <span className="inline-flex items-center gap-2">
              {tag.emoji ? <span>{tag.emoji}</span> : null}
              <span>{tag.name}</span>
            </span>
          </button>
        );
      })}
    </div>
  );
}
