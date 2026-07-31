import { useState } from 'react';
import { Link } from 'react-router-dom';

import { DashboardFiltersPanel } from '../components/dashboard-filters';
import { useDashboardSummary } from '../hooks/use-dashboard-summary';
import type { DashboardFilters } from '../types/dashboard';

function formatMoney(value: string) {
  return `${value} PLN`;
}

function formatPercent(value: string) {
  return `${value}%`;
}

function currentStreakLabel(currentWinningStreak: number, currentLosingStreak: number) {
  if (currentWinningStreak > 0) {
    return `${currentWinningStreak} wins`;
  }

  if (currentLosingStreak > 0) {
    return `${currentLosingStreak} losses`;
  }

  return 'No active streak';
}

export function DashboardPage() {
  const [filters, setFilters] = useState<DashboardFilters>({});
  const dashboardSummaryQuery = useDashboardSummary(filters);

  const summary = dashboardSummaryQuery.data?.summary;
  const hasBetSlips = (summary?.totalBetSlips ?? 0) > 0;
  const cards = summary
    ? [
        { title: 'Total Bet Slips', value: String(summary.totalBetSlips), note: `${summary.settledBetSlips} settled · ${summary.pendingBetSlips} pending` },
        { title: 'Total Stake', value: formatMoney(summary.totalStake), note: 'All matched bet slips in the current filter set' },
        { title: 'Total Profit', value: formatMoney(summary.totalProfit), note: 'Settled slips only' },
        { title: 'ROI', value: formatPercent(summary.roi), note: 'Settled cash slips only' },
        { title: 'Win Rate', value: formatPercent(summary.winRate), note: 'Won slips divided by settled slips' },
        { title: 'Average Stake', value: formatMoney(summary.averageStake), note: 'Across all matched bet slips' },
        { title: 'Average Odds', value: summary.averageOdds, note: 'Across all matched bet slips' },
        { title: 'Current Streak', value: currentStreakLabel(summary.currentWinningStreak, summary.currentLosingStreak), note: 'Latest settled sequence' },
        { title: 'Best Winning Streak', value: String(summary.bestWinningStreak), note: 'Longest consecutive run of wins' },
        { title: 'Worst Losing Streak', value: String(summary.worstLosingStreak), note: 'Longest consecutive run of losses' },
      ]
    : [];

  return (
    <div className="space-y-6">
      <section className="relative overflow-hidden rounded-[32px] border border-white/10 bg-slate-900/70 p-6 shadow-[0_25px_80px_rgba(15,23,42,0.45)] backdrop-blur sm:p-8">
        <div className="pointer-events-none absolute hidden h-32 w-32 rounded-full bg-cyan-300/15 blur-3xl lg:block" />
        <p className="text-xs uppercase tracking-[0.45em] text-cyan-300/70">dashboard summary</p>
        <div className="mt-4 max-w-3xl">
          <h2 className="text-3xl font-semibold text-white sm:text-4xl">Read the account at a glance before deeper analytics land.</h2>
          <p className="mt-4 text-base leading-7 text-slate-300">
            This first dashboard module stays deliberately narrow: summary statistics, reusable filters and a clean API surface for future charts and rankings.
          </p>
        </div>
      </section>

      <DashboardFiltersPanel filters={filters} onChange={setFilters} />

      {dashboardSummaryQuery.isLoading ? (
        <section className="rounded-[28px] border border-white/10 bg-slate-900/75 p-10 text-center text-slate-300 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
          Loading summary…
        </section>
      ) : null}

      {dashboardSummaryQuery.isError ? (
        <section className="rounded-[28px] border border-rose-400/30 bg-rose-500/10 p-8 text-rose-100 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
          {(dashboardSummaryQuery.error as Error).message}
        </section>
      ) : null}

      {!dashboardSummaryQuery.isLoading && !dashboardSummaryQuery.isError && !hasBetSlips ? (
        <section className="rounded-[32px] border border-dashed border-white/15 bg-[linear-gradient(135deg,rgba(8,47,73,0.85),rgba(15,23,42,0.92))] p-10 text-center shadow-[0_25px_80px_rgba(8,47,73,0.25)]">
          <p className="text-xs uppercase tracking-[0.35em] text-cyan-300/70">empty state</p>
          <h3 className="mt-4 text-3xl font-semibold text-white">The dashboard wakes up after the first bet slip.</h3>
          <p className="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-300">
            There is nothing to summarize yet, so the MVP intentionally avoids showing a wall of zeroes.
          </p>
          <Link
            to="/kupony"
            className="mt-8 inline-flex rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200"
          >
            Create your first bet slip
          </Link>
        </section>
      ) : null}

      {!dashboardSummaryQuery.isLoading && !dashboardSummaryQuery.isError && hasBetSlips ? (
        <div className="grid gap-4 md:grid-cols-2 2xl:grid-cols-5">
          {cards.map((card) => (
            <article
              key={card.title}
              className="rounded-[28px] border border-white/10 bg-slate-900/80 p-5 shadow-[0_20px_50px_rgba(15,23,42,0.3)]"
            >
              <p className="text-xs uppercase tracking-[0.22em] text-slate-500">{card.title}</p>
              <p className="mt-4 text-3xl font-semibold text-white">{card.value}</p>
              <p className="mt-3 text-sm leading-6 text-slate-300">{card.note}</p>
            </article>
          ))}
        </div>
      ) : null}
    </div>
  );
}
