#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="${ROOT_DIR:-$(cd "${SCRIPT_DIR}/.." && pwd)}"
WORDPRESS_SOURCE="${WORDPRESS_SOURCE:-${ROOT_DIR}/source/wordpress}"
RUNTIME_DIR="${RUNTIME_DIR:-/tmp/wordpress-wsl}"

PHP_BIN="${PHP_BIN:-php}"
MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQLADMIN_BIN="${MYSQLADMIN_BIN:-mysqladmin}"
MARIADB_INSTALL_DB_BIN="${MARIADB_INSTALL_DB_BIN:-mariadb-install-db}"
MARIADBD_BIN="${MARIADBD_BIN:-mariadbd}"

PANEL_HOST="${PANEL_HOST:-127.0.0.1}"
PANEL_PORT="${PANEL_PORT:-8080}"
PANEL_URL="http://${PANEL_HOST}:${PANEL_PORT}"
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-}"
DB_ROOT_USER="${DB_ROOT_USER:-root}"
MARIADB_DATA_DIR="${ROOT_DIR}/data/mariadb"
MARIADB_SOCKET="${RUNTIME_DIR}/mariadb.sock"
MARIADB_PID_FILE="${RUNTIME_DIR}/mariadb.pid"
MARIADB_LOG_FILE="${RUNTIME_DIR}/mariadb.log"
MARIADB_INIT_MARKER="${ROOT_DIR}/data/mariadb/.initialized"
PHP_PID_FILE="${RUNTIME_DIR}/php.pid"
PHP_LOG_FILE="${RUNTIME_DIR}/php.log"
ROUTER_FILE="${ROOT_DIR}/panel/router.php"
SITES_DIR="${ROOT_DIR}/sites"
PANEL_STATE_FILE="${ROOT_DIR}/panel/state.json"

ensure_dirs() {
  mkdir -p "${ROOT_DIR}/bin" "${ROOT_DIR}/config" "${ROOT_DIR}/data" \
    "${MARIADB_DATA_DIR}" "${ROOT_DIR}/logs" "${ROOT_DIR}/panel" "${SITES_DIR}" "${ROOT_DIR}/tmp" "${RUNTIME_DIR}"
}

pid_matches() {
  local pid_file="$1"
  local expected_pattern="$2"

  [[ -f "${pid_file}" ]] || return 1

  local pid
  pid="$(cat "${pid_file}" 2>/dev/null || true)"
  [[ "${pid}" =~ ^[0-9]+$ ]] || return 1
  kill -0 "${pid}" 2>/dev/null || return 1

  local cmdline
  cmdline="$(tr '\0' ' ' < "/proc/${pid}/cmdline" 2>/dev/null || true)"
  [[ -n "${cmdline}" ]] || return 1
  [[ "${cmdline}" == *"${expected_pattern}"* ]]
}

random_secret() {
  local length="${1:-24}"
  local bytes=$(( (length + 1) / 2 ))
  openssl rand -hex "${bytes}" | cut -c1-"${length}"
}

mysql_root_args() {
  printf -- "--socket=%s -u%s" "${MARIADB_SOCKET}" "${DB_ROOT_USER}"
}

require_wordpress_source() {
  if [[ ! -f "${WORDPRESS_SOURCE}/wp-load.php" ]]; then
    echo "WordPress source not found at ${WORDPRESS_SOURCE}" >&2
    exit 1
  fi
}

extract_wordpress_source() {
  local zip_path="${ROOT_DIR}/source/wordpress.zip"
  if [[ -d "${WORDPRESS_SOURCE}" ]]; then
    return 0
  fi
  if [[ ! -f "${zip_path}" ]]; then
    echo "Missing WordPress source and archive: ${zip_path}" >&2
    exit 1
  fi
  mkdir -p "${ROOT_DIR}/source"
  unzip -q -o "${zip_path}" -d "${ROOT_DIR}/source"
}

site_url() {
  printf '%s/%s' "${PANEL_URL}" "$1"
}

site_dir() {
  printf '%s/%s' "${SITES_DIR}" "$1"
}

site_db_name() {
  printf '%s' "$1"
}

site_db_user() {
  printf '%s_user' "$1"
}

site_db_password_file() {
  printf '%s/%s.dbpass' "${ROOT_DIR}/data" "$1"
}
