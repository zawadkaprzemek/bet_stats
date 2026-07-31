import { useMemo, useState } from 'react';

import type { BetSlipCompetition } from '../types/bet-slip';

type CompetitionSelectorProps = {
  competitions: BetSlipCompetition[];
  selectedCompetitionIds: number[];
  onChange: (ids: number[]) => void;
};

function buildCompetitionLabel(competition: BetSlipCompetition) {
  const regionLabel = competition.region?.emoji ? `${competition.region.emoji} ` : '';
  const sportLabel = competition.sport.emoji ? `${competition.sport.emoji} ${competition.sport.name}` : competition.sport.name;

  return `${regionLabel}${competition.name} · ${sportLabel}`;
}

export function CompetitionSelector({
  competitions,
  selectedCompetitionIds,
  onChange,
}: CompetitionSelectorProps) {
  const [query, setQuery] = useState('');

  const filteredCompetitions = useMemo(() => {
    const normalizedQuery = query.trim().toLowerCase();

    if (!normalizedQuery) {
      return competitions;
    }

    return competitions.filter((competition) =>
      buildCompetitionLabel(competition).toLowerCase().includes(normalizedQuery),
    );
  }, [competitions, query]);

  const selectedCompetitions = competitions.filter((competition) => selectedCompetitionIds.includes(competition.id));

  return (
    <div className="space-y-4">
      <input
        type="search"
        value={query}
        onChange={(event) => {
          setQuery(event.target.value);
        }}
        placeholder="Search competitions across sports"
        className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
      />

      {selectedCompetitions.length > 0 ? (
        <div className="flex flex-wrap gap-2">
          {selectedCompetitions.map((competition) => (
            <button
              key={competition.id}
              type="button"
              onClick={() => {
                onChange(selectedCompetitionIds.filter((id) => id !== competition.id));
              }}
              className="rounded-full border border-cyan-300/40 bg-cyan-300/10 px-3 py-2 text-xs font-semibold text-cyan-100"
            >
              {buildCompetitionLabel(competition)} ×
            </button>
          ))}
        </div>
      ) : null}

      <div className="max-h-56 space-y-2 overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 p-3">
        {filteredCompetitions.map((competition) => {
          const selected = selectedCompetitionIds.includes(competition.id);

          return (
            <button
              key={competition.id}
              type="button"
              onClick={() => {
                onChange(
                  selected
                    ? selectedCompetitionIds.filter((id) => id !== competition.id)
                    : [...selectedCompetitionIds, competition.id],
                );
              }}
              className={`flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left text-sm transition ${
                selected
                  ? 'border border-cyan-300/40 bg-cyan-300/10 text-white'
                  : 'border border-white/10 bg-slate-900/70 text-slate-200 hover:border-white/20'
              }`}
            >
              <span>{buildCompetitionLabel(competition)}</span>
              <span className="text-xs uppercase tracking-[0.2em] text-slate-400">
                {competition.isGlobal ? 'System' : 'Private'}
              </span>
            </button>
          );
        })}
      </div>
    </div>
  );
}
