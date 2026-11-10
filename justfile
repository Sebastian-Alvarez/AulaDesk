# Punto de entrada único para las tareas del proyecto (D-29).
# La CI usa los mismos comandos.

set windows-shell := ["powershell.exe", "-NoLogo", "-NoProfile", "-Command"]

# Entorno de documentación: Python y Zensical fijados en tools/docs/uv.lock
docs := "uv run --project tools/docs --locked --"

# Muestra las tareas disponibles
default:
    @just --list

# Vista previa de la documentación en http://localhost:8000, con recarga automática
docs-serve:
    {{ docs }} zensical serve

# Construye el sitio en site/ y falla ante cualquier advertencia
docs-build:
    {{ docs }} zensical build --clean --strict
