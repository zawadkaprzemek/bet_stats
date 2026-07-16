import { HealthStatusCard } from '../components/health-status-card';
import { QuickNoteForm } from '../components/quick-note-form';

const dashboardCards = [
  {
    title: 'Dashboard',
    description: 'Main operational overview for metrics, feed health and betting pipelines.',
  },
  {
    title: 'Kupony',
    description: 'Reserved for bet slip analysis, outcomes, import history and automation status.',
  },
  {
    title: 'Statystyki',
    description: 'Reserved for performance models, ROI tracking and trend comparisons.',
  },
  {
    title: 'Ustawienia',
    description: 'Reserved for project configuration, integrations and user preferences.',
  },
];

export function DashboardPage() {
  return (
    <div className="space-y-6">
      <section className="rounded-[32px] border border-white/10 bg-slate-900/70 p-6 shadow-[0_25px_80px_rgba(15,23,42,0.45)] backdrop-blur sm:p-8">
        <p className="text-xs uppercase tracking-[0.45em] text-cyan-300/70">frontend starter</p>
        <div className="mt-4 max-w-3xl">
          <h2 className="text-3xl font-semibold text-white sm:text-4xl">
            Development shell for analytics, tickets and system health.
          </h2>
          <p className="mt-4 text-base leading-7 text-slate-300">
            React, TypeScript, Vite and Tailwind are configured. Query, routing, form validation and
            an API health probe are ready for the first domain features.
          </p>
        </div>
      </section>

      <div className="grid gap-4 xl:grid-cols-4">
        {dashboardCards.map((card) => (
          <article
            key={card.title}
            className="rounded-[28px] border border-white/10 bg-slate-900/80 p-5 shadow-[0_20px_50px_rgba(15,23,42,0.3)]"
          >
            <p className="text-sm font-semibold text-white">{card.title}</p>
            <p className="mt-3 text-sm leading-6 text-slate-300">{card.description}</p>
          </article>
        ))}
      </div>

      <div className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <HealthStatusCard />
        <QuickNoteForm />
      </div>
    </div>
  );
}
