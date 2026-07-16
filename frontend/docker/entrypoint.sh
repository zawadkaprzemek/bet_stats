#!/bin/sh
set -eu

if [ -f package-lock.json ]; then
  if [ ! -d node_modules ]; then
    npm install
  elif [ ! -x node_modules/.bin/vite ] || [ ! -x node_modules/.bin/eslint ] || [ ! -x node_modules/.bin/tsc ] || [ ! -d node_modules/tailwindcss ] || [ ! -d node_modules/postcss ] || [ ! -d node_modules/autoprefixer ]; then
    find node_modules -mindepth 1 -maxdepth 1 -exec rm -rf {} +
    npm install
  fi
elif [ -f package.json ] && [ ! -d node_modules ]; then
  npm install
fi

exec "$@"
