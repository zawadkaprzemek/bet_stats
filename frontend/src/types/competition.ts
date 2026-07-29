export type Competition = {
  id: number;
  sportId: number;
  name: string;
  countryCode: string | null;
  isGlobal: boolean;
};

export type CreateCompetitionInput = {
  sportId: number;
  name: string;
  countryCode?: string | null;
};
