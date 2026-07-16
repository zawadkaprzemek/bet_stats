import { useState } from 'react';

export function useMobileMenu() {
  const [isOpen, setIsOpen] = useState(false);

  return {
    isOpen,
    closeMenu: () => setIsOpen(false),
    toggleMenu: () => setIsOpen((current) => !current),
  };
}
