#!/usr/bin/env sh
set -e

set_env() {
  key="$1"
  value="$2"

  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --ansi
fi

set_env DB_CONNECTION "${DB_CONNECTION:-mysql}"
set_env DB_HOST "${DB_HOST:-mysql}"
set_env DB_PORT "${DB_PORT:-3306}"
set_env DB_DATABASE "${DB_DATABASE:-compasia}"
set_env DB_USERNAME "${DB_USERNAME:-compasia}"
set_env DB_PASSWORD "${DB_PASSWORD:-compasia}"
set_env REDIS_CLIENT "${REDIS_CLIENT:-phpredis}"
set_env REDIS_HOST "${REDIS_HOST:-redis}"
set_env REDIS_PORT "${REDIS_PORT:-6379}"
set_env CACHE_STORE "${CACHE_STORE:-redis}"
set_env QUEUE_CONNECTION "${QUEUE_CONNECTION:-redis}"
set_env BROADCAST_CONNECTION "${BROADCAST_CONNECTION:-reverb}"
set_env REVERB_APP_ID "${REVERB_APP_ID:-281634}"
set_env REVERB_APP_KEY "${REVERB_APP_KEY:-lcr9zyhymu88ryfhrrmz}"
set_env REVERB_APP_SECRET "${REVERB_APP_SECRET:-kge50tnhkr6lfvgni3wc}"
set_env REVERB_HOST "${REVERB_HOST:-reverb}"
set_env REVERB_PORT "${REVERB_PORT:-8080}"
set_env REVERB_SCHEME "${REVERB_SCHEME:-http}"

php artisan optimize:clear || true

until [ -f vendor/autoload.php ]; do
  echo "Waiting for Laravel dependencies (vendor/autoload.php)..."
  sleep 2
done

until php -r '$h=getenv("REDIS_HOST") ?: "redis"; $p=(int) (getenv("REDIS_PORT") ?: 6379); $s=@fsockopen($h, $p, $errno, $errstr, 2); if ($s) { fclose($s); exit(0);} exit(1);'; do
  echo "Waiting for Redis..."
  sleep 2
done

php artisan horizon
