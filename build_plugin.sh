#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="${SCRIPT_DIR}/local_coursebanner"
AMD_SRC_DIR="${PLUGIN_DIR}/amd/src"
AMD_BUILD_DIR="${PLUGIN_DIR}/amd/build"
VERSION_FILE="${PLUGIN_DIR}/version.php"

if [[ ! -f "${VERSION_FILE}" ]]; then
  echo "Error: ${VERSION_FILE} not found." >&2
  exit 1
fi

# Read a $plugin-><field> value from version.php (quoted release, unquoted numeric version).
read_version_field() {
  local field="$1"
  sed -nE "s/^\\\$plugin->${field}[[:space:]]*=[[:space:]]*'?([^';]+)'?;.*/\1/p" "${VERSION_FILE}" | head -n 1
}

RELEASE="$(read_version_field release)"
VERSION="$(read_version_field version)"

if [[ -z "${RELEASE}" || -z "${VERSION}" ]]; then
  echo "Error: could not read \$plugin->release or \$plugin->version from ${VERSION_FILE}." >&2
  exit 1
fi

if [[ ! "${RELEASE}" =~ ^[A-Za-z0-9._-]+$ || ! "${VERSION}" =~ ^[A-Za-z0-9._-]+$ ]]; then
  echo "Error: unsupported characters in release '${RELEASE}' or version '${VERSION}'." >&2
  exit 1
fi

ZIP_NAME="local_coursebanner-${RELEASE}-${VERSION}.zip"
ZIP_PATH="${SCRIPT_DIR}/${ZIP_NAME}"

if ! command -v npx >/dev/null 2>&1; then
  echo "Error: npx is required to build AMD assets." >&2
  echo "Install Node.js (which includes npx), then retry." >&2
  exit 1
fi

mkdir -p "${AMD_BUILD_DIR}"

# Pinned to match the toolchain used for the committed amd/build files; bump only when
# the minified output is regenerated and reviewed.
TERSER_VERSION="5.51.2"

echo "Minifying AMD JavaScript with terser ${TERSER_VERSION}..."
for srcfile in "${AMD_SRC_DIR}"/*.js; do
  filename="$(basename "${srcfile}")"
  outfile="${AMD_BUILD_DIR}/${filename%.js}.min.js"
  npx --yes "terser@${TERSER_VERSION}" "${srcfile}" --compress --mangle --output "${outfile}"
done

find "${PLUGIN_DIR}" -name '.DS_Store' -delete
rm -f "${ZIP_PATH}"

echo "Creating plugin archive (version ${VERSION}, release ${RELEASE})..."
(
  cd "${SCRIPT_DIR}"
  zip -r "${ZIP_NAME}" "$(basename "${PLUGIN_DIR}")" -x '*/.DS_Store'
)

echo "Build complete: ${ZIP_PATH}"
