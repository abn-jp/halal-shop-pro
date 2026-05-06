#!/bin/bash
set -e

# Fix MPM conflict at runtime before Apache starts
find /etc/apache2/mods-enabled/ -name "mpm_*" -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

# Hand off to the official WordPress entrypoint
exec docker-entrypoint.sh "$@"
