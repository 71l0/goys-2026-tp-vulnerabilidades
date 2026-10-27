"""Servicio mínimo para inspeccionar la versión de Brotli.

No explota nada: solo informa la versión y decodifica cuerpos benignos.
"""

from http.server import BaseHTTPRequestHandler, HTTPServer
import json

import brotli


class Handler(BaseHTTPRequestHandler):
    server_version = "goys-py-lab/1.0"

    def _send(self, code: int, body: bytes, ctype: str = "application/json") -> None:
        self.send_response(code)
        self.send_header("Content-Type", ctype)
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def do_GET(self) -> None:  # noqa: N802
        payload = {
            "service": "py-lab",
            "python": self.server.python_version,
            "brotli": getattr(brotli, "__version__", "desconocida"),
        }
        self._send(200, json.dumps(payload).encode("utf-8"))

    def do_POST(self) -> None:  # noqa: N802
        length = int(self.headers.get("Content-Length", "0"))
        body = self.rfile.read(length)

        if self.headers.get("Content-Encoding") == "br":
            try:
                body = brotli.decompress(body)
            except Exception:
                return self._send(400, b'{"error":"brotli invalido"}')

        self._send(200, body, "text/plain")

    def log_message(self, fmt: str, *args) -> None:
        print("%s - %s" % (self.address_string(), fmt % args), flush=True)


if __name__ == "__main__":
    import sys

    server = HTTPServer(("0.0.0.0", 8000), Handler)
    server.python_version = sys.version.split()[0]
    print("py-lab escuchando en 8000", flush=True)
    server.serve_forever()
