import { zodResolver } from '@hookform/resolvers/zod';
import { useEffect } from 'react';
import { useForm, useWatch } from 'react-hook-form';

import { betSlipSettlementSchema, type BetSlipSettlementValues } from '../schemas/bet-slip';
import type { BetSlip, SettleBetSlipPayload } from '../types/bet-slip';

type BetSlipSettlementFormProps = {
  betSlip: BetSlip;
  isPending: boolean;
  errorMessage: string | null;
  onSubmit: (payload: SettleBetSlipPayload) => Promise<void>;
  onCancel?: () => void;
};

function formatDateTimeLocal(isoString: string) {
  const date = new Date(isoString);
  const year = date.getFullYear();
  const month = `${date.getMonth() + 1}`.padStart(2, '0');
  const day = `${date.getDate()}`.padStart(2, '0');
  const hours = `${date.getHours()}`.padStart(2, '0');
  const minutes = `${date.getMinutes()}`.padStart(2, '0');

  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

export function BetSlipSettlementForm({
  betSlip,
  isPending,
  errorMessage,
  onSubmit,
  onCancel,
}: BetSlipSettlementFormProps) {
  const form = useForm<BetSlipSettlementValues>({
    resolver: zodResolver(betSlipSettlementSchema),
    defaultValues: {
      status: betSlip.status === 'pending' ? 'won' : betSlip.status,
      usePredictedPayout: true,
      actualPayout: betSlip.actualPayout ?? '',
      settledAt: formatDateTimeLocal(new Date().toISOString()),
    },
  });

  const status = useWatch({ control: form.control, name: 'status' });
  const usePredictedPayout = useWatch({ control: form.control, name: 'usePredictedPayout' });

  useEffect(() => {
    if (status === 'lost') {
      form.setValue('actualPayout', '0.00');
    }

    if (status === 'void' && !form.getValues('actualPayout')) {
      form.setValue('actualPayout', betSlip.stake);
    }
  }, [betSlip.stake, form, status]);

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <form
        onSubmit={form.handleSubmit(async (values) => {
          const needsActualPayout =
            values.status === 'cashout' ||
            values.status === 'partial_cashout' ||
            values.status === 'void' ||
            (values.status === 'won' && !values.usePredictedPayout);

          await onSubmit({
            status: values.status,
            actualPayout:
              values.status === 'pending'
                ? null
                : values.status === 'lost'
                  ? '0.00'
                  : needsActualPayout
                    ? values.actualPayout || null
                    : null,
            settledAt: values.status === 'pending' ? null : new Date(values.settledAt || new Date().toISOString()).toISOString(),
          });
        })}
        className="space-y-5"
      >
        <div>
          <p className="text-sm font-medium text-cyan-300">Settle bet slip</p>
          <h3 className="mt-2 text-2xl font-semibold text-white">{betSlip.name || `Bet slip #${betSlip.id}`}</h3>
        </div>

        <label className="space-y-2">
          <span className="text-sm text-slate-200">Result</span>
          <select {...form.register('status')} className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white">
            <option value="won">Won</option>
            <option value="lost">Lost</option>
            <option value="void">Void</option>
            <option value="cashout">Cashout</option>
            <option value="partial_cashout">Partial cashout</option>
            <option value="pending">Pending</option>
          </select>
        </label>

        {status === 'won' ? (
          <div className="space-y-3 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
            <p className="text-sm font-medium text-white">Was the predicted payout received?</p>
            <div className="flex flex-wrap gap-3">
              <button
                type="button"
                onClick={() => {
                  form.setValue('usePredictedPayout', true);
                }}
                className={`rounded-full px-4 py-2 text-sm font-semibold ${
                  usePredictedPayout ? 'bg-cyan-300 text-slate-950' : 'border border-white/10 text-slate-200'
                }`}
              >
                Yes
              </button>
              <button
                type="button"
                onClick={() => {
                  form.setValue('usePredictedPayout', false);
                }}
                className={`rounded-full px-4 py-2 text-sm font-semibold ${
                  !usePredictedPayout ? 'bg-cyan-300 text-slate-950' : 'border border-white/10 text-slate-200'
                }`}
              >
                No, enter a different payout
              </button>
            </div>
            <p className="text-sm text-slate-400">Predicted payout: {betSlip.predictedPayout} PLN</p>
          </div>
        ) : null}

        {status !== 'pending' && status !== 'won' && status !== 'lost' ? (
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Actual payout</span>
            <input
              type="text"
              {...form.register('actualPayout')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
            />
          </label>
        ) : null}

        {status === 'won' && !usePredictedPayout ? (
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Actual payout</span>
            <input
              type="text"
              {...form.register('actualPayout')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
            />
          </label>
        ) : null}

        {status !== 'pending' ? (
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Settlement date and time</span>
            <input
              type="datetime-local"
              {...form.register('settledAt')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white"
            />
          </label>
        ) : null}

        <div className="flex flex-wrap items-center gap-3">
          <button
            type="submit"
            disabled={isPending}
            className="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {isPending ? 'Saving...' : 'Save settlement'}
          </button>
          {onCancel ? (
            <button
              type="button"
              onClick={onCancel}
              className="rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-slate-200"
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
