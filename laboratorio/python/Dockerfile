ARG BROTLI_VERSION=1.0.9
FROM python:3.11-slim

ARG BROTLI_VERSION
WORKDIR /app

RUN pip install --no-cache-dir "brotli==${BROTLI_VERSION}"

COPY app.py ./

USER nobody
EXPOSE 8000
CMD ["python", "app.py"]
