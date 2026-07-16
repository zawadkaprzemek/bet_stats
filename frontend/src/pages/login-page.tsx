import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Navigate } from 'react-router-dom';

import { BrandMark } from '../components/brand-mark';
import { FullScreenLoader } from '../components/full-screen-loader';
import { useAuth } from '../hooks/use-auth';
import { loginSchema, type LoginInput } from '../schemas/login';

export function LoginPage() {
  const { isAuthenticated, isLoading, login } = useAuth();
  const form = useForm<LoginInput>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      email: '',
      password: '',
    },
  });

  if (isLoading) {
    return <FullScreenLoader label="Preparing sign-in..." />;
  }

  if (isAuthenticated) {
    return <Navigate to="/" replace />;
  }

  const onSubmit = async (values: LoginInput) => {
    try {
      await login.submit(values);
    } catch {
      form.setValue('password', '');
    }
  };

  return (
    <div className="min-h-screen bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.16),transparent_25%),radial-gradient(circle_at_bottom_right,rgba(52,211,153,0.14),transparent_24%),linear-gradient(180deg,#0f172a_0%,#020617_100%)] px-5 py-10 text-slate-100 sm:px-8">
      <div className="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center justify-center">
        <div className="grid w-full overflow-hidden rounded-[36px] border border-white/10 bg-slate-950/70 shadow-[0_35px_100px_rgba(15,23,42,0.55)] backdrop-blur lg:grid-cols-[1.1fr_0.9fr]">
          <section className="flex flex-col justify-between border-b border-white/10 p-8 sm:p-10 lg:border-b-0 lg:border-r">
            <div>
              <BrandMark />
              <p className="mt-10 text-xs uppercase tracking-[0.45em] text-cyan-300/70">session auth</p>
              <h1 className="mt-5 max-w-xl text-4xl font-semibold leading-tight text-white sm:text-5xl">
                Sign in to access dashboards, ticket views and admin tooling.
              </h1>
              <p className="mt-6 max-w-lg text-base leading-7 text-slate-300">
                This frontend uses the existing Symfony session and secure cookies. No JWT and no local
                storage persistence.
              </p>
            </div>

            <div className="mt-10 grid gap-4 sm:grid-cols-3">
              {[
                ['API session', 'Cookie-based auth shared with Symfony backend.'],
                ['Route guards', 'Private views stay hidden until session check completes.'],
                ['No token storage', 'The browser keeps only the HTTP session cookie.'],
              ].map(([title, description]) => (
                <article key={title} className="rounded-[24px] border border-white/10 bg-white/5 p-4">
                  <p className="text-sm font-semibold text-white">{title}</p>
                  <p className="mt-2 text-sm leading-6 text-slate-300">{description}</p>
                </article>
              ))}
            </div>
          </section>

          <section className="p-8 sm:p-10">
            <div className="mx-auto max-w-md">
              <p className="text-sm font-medium text-emerald-300">Welcome back</p>
              <h2 className="mt-3 text-3xl font-semibold text-white">Log in to bet_stats</h2>
              <p className="mt-3 text-sm leading-6 text-slate-300">
                Use an existing account created in Symfony. Admin users can also access EasyAdmin.
              </p>

              <form onSubmit={form.handleSubmit(onSubmit)} className="mt-8 space-y-5">
                <label className="block space-y-2">
                  <span className="text-sm text-slate-200">Email</span>
                  <input
                    type="email"
                    autoComplete="email"
                    {...form.register('email')}
                    className="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
                    placeholder="admin@example.com"
                  />
                  {form.formState.errors.email ? (
                    <span className="text-sm text-rose-300">{form.formState.errors.email.message}</span>
                  ) : null}
                </label>

                <label className="block space-y-2">
                  <span className="text-sm text-slate-200">Password</span>
                  <input
                    type="password"
                    autoComplete="current-password"
                    {...form.register('password')}
                    className="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300"
                    placeholder="Your password"
                  />
                  {form.formState.errors.password ? (
                    <span className="text-sm text-rose-300">{form.formState.errors.password.message}</span>
                  ) : null}
                </label>

                {login.errorMessage ? (
                  <div className="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {login.errorMessage}
                  </div>
                ) : null}

                <button
                  type="submit"
                  disabled={login.isPending}
                  className="inline-flex w-full items-center justify-center rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-70"
                >
                  {login.isPending ? 'Signing in...' : 'Sign in'}
                </button>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
  );
}
