#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="${SCRIPT_DIR}/local_coursebanner"
AMD_SRC_DIR="${PLUGIN_DIR}/amd/src"
AMD_BUILD_DIR="${PLUGIN_DIR}/amd/build"
ZIP_PATH="${SCRIPT_DIR}/local_coursebanner.zip"

if ! command -v npx >/dev/null 2>&1; then
  echo "Error: npx is required to build AMD assets." >&2
  echo "Install Node.js (which includes npx), then retry." >&2
  exit 1
fi

mkdir -p "${AMD_BUILD_DIR}"

echo "Minifying AMD JavaScript..."
for srcfile in "${AMD_SRC_DIR}"/*.js; do
  filename="$(basename "${srcfile}")"
  outfile="${AMD_BUILD_DIR}/${filename%.js}.min.js"
  npx --yes terser "${srcfile}" --compress --mangle --output "${outfile}"
done

find "${PLUGIN_DIR}" -name '.DS_Store' -delete
rm -f "${ZIP_PATH}"

echo "Creating plugin archive..."
(
  cd "${SCRIPT_DIR}"
  zip -r "$(basename "${ZIP_PATH}")" "$(basename "${PLUGIN_DIR}")" -x '*/.DS_Store'
)

echo "Build complete: ${ZIP_PATH}"