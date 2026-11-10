---
status: aceptado
date: 2026-11-09
decisions: D-26
---

# Diagramas C4 como código

## Contexto y problema

Los diagramas C4 de AulaDesk (niveles 1 y 2) estaban en Lucidchart, en el documento "AulaDesk - C4 Diagrams", mientras que los diagramas de secuencia ya se escribían en Mermaid (D-26). Al revisar el documento de Lucid, los dos niveles no coincidían entre sí: cada diagrama se dibujaba por separado y nada garantizaba que mostraran las mismas relaciones.

Además, la fuente quedaba fuera del repositorio: los cambios no se revisaban en un PR, no tenían historial en Git y el sitio de documentación no podía generarlos.

## Factores de decisión

- Principio "todo como código": la fuente de los diagramas vive en el repositorio y se revisa en los PR.
- Consistencia entre niveles: un solo modelo del que salgan todas las vistas.
- Fidelidad al modelo C4 y valor como portafolio.
- Publicación en el sitio de documentación y validación en la CI.
- Sin costo, con licencias abiertas y con la versión fijada.
- No adelantar herramientas que el proyecto todavía no define, como Node en la fase 1 (P-13).

## Opciones consideradas

| Opción | Modelo | Requisitos | Observaciones |
|---|---|---|---|
| Lucidchart | Un diagrama por nivel | Cuenta de Lucid | Edición visual, pero la fuente queda fuera del repositorio y los niveles pueden divergir |
| Structurizr DSL | Un modelo, varias vistas | Docker | Implementación de referencia, creada por el autor de C4. Exporta un sitio estático. Licencia Apache 2.0 |
| LikeC4 | Un modelo, varias vistas | Node | Vista previa en vivo y plugin para JetBrains. Vocabulario y niveles libres, lo que permite apartarse de C4. Licencia MIT |
| D2 | Un modelo reutilizable entre vistas (`suspend`) | Binario de D2 | Soporte de C4 desde la versión 0.7. Riesgo de mantenimiento: el proyecto continúa con dedicación limitada. Licencia MPL-2.0 |
| C4-PlantUML | Un diagrama por nivel | Java | Macros de PlantUML; la consistencia entre niveles depende de quien edita. Licencia MIT |
| Mermaid C4 | Un diagrama por nivel | Ninguno | GitHub y Zensical lo muestran sin herramientas extra, pero el soporte de C4 es experimental y no hay control del layout |

## Resultado de la decisión

Diagramas C4 en **Structurizr DSL**, en `arquitectura/workspace.dsl`, publicados como sitio estático en [`/arquitectura/`](https://sebastian-alvarez.github.io/AulaDesk/arquitectura/). Los diagramas de secuencia siguen en Mermaid, en `docs/diagramas/`.

- El modelo está en `arquitectura/`, fuera de `docs/`, para que Zensical no publique los archivos fuente.
- Structurizr corre en Docker, con la imagen fijada en `dev/structurizr/compose.yaml` y actualizada por Dependabot.
- `just diagrams-serve` levanta la vista previa, `just diagrams-check` valida el modelo y `just docs-diagrams` genera el sitio estático. La CI ejecuta `just docs-build docs-diagrams`, así que un modelo inválido deja en rojo el check **docs**.
- Todas las vistas usan layout automático (`autoLayout`), así que el DSL es la única fuente: `workspace.json` no se versiona.

Se eligió Structurizr sobre LikeC4, que era la alternativa más cercana, porque obliga a seguir el modelo C4 y porque usa Docker, que el proyecto ya requiere para el Moodle de pruebas (D-31), en lugar de sumar Node antes de la fase 2.

## Consecuencias

**Positivas**

- Los niveles 1 y 2 salen del mismo modelo, así que no pueden contradecirse.
- Los cambios de arquitectura se revisan en el mismo PR que el código o las decisiones que los motivan.
- La CI valida el modelo y publica los diagramas junto con el resto de la documentación.

**Negativas**

- Ver o ajustar los diagramas en local requiere Docker Desktop corriendo.
- El layout automático controla poco la posición de los elementos. Si una vista lo necesita, se puede pasar a layout manual con `workspace.json`, combinándolo con el DSL mediante `merge` antes del export.
- El sitio de los diagramas es independiente de Zensical: `just docs-serve` no lo incluye y, al reconstruir `site/`, borra el export. Para verlo en local hay que volver a ejecutar `just docs-diagrams`.
- La edición no tiene vista previa en vivo dentro de RustRover: se edita el `.dsl` y se recarga el navegador.
