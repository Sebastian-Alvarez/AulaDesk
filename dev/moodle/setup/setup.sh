#!/usr/bin/env bash
# Prepara el Moodle de pruebas (D-31): descarga el código, lo instala y carga los datos de ejemplo.
# Lo ejecuta el servicio setup de compose.yaml en cada `just moodle-up`; los pasos ya hechos no se repiten.
set -euo pipefail

: "${MOODLE_DIR:?}" "${MOODLE_TAG:?}" "${MOODLE_COMMIT:?}" "${MOODLE_ADMIN_PASSWORD:?}"
moodledata=/var/www/moodledata
installed="$moodledata/.auladesk-installed"

# PHP corre como www-data, el mismo usuario de Apache, para que los archivos de moodledata le pertenezcan.
as_www() { runuser -u www-data -- "$@"; }

# 1. Código de Moodle, con un tag fijo y verificado por su commit. No se incluye en el repositorio (D-20).
if [ ! -f "$MOODLE_DIR/version.php" ]; then
    echo "Descargando Moodle $MOODLE_TAG..."
    git -c advice.detachedHead=false clone --quiet --depth 1 --branch "$MOODLE_TAG" https://github.com/moodle/moodle.git "$MOODLE_DIR"
    commit=$(git -C "$MOODLE_DIR" rev-parse HEAD)
    if [ "$commit" != "$MOODLE_COMMIT" ]; then
        echo "El tag $MOODLE_TAG apunta a $commit y se esperaba $MOODLE_COMMIT." >&2
        find "$MOODLE_DIR" -mindepth 1 -delete
        exit 1
    fi
    rm -rf "$MOODLE_DIR/.git"
fi

cp /setup/config.php "$MOODLE_DIR/config.php"
chown www-data:www-data "$moodledata"

# 2. Instalación (solo la primera vez, o después de `just moodle-reset`).
if [ ! -f "$installed" ]; then
    echo "Instalando Moodle..."
    as_www php "$MOODLE_DIR/admin/cli/install_database.php" \
        --agree-license --lang=en \
        --fullname="AulaDesk (Moodle de pruebas)" --shortname=AulaDesk \
        --adminuser=admin --adminpass="$MOODLE_ADMIN_PASSWORD" \
        --adminemail=admin@example.com --supportemail=admin@example.com
    as_www touch "$installed"
fi

# 3. Servicios web, servicio móvil y datos de ejemplo.
as_www php /setup/seed.php

echo "Moodle de pruebas listo en $MOODLE_WWWROOT"
