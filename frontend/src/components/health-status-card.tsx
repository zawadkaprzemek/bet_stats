import { useQuery } from '@tanstack/react-query';

import { fetchHealthStatus } from '../api/health';
import { appEnv } from '../utils/env';

function formatError(message: string) {
  return message.length > 96 ? `${message.slice(0, 93)}...` : message;
}

export function HealthStatusCard() {
  const query = useQuery({
    queryKey: ['health-status'],
    queryFn: fetchHealthStatus,
    retry: 1,
    staleTime: 30_000,
  });

  return (
    <section className="rounded-[28px] border border-white/10 bg-slate-900/80 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur">
      <div className="flex items-start justify-between gap-4">
        <div>
          <p className="text-sm font-medium text-emerald-300">Backend status</p>
          <h2 className="mt-2 text-2xl font-semibold text-white">GET /api/health</h2>
        </div>
        <span
          className={`rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] ${
            query.isSuccess
              ? 'bg-emerald-400/15 text-emerald-300'
              : query.isError
                ? 'bg-rose-400/15 text-rose-300'
                : 'bg-amber-300/15 text-amber-200'
          }`}
        >
          {query.isSuccess ? 'online' : query.isError ? 'offline' : 'checking'}
        </span>
      </div>

      <div className="mt-6 grid gap-4 sm:grid-cols-2">
        <div className="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
          <p className="text-xs uppercase tracking-[0.3em] text-slate-400">API URL</p>
          <p className="mt-2 break-all text-sm text-slate-200">{appEnv.apiUrl}</p>
        </div>
        <div className="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
          <p className="text-xs uppercase tracking-[0.3em] text-slate-400">Response</p>
          <p className="mt-2 text-sm text-slate-200">
            {query.isSuccess && `${query.data.status} / ${query.data.app}`}
            {query.isPending && 'Waiting for backend response...'}
            {query.isError && formatError(query.error instanceof Error ? query.error.message : 'Unknown error')}
          </p>
        </div>
      </div>
    </section>
  );
}
