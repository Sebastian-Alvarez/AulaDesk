# Registros de decisiones de arquitectura (ADR)

Cada decisión importante tiene su propio ADR en esta carpeta, en formato [MADR](https://adr.github.io/madr/) (variante mínima). El [registro de decisiones](../registro-de-decisiones.md) funciona como índice de todas las decisiones, tengan o no un ADR.

| ADR | Título | Decisiones | Estado | Fecha |
|---|---|---|---|---|
| [0001](0001-entorno-de-desarrollo.md) | Entorno de desarrollo | D-27 a D-32 | Aceptado | 2026-11-08 |

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
