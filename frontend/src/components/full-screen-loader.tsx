type FullScreenLoaderProps = {
  label?: string;
};

export function FullScreenLoader({ label = 'Checking session...' }: FullScreenLoaderProps) {
  return (
    <div className="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.18),transparent_30%),linear-gradient(180deg,#0f172a_0%,#020617_100%)] px-6 text-slate-100">
      <div className="w-full max-w-md rounded-[32px] border border-white/10 bg-slate-900/80 p-8 text-center shadow-[0_30px_80px_rgba(15,23,42,0.45)] backdrop-blur">
        <div className="mx-auto mb-5 size-12 animate-spin rounded-full border-2 border-cyan-300/30 border-t-cyan-300" />
        <p className="text-xs uppercase tracking-[0.45em] text-cyan-300/70">bet stats</p>
        <p className="mt-4 text-lg font-semibold text-white">{label}</p>
      </div>
    </div>
  );
}
