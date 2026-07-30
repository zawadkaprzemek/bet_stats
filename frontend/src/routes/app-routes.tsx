import { lazy } from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';

import { AppLayout } from '../layouts/app-layout';
import { GuestRoute, PrivateRoute } from './guards';

const DashboardPage = lazy(async () => {
  const module = await import('../pages/dashboard-page');

  return { default: module.DashboardPage };
});

const PlaceholderPage = lazy(async () => {
  const module = await import('../pages/placeholder-page');

  return { default: module.PlaceholderPage };
});

const LoginPage = lazy(async () => {
  const module = await import('../pages/login-page');

  return { default: module.LoginPage };
});

const SettingsPage = lazy(async () => {
  const module = await import('../pages/settings-page');

  return { default: module.SettingsPage };
});

export function AppRoutes() {
  return (
    <Routes>
      <Route element={<GuestRoute />}>
        <Route path="/login" element={<LoginPage />} />
      </Route>

      <Route element={<PrivateRoute />}>
        <Route element={<AppLayout />}>
          <Route index element={<DashboardPage />} />
          <Route
            path="/kupony"
            element={
              <PlaceholderPage
                title="Kupony"
                description="Space reserved for ticket intake, settlement summaries and quick filters."
              />
            }
          />
          <Route
            path="/statystyki"
            element={
              <PlaceholderPage
                title="Statystyki"
                description="Space reserved for analytical charts, model quality and longitudinal comparisons."
              />
            }
          />
          <Route
            path="/ustawienia"
            element={<SettingsPage />}
          />
        </Route>
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
