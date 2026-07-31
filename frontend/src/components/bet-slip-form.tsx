import { zodResolver } from '@hookform/resolvers/zod';
import { useEffect, useRef } from 'react';
import { Controller, useForm, useWatch } from 'react-hook-form';

import { betSlipFormSchema, type BetSlipFormValues } from '../schemas/bet-slip';
import type { BetSlip, BetSlipCompetition, BetSlipStakeType, CreateBetSlipPayload } from '../types/bet-slip';
import type { Bookmaker } from '../types/bookmaker';
import type { Tag } from '../types/tag';
import { CompetitionSelector } from './competition-selector';
import { TagSelector } from './tag-selector';

type BetSlipFormProps = {
  bookmakers: Bookmaker[];
  competitions: BetSlipCompetition[];
  tags: Tag[];
  initialBetSlip?: BetSlip;
  isPending: boolean;
  errorMessage: string | null;
  submitLabel: string;
  onSubmit: (payload: CreateBetSlipPayload) => Promise<void>;
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

function normalizeNumberString(value: string, digits: number) {
  return Number.parseFloat(value).toFixed(digits);
}

function calculatePredictedPayoutPreview(
  stake: string,
  totalOdds: string,
  taxPercent: string,
  stakeType: BetSlipStakeType,
) {
  const stakeValue = Number.parseFloat(stake);
  const oddsValue = Number.parseFloat(totalOdds);
  const taxValue = Number.parseFloat(taxPercent);

  if (!Number.isFinite(stakeValue) || !Number.isFinite(oddsValue) || !Number.isFinite(taxValue)) {
    return null;
  }

  const taxedStake = stakeValue * (1 - taxValue / 100);
  const multiplier = stakeType === 'cash' ? oddsValue : Math.max(0, oddsValue - 1);

  return (taxedStake * multiplier).toFixed(2);
}

export function BetSlipForm({
  bookmakers,
  competitions,
  tags,
  initialBetSlip,
  isPending,
  errorMessage,
  submitLabel,
  onSubmit,
  onCancel,
}: BetSlipFormProps) {
  const form = useForm<BetSlipFormValues>({
    resolver: zodResolver(betSlipFormSchema),
    defaultValues: {
      name: initialBetSlip?.name ?? '',
      description: initialBetSlip?.description ?? '',
      bookmakerId: initialBetSlip?.bookmaker.id ?? bookmakers[0]?.id ?? 0,
      stake: initialBetSlip?.stake ?? '',
      totalOdds: initialBetSlip?.totalOdds ?? '',
      stakeType: initialBetSlip?.stakeType ?? 'cash',
      timing: initialBetSlip?.timing ?? 'pre_match',
      selectionsCount: initialBetSlip?.selectionsCount ? String(initialBetSlip.selectionsCount) : '',
      taxPercent: initialBetSlip?.taxPercent ?? bookmakers[0]?.defaultTaxPercent ?? '0.00',
      competitionIds: initialBetSlip?.competitions.map((competition) => competition.id) ?? [],
      tagIds: initialBetSlip?.tags.map((tag) => tag.id) ?? [],
      placedAt: initialBetSlip?.placedAt ? formatDateTimeLocal(initialBetSlip.placedAt) : formatDateTimeLocal(new Date().toISOString()),
    },
  });

  const selectedBookmakerId = useWatch({ control: form.control, name: 'bookmakerId' });
  const selectedStake = useWatch({ control: form.control, name: 'stake' });
  const selectedOdds = useWatch({ control: form.control, name: 'totalOdds' });
  const selectedStakeType = useWatch({ control: form.control, name: 'stakeType' });
  const selectedTaxPercent = useWatch({ control: form.control, name: 'taxPercent' });
  const taxTouchedRef = useRef(Boolean(initialBetSlip));

  useEffect(() => {
    const subscription = form.watch((_values, { name }) => {
      if (name === 'taxPercent') {
        taxTouchedRef.current = true;
      }
    });

    return () => {
      subscription.unsubscribe();
    };
  }, [form]);

  useEffect(() => {
    const bookmaker = bookmakers.find((item) => item.id === selectedBookmakerId);

    if (!bookmaker || taxTouchedRef.current) {
      return;
    }

    form.setValue('taxPercent', bookmaker.defaultTaxPercent, { shouldValidate: true });
  }, [bookmakers, form, selectedBookmakerId]);

  const preview = calculatePredictedPayoutPreview(
    selectedStake,
    selectedOdds,
    selectedTaxPercent,
    selectedStakeType,
  );

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <form
        onSubmit={form.handleSubmit(async (values) => {
          await onSubmit({
            name: values.name?.trim() || null,
            description: values.description.trim(),
            bookmakerId: values.bookmakerId,
            stake: normalizeNumberString(values.stake, 2),
            totalOdds: normalizeNumberString(values.totalOdds, 3),
            stakeType: values.stakeType,
            timing: values.timing,
            selectionsCount: values.selectionsCount ? Number.parseInt(values.selectionsCount, 10) : null,
            taxPercent: normalizeNumberString(values.taxPercent, 2),
            competitionIds: values.competitionIds,
            tagIds: values.tagIds,
            placedAt: new Date(values.placedAt).toISOString(),
          });
        })}
        className="space-y-6"
      >
        <div className="grid gap-4 lg:grid-cols-2">
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Bet slip name</span>
            <input
              type="text"
              {...form.register('name')}
              placeholder="Weekend accumulator"
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            />
            <p className="text-xs text-slate-400">Optional. For example: Weekend accumulator.</p>
            {form.formState.errors.name ? <p className="text-sm text-rose-300">{form.formState.errors.name.message}</p> : null}
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Bookmaker</span>
            <select
              {...form.register('bookmakerId', { valueAsNumber: true })}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-300"
            >
              {bookmakers.map((bookmaker) => (
                <option key={bookmaker.id} value={bookmaker.id}>
                  {bookmaker.emoji ? `${bookmaker.emoji} ` : ''}
                  {bookmaker.name}
                </option>
              ))}
            </select>
            {form.formState.errors.bookmakerId ? <p className="text-sm text-rose-300">{form.formState.errors.bookmakerId.message}</p> : null}
          </label>
        </div>

        <label className="space-y-2">
          <span className="text-sm text-slate-200">Description</span>
          <textarea
            {...form.register('description')}
            rows={4}
            placeholder="Describe the whole coupon. Do not create separate event entries here."
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
          />
          <p className="text-xs text-slate-400">
            Describe the entire coupon in free text instead of entering each event separately.
          </p>
          {form.formState.errors.description ? <p className="text-sm text-rose-300">{form.formState.errors.description.message}</p> : null}
        </label>

        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Stake</span>
            <input
              type="text"
              {...form.register('stake')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            />
            {form.formState.errors.stake ? <p className="text-sm text-rose-300">{form.formState.errors.stake.message}</p> : null}
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Total odds</span>
            <input
              type="text"
              {...form.register('totalOdds')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            />
            {form.formState.errors.totalOdds ? <p className="text-sm text-rose-300">{form.formState.errors.totalOdds.message}</p> : null}
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Number of selections</span>
            <input
              type="text"
              {...form.register('selectionsCount')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            />
            {form.formState.errors.selectionsCount ? <p className="text-sm text-rose-300">{form.formState.errors.selectionsCount.message}</p> : null}
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Tax percentage</span>
            <input
              type="text"
              {...form.register('taxPercent')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            />
            {form.formState.errors.taxPercent ? <p className="text-sm text-rose-300">{form.formState.errors.taxPercent.message}</p> : null}
          </label>
        </div>

        <div className="grid gap-4 md:grid-cols-3">
          <label className="space-y-2">
            <span className="text-sm text-slate-200">Stake type</span>
            <select {...form.register('stakeType')} className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white">
              <option value="cash">Cash</option>
              <option value="freebet">Freebet</option>
              <option value="bonus">Bonus</option>
            </select>
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Timing</span>
            <select {...form.register('timing')} className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white">
              <option value="pre_match">Pre-match</option>
              <option value="live">Live</option>
              <option value="hybrid">Hybrid</option>
            </select>
          </label>

          <label className="space-y-2">
            <span className="text-sm text-slate-200">Placed date and time</span>
            <input
              type="datetime-local"
              {...form.register('placedAt')}
              className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-300"
            />
            {form.formState.errors.placedAt ? <p className="text-sm text-rose-300">{form.formState.errors.placedAt.message}</p> : null}
          </label>
        </div>

        <div className="rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-4">
          <p className="text-xs uppercase tracking-[0.24em] text-cyan-300/80">Estimate</p>
          <p className="mt-2 text-2xl font-semibold text-white">{preview ? `${preview} PLN` : 'Fill stake, odds and tax.'}</p>
          <p className="mt-2 text-sm text-slate-300">Frontend preview only. The backend remains the final source of truth.</p>
        </div>

        <div className="grid gap-6 xl:grid-cols-2">
          <div className="space-y-3">
            <div>
              <h3 className="text-lg font-semibold text-white">Competitions</h3>
              <p className="text-sm text-slate-400">Optional cross-sport assignment for the whole coupon.</p>
            </div>
            <Controller
              control={form.control}
              name="competitionIds"
              render={({ field }) => (
                <CompetitionSelector competitions={competitions} selectedCompetitionIds={field.value} onChange={field.onChange} />
              )}
            />
          </div>

          <div className="space-y-3">
            <div>
              <h3 className="text-lg font-semibold text-white">Tags</h3>
              <p className="text-sm text-slate-400">Optional labels for faster filtering later.</p>
            </div>
            <Controller
              control={form.control}
              name="tagIds"
              render={({ field }) => <TagSelector tags={tags} selectedTagIds={field.value} onChange={field.onChange} />}
            />
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-3">
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
