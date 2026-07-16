import { useAuth } from '../hooks/use-auth';

type LogoutButtonProps = {
  className?: string;
};

export function LogoutButton({ className }: LogoutButtonProps) {
  const { logout, user } = useAuth();

  return (
    <button
      type="button"
      onClick={() => {
        void logout.submit();
      }}
      disabled={logout.isPending}
      className={
        className ??
        'inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60'
      }
    >
      <span className="hidden text-slate-300 sm:inline">{user?.email}</span>
      <span>{logout.isPending ? 'Signing out...' : 'Sign out'}</span>
    </button>
  );
}
