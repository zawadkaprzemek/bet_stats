type PlaceholderPageProps = {
  title: string;
  description: string;
};

export function PlaceholderPage({ title, description }: PlaceholderPageProps) {
  return (
    <section className="rounded-[32px] border border-dashed border-white/15 bg-slate-900/60 p-8 shadow-[0_20px_60px_rgba(15,23,42,0.35)]">
      <p className="text-xs uppercase tracking-[0.45em] text-slate-400">placeholder</p>
      <h2 className="mt-4 text-3xl font-semibold text-white">{title}</h2>
      <p className="mt-4 max-w-2xl text-base leading-7 text-slate-300">{description}</p>
    </section>
  );
}
