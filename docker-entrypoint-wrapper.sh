#!/bin/bash
set -e

# Fix MPM conflict at runtime before Apache starts
find /etc/apache2/mods-enabled/ -name "mpm_*" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

# ── Seed uploads volume with WooCommerce placeholder ──────────────────────────
# The uploads directory is mounted as a persistent Railway volume.
# On first start the volume is empty, so the WooCommerce placeholder doesn't
# exist and product cards show broken images. Copy it from the plugin bundle
# on every start (no-op if the file already exists).
UPLOADS_DIR="/var/www/html/wp-content/uploads"
PLACEHOLDER_DST="${UPLOADS_DIR}/woocommerce-placeholder.webp"
WC_PLUGIN="/var/www/html/wp-content/plugins/woocommerce"

mkdir -p "${UPLOADS_DIR}"
chown -R www-data:www-data "${UPLOADS_DIR}" 2>/dev/null || true

if [ ! -f "${PLACEHOLDER_DST}" ]; then
  # Try webp first, then png fallback
  if [ -f "${WC_PLUGIN}/assets/images/placeholder.webp" ]; then
    cp "${WC_PLUGIN}/assets/images/placeholder.webp" "${PLACEHOLDER_DST}"
  elif [ -f "${WC_PLUGIN}/assets/images/placeholder.png" ]; then
    cp "${WC_PLUGIN}/assets/images/placeholder.png" "${UPLOADS_DIR}/woocommerce-placeholder.png"
    # Also copy as .webp so WC's hardcoded reference resolves
    cp "${WC_PLUGIN}/assets/images/placeholder.png" "${PLACEHOLDER_DST}"
  fi
fi

# Hand off to the official WordPress entrypoint
exec docker-entrypoint.sh "$@"
