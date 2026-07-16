import { Suspense } from 'react';

import { AppRoutes } from './routes/app-routes';

function App() {
  return (
    <Suspense
      fallback={
        <div className="flex min-h-screen items-center justify-center bg-slate-950 text-sm text-slate-300">
          Loading application...
        </div>
      }
    >
      <AppRoutes />
    </Suspense>
  );
}

export default App;
