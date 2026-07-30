export type Tag = {
  id: number;
  name: string;
  slug: string;
  emoji: string | null;
  color: string | null;
  isSystem: boolean;
};

export type TagInput = {
  name: string;
  emoji?: string | null;
  color?: string | null;
};
