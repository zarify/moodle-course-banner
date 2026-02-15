#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="${SCRIPT_DIR}/local_coursebanner"
ZIP_PATH="${SCRIPT_DIR}/local_coursebanner.zip"

find "${PLUGIN_DIR}" -name '.DS_Store' -delete
rm -f "${ZIP_PATH}"
(
  cd "${SCRIPT_DIR}"
  zip -r "$(basename "${ZIP_PATH}")" "$(basename "${PLUGIN_DIR}")" -x '*/.DS_Store'
)
