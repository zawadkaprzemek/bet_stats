import { useState } from 'react';
import { Link } from 'react-router-dom';

import { BetSlipForm } from '../components/bet-slip-form';
import { useBookmakers } from '../hooks/use-bookmakers';
import { useBetSlips, useCreateBetSlip } from '../hooks/use-bet-slips';
import { useCompetitions } from '../hooks/use-competitions';
import { useTags } from '../hooks/use-tags';
import type { BetSlipFilters } from '../types/bet-slip';

function getBetSlipTitle(name: string | null, description: string, id: number) {
  if (name && name.trim()) {
    return name;
  }

  if (description.trim()) {
    return description.slice(0, 70);
  }

  return `Bet slip #${id}`;
}

function formatMoney(value: string | null) {
  return value === null ? '—' : `${value} PLN`;
}

export function BetSlipsPage() {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [filters, setFilters] = useState<BetSlipFilters>({ page: 1, perPage: 20, status: '', search: '' });

  const bookmakersQuery = useBookmakers();
  const competitionsQuery = useCompetitions(null);
  const tagsQuery = useTags();
  const betSlipsQuery = useBetSlips(filters);
  const createBetSlipMutation = useCreateBetSlip();

  const bookmakers = bookmakersQuery.data ?? [];
  const competitions = competitionsQuery.data ?? [];
  const tags = tagsQuery.data ?? [];
  const betSlips = betSlipsQuery.data?.items ?? [];

  return (
    <div className="space-y-6">
      <section className="rounded-[32px] border border-white/10 bg-slate-900/70 p-6 shadow-[0_25px_80px_rgba(15,23,42,0.45)] backdrop-blur sm:p-8">
        <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div className="max-w-3xl">
            <p className="text-xs uppercase tracking-[0.45em] text-cyan-300/70">bet slip history</p>
            <h2 className="mt-4 text-3xl font-semibold text-white sm:text-4xl">Whole-coupon tracking, not event-by-event storage.</h2>
            <p className="mt-4 text-base leading-7 text-slate-300">
              Save the full bookmaker coupon with a single description, optional competitions, tags and settlement data.
            </p>
          </div>
          <button
            type="button"
            onClick={() => {
              setShowCreateForm((current) => !current);
            }}
            className="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200"
          >
            {showCreateForm ? 'Hide form' : 'Add bet slip'}
          </button>
        </div>
      </section>

      {showCreateForm ? (
        <BetSlipForm
          bookmakers={bookmakers}
          competitions={competitions}
          tags={tags}
          isPending={createBetSlipMutation.isPending}
          errorMessage={createBetSlipMutation.error instanceof Error ? createBetSlipMutation.error.message : null}
          submitLabel={createBetSlipMutation.isPending ? 'Saving...' : 'Save bet slip'}
          onCancel={() => {
            setShowCreateForm(false);
          }}
          onSubmit={async (payload) => {
            await createBetSlipMutation.mutateAsync(payload);
            setShowCreateForm(false);
          }}
        />
      ) : null}

      <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
        <div className="grid gap-4 md:grid-cols-4">
          <input
            type="search"
            value={filters.search ?? ''}
            onChange={(event) => {
              setFilters((current) => ({ ...current, search: event.target.value, page: 1 }));
            }}
            placeholder="Search name or description"
            className="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
          />
          <select
            value={filters.status ?? ''}
            onChange={(event) => {
              setFilters((current) => ({ ...current, status: event.target.value as BetSlipFilters['status'], page: 1 }));
            }}
            className="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
          >
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="won">Won</option>
            <option value="lost">Lost</option>
            <option value="void">Void</option>
            <option value="cashout">Cashout</option>
            <option value="partial_cashout">Partial cashout</option>
          </select>
          <select
            value={filters.bookmaker ?? ''}
            onChange={(event) => {
              setFilters((current) => ({ ...current, bookmaker: event.target.value ? Number(event.target.value) : '', page: 1 }));
            }}
            className="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
          >
            <option value="">All bookmakers</option>
            {bookmakers.map((bookmaker) => (
              <option key={bookmaker.id} value={bookmaker.id}>
                {bookmaker.name}
              </option>
            ))}
          </select>
          <select
            value={filters.tag ?? ''}
            onChange={(event) => {
              setFilters((current) => ({ ...current, tag: event.target.value ? Number(event.target.value) : '', page: 1 }));
            }}
            className="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
          >
            <option value="">All tags</option>
            {tags.map((tag) => (
              <option key={tag.id} value={tag.id}>
                {tag.name}
              </option>
            ))}
          </select>
        </div>
      </section>

      {betSlips.length === 0 ? (
        <section className="rounded-[28px] border border-dashed border-white/15 bg-slate-900/50 p-10 text-center">
          <h3 className="text-2xl font-semibold text-white">No bet slips yet</h3>
          <p className="mt-3 text-slate-300">Start with the first coupon and keep the workflow quick: one description, one slip, optional metadata.</p>
          <button
            type="button"
            onClick={() => {
              setShowCreateForm(true);
            }}
            className="mt-6 rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950"
          >
            Add the first bet slip
          </button>
        </section>
      ) : (
        <div className="grid gap-4">
          {betSlips.map((betSlip) => (
            <Link
              key={betSlip.id}
              to={`/kupony/${betSlip.id}`}
              className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)] transition hover:border-cyan-300/30"
            >
              <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div className="space-y-3">
                  <div className="flex flex-wrap items-center gap-3">
                    <span className="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-slate-300">
                      {betSlip.status}
                    </span>
                    <span className="text-sm text-slate-400">
                      {betSlip.bookmaker.emoji ? `${betSlip.bookmaker.emoji} ` : ''}
                      {betSlip.bookmaker.name}
                    </span>
                  </div>
                  <div>
                    <h3 className="text-2xl font-semibold text-white">{getBetSlipTitle(betSlip.name, betSlip.description, betSlip.id)}</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-300">{betSlip.description}</p>
                  </div>
                  <div className="flex flex-wrap gap-2">
                    {betSlip.tags.map((tag) => (
                      <span key={tag.id} className="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">
                        {tag.emoji ? `${tag.emoji} ` : ''}
                        {tag.name}
                      </span>
                    ))}
                  </div>
                </div>
                <dl className="grid min-w-[280px] gap-3 text-sm text-slate-300 sm:grid-cols-2">
                  <div>
                    <dt className="text-slate-500">Placed</dt>
                    <dd>{new Date(betSlip.placedAt).toLocaleString()}</dd>
                  </div>
                  <div>
                    <dt className="text-slate-500">Stake</dt>
                    <dd>{formatMoney(betSlip.stake)}</dd>
                  </div>
                  <div>
                    <dt className="text-slate-500">Total odds</dt>
                    <dd>{betSlip.totalOdds}</dd>
                  </div>
                  <div>
                    <dt className="text-slate-500">{betSlip.actualPayout ? 'Actual payout' : 'Predicted payout'}</dt>
                    <dd>{formatMoney(betSlip.actualPayout ?? betSlip.predictedPayout)}</dd>
                  </div>
                  <div>
                    <dt className="text-slate-500">Profit</dt>
                    <dd>{formatMoney(betSlip.profit)}</dd>
                  </div>
                </dl>
              </div>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}
