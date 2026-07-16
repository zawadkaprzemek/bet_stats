import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';

import { quickNoteSchema, type QuickNoteInput } from '../schemas/quick-note';

export function QuickNoteForm() {
  const form = useForm<QuickNoteInput>({
    resolver: zodResolver(quickNoteSchema),
    defaultValues: {
      title: '',
    },
  });

  const onSubmit = (values: QuickNoteInput) => {
    form.reset(values);
  };

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur">
      <div className="max-w-xl">
        <p className="text-sm font-medium text-cyan-300">React Hook Form + Zod</p>
        <h2 className="mt-2 text-2xl font-semibold text-white">Quick note placeholder</h2>
        <p className="mt-3 text-sm text-slate-300">
          Temporary form wired for the frontend stack. No domain logic yet.
        </p>
      </div>

      <form onSubmit={form.handleSubmit(onSubmit)} className="mt-6 flex flex-col gap-4">
        <label className="space-y-2">
          <span className="text-sm text-slate-200">Title</span>
          <input
            type="text"
            {...form.register('title')}
            className="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
            placeholder="Eg. First dashboard widget"
          />
        </label>

        {form.formState.errors.title ? (
          <p className="text-sm text-rose-300">{form.formState.errors.title.message}</p>
        ) : null}

        <div className="flex items-center gap-3">
          <button
            type="submit"
            className="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200"
          >
            Save placeholder
          </button>
          {form.formState.isSubmitSuccessful ? (
            <span className="text-sm text-emerald-300">Validated locally.</span>
          ) : null}
        </div>
      </form>
    </section>
  );
}
