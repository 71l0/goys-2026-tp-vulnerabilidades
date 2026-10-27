'use strict';

const express = require('express');
const zlib = require('node:zlib');

const app = express();
app.disable('x-powered-by');

app.get('/', (_req, res) => {
  res.json({
    service: 'node-lab',
    runtime: process.version,
    brotli: process.versions.brotli || 'no expuesto',
    express: require('express/package.json').version,
  });
});

// Decodifica un cuerpo Brotli benigno. No ejecuta ni evalúa el contenido.
app.post('/decode', express.raw({ type: '*/*', limit: '1mb' }), (req, res) => {
  if (req.headers['content-encoding'] !== 'br') {
    return res.type('text/plain').send(req.body);
  }

  try {
    const out = zlib.brotliDecompressSync(req.body);
    return res.type('text/plain').send(out);
  } catch {
    return res.status(400).json({ error: 'brotli invalido' });
  }
});

app.listen(3000, '0.0.0.0', () => {
  console.log('node-lab escuchando en 3000');
});
