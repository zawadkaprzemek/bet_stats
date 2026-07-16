/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_API_URL: string | undefined;
  readonly VITE_BACKEND_PROXY_TARGET: string | undefined;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
