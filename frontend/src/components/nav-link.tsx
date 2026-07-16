import { NavLink } from 'react-router';

import type { NavigationItem } from '../types/navigation';

type NavLinkItemProps = {
  item: NavigationItem;
  onClick?: () => void;
};

export function NavLinkItem({ item, onClick }: NavLinkItemProps) {
  return (
    <NavLink
      to={item.to}
      onClick={onClick}
      className={({ isActive }) =>
        [
          'group flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium transition',
          isActive
            ? 'bg-white text-slate-950 shadow-[0_12px_30px_rgba(255,255,255,0.18)]'
            : 'text-slate-300 hover:bg-white/8 hover:text-white',
        ].join(' ')
      }
    >
      <span>{item.label}</span>
      <span className="text-xs uppercase tracking-[0.3em] opacity-60">{item.shortLabel}</span>
    </NavLink>
  );
}
