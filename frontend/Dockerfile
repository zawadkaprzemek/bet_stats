FROM node:22-bookworm

WORKDIR /app

COPY package.json package-lock.json* ./
COPY docker/entrypoint.sh /usr/local/bin/frontend-entrypoint.sh

RUN chmod +x /usr/local/bin/frontend-entrypoint.sh

EXPOSE 5173

ENTRYPOINT ["frontend-entrypoint.sh"]

CMD ["tail", "-f", "/dev/null"]
