---
title: Asistencia por la vía de la app móvil
date: 2026-11-13
instance: aula.usm.cl (Moodle 4.5.15, mod_attendance 2024082402)
---

# Asistencia por la vía de la app móvil (RF-06)

Informe de la prueba de asistencia contra el aula virtual de la USM, realizada el 2026-11-13. Es un documento fechado: no se actualiza. Completa el punto pendiente de la [validación de la fase 1](fase-1.md), que no encontró funciones `mod_attendance_*`. El cambio en el alcance de RF-06 se refleja en el [registro de decisiones](../registro-de-decisiones.md).

Documenta **qué datos entrega Moodle y en qué formato**. No incluye los valores obtenidos, porque son datos personales de la cuenta usada.

## 1. Objetivo

Comprobar si la asistencia se puede leer por la misma vía que usa la app oficial de Moodle, ya que la API no expone funciones del plugin de asistencia.

## 2. Método

- **Herramienta:** `check_attendance.py`, un script en Python que reutiliza el token guardado y la lectura de credenciales de `validate_usm.py`. Se ejecutó desde la misma copia personal, fuera del repositorio. No imprime ni guarda tokens.
- **Instancia:** `https://aula.usm.cl`, Moodle 4.5.15, con una cuenta de alumno inscrita en ramos que usan el plugin de asistencia.
- **Pasos:**
  1. `core_webservice_get_site_info`: comprobar que estén habilitadas `tool_mobile_get_plugins_supporting_mobile` y `tool_mobile_get_content`.
  2. `tool_mobile_get_plugins_supporting_mobile`: comprobar si `mod_attendance` tiene soporte para la app y con qué método.
  3. `core_enrol_get_users_courses` y `core_course_get_contents`: encontrar las actividades de asistencia (`modname = attendance`).
  4. `tool_mobile_get_content` para cada actividad, con `component = mod_attendance`, el método del paso 2 y los argumentos `courseid`, `cmid` y `userid`.
- **Reproducción local:** el mismo flujo se probó en el Moodle de pruebas (D-31) con el plugin instalado desde su rama `MOODLE_405_STABLE` (versión 2024082405). La estructura de la respuesta fue la misma.

## 3. Resultados

| Punto | Resultado |
|---|---|
| Funciones `tool_mobile_*` | Las dos están habilitadas. |
| Soporte móvil de `mod_attendance` | Sí: handler `view`, delegado `CoreCourseModuleDelegate`, método `mobile_view_activity`. |
| `tool_mobile_get_content` | Responde en todas las actividades de asistencia probadas, con una plantilla por actividad. |
| Datos estructurados (`otherdata`) | Vacío: los datos solo vienen dentro del HTML. |

## 4. Formato de la respuesta

`tool_mobile_get_content` devuelve `templates`, `javascript`, `otherdata`, `files`, `restrict` y `disabled`. Los datos de asistencia vienen en `templates[0].html`: una plantilla de la app (Ionic y Angular), no una página HTML normal.

- **Estructura:** un `ion-grid` con una fila (`ion-row`) por dato. Cada fila tiene dos columnas (`ion-col`): la etiqueta y el valor.
- **Etiquetas:** no son texto, sino expresiones de traducción de la app, por ejemplo `{{ 'plugin.mod_attendance.sessionscompleted' | translate }}`. La clave identifica el dato sin depender del idioma.
- **Valores:** texto ya formateado en el idioma del usuario. El separador decimal cambia según el idioma: coma en la USM y punto en una instalación en inglés.
- **Alcance:** solo el resumen de la actividad. No incluye el detalle por sesión (fecha y estado).

| Clave (`plugin.mod_attendance.…`) | Dato | Formato del valor |
|---|---|---|
| `sessionscompleted` | Sesiones ya registradas | Entero |
| `pointssessionscompleted` | Puntos obtenidos en las sesiones registradas | `obtenidos / máximo` |
| `percentagesessionscompleted` | Porcentaje sobre las sesiones registradas | Decimal con `%` |
| `sessionstotal` | Sesiones totales de la actividad | Entero |
| `pointsallsessions` | Puntos sobre todas las sesiones | Se esperaba `obtenidos / máximo`, pero llega un porcentaje (ver la sección 5) |
| `percentageallsessions` | Porcentaje sobre todas las sesiones | Decimal con `%` |
| `maxpossiblepoints` | Puntos máximos posibles si se asiste a las sesiones restantes | `obtenidos / máximo` |
| `maxpossiblepercentage` | Porcentaje máximo posible | Decimal con `%` |

## 5. Limitaciones

- **Extracción desde el HTML:** hay que leer la plantilla y asociar cada clave con su valor. Si el plugin cambia la plantilla en otra versión, la lectura puede fallar, así que conviene probarla con respuestas guardadas de cada versión conocida.
- **Valores formateados:** hay que convertirlos según el idioma (separador decimal y `%`). Los puntos (`obtenidos / máximo`) son la fuente más confiable para calcular porcentajes propios.
- **`pointsallsessions`:** en la USM y en la prueba local, este dato muestra un porcentaje en lugar de puntos. Parece un error de la plantilla del plugin; AulaDesk no debería depender de él.
- **Sin detalle por sesión:** esta vía no permite listar las sesiones ni su estado.
- **Dependencia del plugin:** solo funciona en instancias que tengan `mod_attendance` con soporte móvil.

## 6. Impacto en el diseño

| Elemento | Impacto |
|---|---|
| RF-06 Asistencia | Viable en la USM como resumen por actividad: sesiones, puntos, porcentajes y máximo posible. |
| Cliente de la API | Necesita `tool_mobile_get_content` y un lector de la plantilla de asistencia, con tests a partir de respuestas guardadas. |
| Moodle de pruebas | No incluye el plugin de asistencia. Agregarlo permitiría tests de integración de RF-06, pero es una decisión aparte. |
