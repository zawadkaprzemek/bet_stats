import { Outlet } from 'react-router-dom';

import { BrandMark } from '../components/brand-mark';
import { NavLinkItem } from '../components/nav-link';
import { LogoutButton } from '../components/logout-button';
import { useAuth } from '../hooks/use-auth';
import { useMobileMenu } from '../hooks/use-mobile-menu';
import { navigationItems } from '../routes/navigation';

function NavigationPanel({ onNavigate }: { onNavigate?: () => void }) {
  return (
    <div className="flex h-full flex-col">
      <div className="border-b border-white/10 px-6 py-5">
        <BrandMark />
      </div>
      <nav className="flex-1 space-y-2 px-4 py-6">
        {navigationItems.map((item) => (
          <NavLinkItem key={item.to} item={item} onClick={onNavigate} />
        ))}
      </nav>
      <div className="border-t border-white/10 px-6 py-5 text-sm text-slate-400">
        Frontend shell for bet_stats
      </div>
    </div>
  );
}

export function AppLayout() {
  const { isOpen, closeMenu, toggleMenu } = useMobileMenu();
  const { user } = useAuth();

  return (
    <div className="min-h-screen bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.18),transparent_30%),linear-gradient(180deg,#0f172a_0%,#020617_100%)] text-slate-100">
      <div className="mx-auto flex min-h-screen">
        <aside className="hidden w-80 border-r border-white/10 bg-slate-950/60 backdrop-blur lg:block">
          <NavigationPanel />
        </aside>

        <div className="flex min-h-screen flex-1 flex-col">
          <header className="sticky top-0 z-30 border-b border-white/10 bg-slate-950/70 backdrop-blur">
            <div className="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
              <div className="lg:hidden">
                <BrandMark />
              </div>
              <div className="hidden lg:block">
                <p className="text-xs uppercase tracking-[0.4em] text-slate-400">analytics workspace</p>
                <h1 className="mt-1 text-2xl font-semibold text-white">bet_stats dashboard</h1>
              </div>
              <div className="flex items-center gap-3">
                <div className="hidden rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300 lg:inline-flex">
                  {user?.roles.includes('ROLE_ADMIN') ? 'Admin session' : 'User session'}
                </div>
                <LogoutButton />
                <button
                  type="button"
                  onClick={toggleMenu}
                  className="inline-flex rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10 lg:hidden"
                >
                  Menu
                </button>
              </div>
            </div>
          </header>

          {isOpen ? (
            <div className="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden" onClick={closeMenu}>
              <div
                className="absolute inset-y-0 left-0 w-80 max-w-[85vw] border-r border-white/10 bg-slate-950"
                onClick={(event) => event.stopPropagation()}
              >
                <NavigationPanel onNavigate={closeMenu} />
              </div>
            </div>
          ) : null}

          <main className="flex-1 px-5 py-6 sm:px-8 sm:py-8">
            <Outlet />
          </main>
        </div>
      </div>
    </div>
  );
}
