import { useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';

import { BetSlipForm } from '../components/bet-slip-form';
import { BetSlipSettlementForm } from '../components/bet-slip-settlement-form';
import { useBookmakers } from '../hooks/use-bookmakers';
import { useBetSlip, useDeleteBetSlip, useSettleBetSlip, useUpdateBetSlip } from '../hooks/use-bet-slips';
import { useCompetitions } from '../hooks/use-competitions';
import { useTags } from '../hooks/use-tags';

function DetailRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
      <dt className="text-xs uppercase tracking-[0.2em] text-slate-500">{label}</dt>
      <dd className="mt-2 text-sm text-slate-100">{value}</dd>
    </div>
  );
}

export function BetSlipDetailsPage() {
  const params = useParams();
  const navigate = useNavigate();
  const betSlipId = params.id ? Number(params.id) : null;
  const [mode, setMode] = useState<'view' | 'edit' | 'settle'>('view');

  const betSlipQuery = useBetSlip(betSlipId);
  const bookmakersQuery = useBookmakers();
  const competitionsQuery = useCompetitions(null);
  const tagsQuery = useTags();
  const updateBetSlipMutation = useUpdateBetSlip();
  const settleBetSlipMutation = useSettleBetSlip();
  const deleteBetSlipMutation = useDeleteBetSlip();

  const betSlip = betSlipQuery.data;

  const competitions = competitionsQuery.data ?? [];

  if (betSlipId === null) {
    return <p className="text-sm text-rose-300">Missing bet slip id.</p>;
  }

  if (betSlipQuery.isLoading || !betSlip) {
    return <p className="text-sm text-slate-300">Loading bet slip...</p>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between gap-4">
        <div>
          <Link to="/kupony" className="text-sm text-cyan-300">← Back to history</Link>
          <h2 className="mt-3 text-3xl font-semibold text-white">{betSlip.name || `Bet slip #${betSlip.id}`}</h2>
          <p className="mt-2 max-w-3xl text-slate-300">{betSlip.description}</p>
        </div>
        <div className="flex flex-wrap gap-3">
          <button type="button" onClick={() => setMode('edit')} className="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200">Edit</button>
          <button type="button" onClick={() => setMode('settle')} className="rounded-full bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-950">Settle</button>
          <button
            type="button"
            onClick={async () => {
              if (!window.confirm('Delete this bet slip?')) {
                return;
              }

              await deleteBetSlipMutation.mutateAsync(betSlip.id);
              navigate('/kupony');
            }}
            className="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-semibold text-rose-200"
          >
            Delete
          </button>
        </div>
      </div>

      {mode === 'edit' ? (
        <BetSlipForm
          bookmakers={bookmakersQuery.data ?? []}
          competitions={competitions}
          tags={tagsQuery.data ?? []}
          initialBetSlip={betSlip}
          isPending={updateBetSlipMutation.isPending}
          errorMessage={updateBetSlipMutation.error instanceof Error ? updateBetSlipMutation.error.message : null}
          submitLabel={updateBetSlipMutation.isPending ? 'Saving...' : 'Save changes'}
          onCancel={() => setMode('view')}
          onSubmit={async (payload) => {
            await updateBetSlipMutation.mutateAsync({ id: betSlip.id, input: payload });
            setMode('view');
          }}
        />
      ) : null}

      {mode === 'settle' ? (
        <BetSlipSettlementForm
          betSlip={betSlip}
          isPending={settleBetSlipMutation.isPending}
          errorMessage={settleBetSlipMutation.error instanceof Error ? settleBetSlipMutation.error.message : null}
          onCancel={() => setMode('view')}
          onSubmit={async (payload) => {
            await settleBetSlipMutation.mutateAsync({ id: betSlip.id, input: payload });
            setMode('view');
          }}
        />
      ) : null}

      <section className="grid gap-4 lg:grid-cols-3">
        <DetailRow label="Bookmaker" value={betSlip.bookmaker.name} />
        <DetailRow label="Stake" value={`${betSlip.stake} PLN`} />
        <DetailRow label="Total odds" value={betSlip.totalOdds} />
        <DetailRow label="Tax" value={`${betSlip.taxPercent}%`} />
        <DetailRow label="Stake type" value={betSlip.stakeType} />
        <DetailRow label="Timing" value={betSlip.timing} />
        <DetailRow label="Status" value={betSlip.status} />
        <DetailRow label="Selections" value={betSlip.selectionsCount ? String(betSlip.selectionsCount) : '—'} />
        <DetailRow label="Predicted payout" value={`${betSlip.predictedPayout} PLN`} />
        <DetailRow label="Actual payout" value={betSlip.actualPayout ? `${betSlip.actualPayout} PLN` : '—'} />
        <DetailRow label="Profit" value={betSlip.profit ? `${betSlip.profit} PLN` : '—'} />
        <DetailRow label="ROI" value={betSlip.roi ? `${betSlip.roi}%` : '—'} />
        <DetailRow label="Placed at" value={new Date(betSlip.placedAt).toLocaleString()} />
        <DetailRow label="Settled at" value={betSlip.settledAt ? new Date(betSlip.settledAt).toLocaleString() : '—'} />
      </section>

      <section className="grid gap-6 xl:grid-cols-2">
        <article className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6">
          <h3 className="text-xl font-semibold text-white">Competitions</h3>
          <div className="mt-4 flex flex-wrap gap-2">
            {betSlip.competitions.length > 0 ? (
              betSlip.competitions.map((competition) => (
                <span key={competition.id} className="rounded-full border border-white/10 px-3 py-2 text-sm text-slate-200">
                  {competition.region?.emoji ? `${competition.region.emoji} ` : ''}
                  {competition.name} · {competition.sport.name}
                </span>
              ))
            ) : (
              <p className="text-sm text-slate-400">No competitions assigned.</p>
            )}
          </div>
        </article>

        <article className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6">
          <h3 className="text-xl font-semibold text-white">Tags</h3>
          <div className="mt-4 flex flex-wrap gap-2">
            {betSlip.tags.length > 0 ? (
              betSlip.tags.map((tag) => (
                <span key={tag.id} className="rounded-full border border-white/10 px-3 py-2 text-sm text-slate-200">
                  {tag.emoji ? `${tag.emoji} ` : ''}
                  {tag.name}
                </span>
              ))
            ) : (
              <p className="text-sm text-slate-400">No tags assigned.</p>
            )}
          </div>
        </article>
      </section>
    </div>
  );
}
