FROM python:3.12-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1 \
    PIP_DISABLE_PIP_VERSION_CHECK=1

WORKDIR /app

# Dependency sistem (untuk mysql-connector-python)
RUN apt-get update && apt-get install -y --no-install-recommends \
        gcc \
        default-libmysqlclient-dev \
        pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Install Python packages dulu (layer cache efisien)
COPY requirements.txt /app/requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

# Copy source (akan ditimpa bind-mount saat runtime)
COPY . /app

EXPOSE 5000

# Gunakan flask dev server (hot reload karena FLASK_DEBUG=1)
CMD ["python", "app.py"]
