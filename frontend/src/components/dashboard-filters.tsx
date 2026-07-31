import { useEffect } from 'react';

import { useBookmakers } from '../hooks/use-bookmakers';
import { useCompetitions } from '../hooks/use-competitions';
import { useSports } from '../hooks/use-sports';
import { useTags } from '../hooks/use-tags';
import type { DashboardFilters } from '../types/dashboard';

type DashboardFiltersProps = {
  filters: DashboardFilters;
  onChange: (filters: DashboardFilters) => void;
};

const statusOptions = [
  { value: '', label: 'All statuses' },
  { value: 'pending', label: 'Pending' },
  { value: 'won', label: 'Won' },
  { value: 'lost', label: 'Lost' },
  { value: 'void', label: 'Void' },
  { value: 'cashout', label: 'Cashout' },
  { value: 'partial_cashout', label: 'Partial cashout' },
] as const;

export function DashboardFiltersPanel({ filters, onChange }: DashboardFiltersProps) {
  const bookmakersQuery = useBookmakers();
  const sportsQuery = useSports();
  const competitionsQuery = useCompetitions(typeof filters.sportId === 'number' ? filters.sportId : null);
  const tagsQuery = useTags();

  const bookmakers = bookmakersQuery.data ?? [];
  const sports = sportsQuery.data ?? [];
  const competitions = competitionsQuery.data;
  const tags = tagsQuery.data ?? [];

  useEffect(() => {
    if (
      typeof filters.competitionId === 'number' &&
      competitions &&
      competitions.length > 0 &&
      !competitions.some((competition) => competition.id === filters.competitionId)
    ) {
      onChange({ ...filters, competitionId: '' });
    }
  }, [competitions, filters, onChange]);

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p className="text-xs uppercase tracking-[0.35em] text-cyan-300/70">global filters</p>
          <h3 className="mt-2 text-xl font-semibold text-white">Slice the dashboard before charts and rankings arrive.</h3>
        </div>
        <button
          type="button"
          onClick={() => {
            onChange({});
          }}
          className="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-cyan-300/40 hover:text-white"
        >
          Clear filters
        </button>
      </div>

      <div className="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Date from</span>
          <input
            type="date"
            value={filters.dateFrom ?? ''}
            onChange={(event) => {
              onChange({ ...filters, dateFrom: event.target.value || undefined });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          />
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Date to</span>
          <input
            type="date"
            value={filters.dateTo ?? ''}
            onChange={(event) => {
              onChange({ ...filters, dateTo: event.target.value || undefined });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          />
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Bookmaker</span>
          <select
            value={filters.bookmakerId ?? ''}
            onChange={(event) => {
              onChange({ ...filters, bookmakerId: event.target.value ? Number(event.target.value) : '' });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          >
            <option value="">All bookmakers</option>
            {bookmakers.map((bookmaker) => (
              <option key={bookmaker.id} value={bookmaker.id}>
                {bookmaker.name}
              </option>
            ))}
          </select>
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Sport</span>
          <select
            value={filters.sportId ?? ''}
            onChange={(event) => {
              onChange({
                ...filters,
                sportId: event.target.value ? Number(event.target.value) : '',
                competitionId: '',
              });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          >
            <option value="">All sports</option>
            {sports.map((sport) => (
              <option key={sport.id} value={sport.id}>
                {sport.name}
              </option>
            ))}
          </select>
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Competition</span>
          <select
            value={filters.competitionId ?? ''}
            onChange={(event) => {
              onChange({ ...filters, competitionId: event.target.value ? Number(event.target.value) : '' });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          >
            <option value="">All competitions</option>
            {(competitions ?? []).map((competition) => (
              <option key={competition.id} value={competition.id}>
                {competition.name}
              </option>
            ))}
          </select>
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Tag</span>
          <select
            value={filters.tagId ?? ''}
            onChange={(event) => {
              onChange({ ...filters, tagId: event.target.value ? Number(event.target.value) : '' });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          >
            <option value="">All tags</option>
            {tags.map((tag) => (
              <option key={tag.id} value={tag.id}>
                {tag.name}
              </option>
            ))}
          </select>
        </label>

        <label className="space-y-2 text-sm text-slate-300">
          <span className="block text-xs uppercase tracking-[0.2em] text-slate-500">Status</span>
          <select
            value={filters.status ?? ''}
            onChange={(event) => {
              onChange({ ...filters, status: event.target.value as DashboardFilters['status'] });
            }}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300"
          >
            {statusOptions.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
        </label>
      </div>
    </section>
  );
}
