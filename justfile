# Punto de entrada único para las tareas del proyecto (D-29).
# La CI usa los mismos comandos.

set windows-shell := ["powershell.exe", "-NoLogo", "-NoProfile", "-Command"]

# Entorno de documentación: Python y Zensical fijados en tools/docs/uv.lock
docs := "uv run --project tools/docs --locked --"

# Structurizr en Docker, con la imagen fijada en dev/structurizr/compose.yaml (D-26)
structurizr := "docker compose -f dev/structurizr/compose.yaml"

# En Linux, el contenedor corre con el uid y gid de quien lo ejecuta (ver compose.yaml)
export STRUCTURIZR_USER := if os_family() == "unix" { `id -u` + ":" + `id -g` } else { "" }

# Muestra las tareas disponibles
default:
    @just --list

# Vista previa de la documentación en http://localhost:8000, con recarga automática
docs-serve:
    {{ docs }} zensical serve

# Construye el sitio en site/ y falla ante cualquier advertencia
docs-build:
    {{ docs }} zensical build --clean --strict

# Vista previa de los diagramas C4 en http://localhost:8080 (Ctrl+C para salir)
# El "-" evita que just marque como error la salida con Ctrl+C.
diagrams-serve:
    -{{ structurizr }} up

# Valida el modelo C4
diagrams-check:
    {{ structurizr }} run --rm structurizr validate -w workspace.dsl

# Genera los diagramas C4 en site/arquitectura/ (ejecutar después de docs-build)
docs-diagrams:
    {{ structurizr }} run --rm structurizr export -w workspace.dsl -f static -o /repo/site/arquitectura
