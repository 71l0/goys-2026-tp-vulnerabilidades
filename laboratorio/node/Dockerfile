ARG NODE_TAG=22.23.3-bookworm-slim
FROM node:${NODE_TAG}

WORKDIR /app

COPY package.json ./
RUN npm install --omit=dev

COPY server.js ./

USER node
EXPOSE 3000
CMD ["node", "server.js"]
