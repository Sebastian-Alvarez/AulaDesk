# Registros de decisiones de arquitectura (ADR)

Cada decisión importante tiene su propio ADR en esta carpeta, en formato [MADR](https://adr.github.io/madr/) (variante mínima). El [registro de decisiones](../registro-de-decisiones.md) funciona como índice de todas las decisiones, tengan o no un ADR.

| ADR | Título | Decisiones | Estado | Fecha |
|---|---|---|---|---|
| [0001](0001-entorno-de-desarrollo.md) | Entorno de desarrollo | D-27 a D-32 | Aceptado | 2026-11-08 |
| [0002](0002-diagramas-c4-como-codigo.md) | Diagramas C4 como código | D-26 | Aceptado | 2026-11-09 |
| [0003](0003-arquitectura.md) | Arquitectura | D-05 a D-07 | Aceptado | 2026-11-06 |
| [0004](0004-tecnologia.md) | Tecnología | D-08 y D-09 | Aceptado | 2026-11-06 |
| [0005](0005-login-y-sesion.md) | Login y sesión | D-13 a D-15 | Aceptado | 2026-11-06 |
| [0006](0006-vista-de-cursos-y-sitio-real.md) | Vista de cursos y sitio real | D-16 y D-17 | Aceptado | 2026-11-06 |

Los ADR 0003 a 0006 son retroactivos: se escribieron el 2026-11-10 y llevan la fecha en que se tomaron las decisiones.

## Mantenimiento

- **Registro de decisiones:** se reescribe para mostrar siempre el estado actual. El historial de sus cambios queda en Git.
- **ADR aceptado:** no se edita. Si una decisión cambia, se escribe un ADR nuevo que la reemplaza, y el anterior pasa al estado "Reemplazado por ADR-NNNN".
- **Informes fechados** (por ejemplo, las validaciones): no se actualizan; las conclusiones se llevan al registro.

## Formato

Los ADR se numeran de forma correlativa (`NNNN-titulo-en-minusculas.md`) y tienen estas secciones:

1. Contexto y problema
2. Factores de decisión
3. Opciones consideradas
4. Resultado de la decisión
5. Consecuencias

Estados posibles: Propuesto, Aceptado, Rechazado, Obsoleto y Reemplazado.
