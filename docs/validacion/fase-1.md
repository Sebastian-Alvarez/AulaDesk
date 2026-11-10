---
title: Validación de la fase 1
date: 2026-11-07
instance: aula.usm.cl (Moodle 4.5.15)
---

# Validación de la fase 1

Informe de la validación contra el aula virtual de la USM, realizada el 2026-11-07. Es un documento fechado: no se actualiza. Las decisiones que cambiaron por estos resultados se reflejan en el [registro de decisiones](../registro-de-decisiones.md).

## 1. Objetivo

Comprobar, antes de escribir código en Rust, que la API de la instancia real permite lo que el diseño supone. El registro definía cuatro puntos a validar:

1. Funciones de la API habilitadas para el rol de alumno.
2. Funcionamiento del autologin.
3. Disponibilidad del plugin de asistencia.
4. Disponibilidad de la exportación iCal.

## 2. Método

- **Herramienta:** `validate_usm.py`, un script en Python que reutiliza el token guardado por MoodleConector o por su fork AulaVirtualSync. Se ejecutó desde una copia personal que no está bajo control de versiones.
- **Instancia:** `https://aula.usm.cl`, Moodle 4.5.15, con una cuenta de alumno inscrita en 6 ramos.
- **Seguridad:** el script no imprime ni guarda tokens y espera entre llamadas para no cargar el servidor. El informe que genera queda en `validation/`, dentro de la copia personal del script, fuera del repositorio de AulaDesk.
- **Login:** `launch.php` con un passport aleatorio, desde el navegador del sistema, con el SSO de Microsoft y MFA.
- **Autologin:** la URL generada se abrió en una ventana de incógnito, sin sesión previa, para que la prueba fuera concluyente.

## 3. Resultados

| Punto | Resultado | Detalle |
|---|---|---|
| Funciones de la API | Cumple | 463 funciones habilitadas, incluidas todas las previstas: cursos, actualizaciones, archivos, calificaciones, calendario, notificaciones, tareas, foros y cuestionarios. |
| Autologin | Cumple, con restricciones | La URL abrió el aula con la sesión iniciada en incógnito. Requiere un User-Agent con `MoodleMobile` y permite una clave cada 6 minutos. |
| Plugin de asistencia | No disponible por API | No hay funciones `mod_attendance_*`, aunque 2 de los 6 ramos usan el plugin. |
| Exportación iCal | Cumple | `core_calendar_get_calendar_export_token` y `export_execute.php` devolvieron 13 eventos. |

### 3.1 Login y firma

- La firma del passport se calcula con la `wwwroot` sin `/` final: `md5("https://aula.usm.cl" + passport)`. Con `/` final no coincide.
- El flujo funcionó con el navegador del sistema, sin Playwright.
- La USM tiene configurado `typeoflogin = 3` (navegador embebido) para la app oficial. El login con navegador externo funciona igual.

### 3.2 Token y `privatetoken`

En dos logins distintos, Moodle entregó el mismo token y el mismo `privatetoken`. Moodle los reutiliza por usuario y servicio: un login nuevo no los renueva. Solo se invalidan al reiniciar la clave de seguridad (Preferencias → Claves de seguridad) o cuando caducan.

### 3.3 Autologin

- `tool_mobile_get_autologin_key` responde `apprequired` si el User-Agent no contiene `MoodleMobile`.
- La configuración `tool_mobile_autologinmintimebetweenreq = 360` permite una clave cada 6 minutos.

### 3.4 Contenido de los cursos

- 164 de las 303 actividades de los 6 ramos son etiquetas (`label`).
- 3 ramos tienen secciones vacías.
- Las ponderaciones de las calificaciones solo vienen en los ítems ya calificados.

### 3.5 Sitio web

`launch.php` responde con:

```
Content-Security-Policy: frame-ancestors 'self' http://localhost:* capacitor: ionic: moodleappfs://localhost
```

Si el resto del sitio usa la misma política, el aula no se puede mostrar en un `<iframe>` dentro de la interfaz. En desarrollo (`http://localhost:1420`) funcionaría, pero fallaría en la app instalada (`tauri.localhost`).

### 3.6 Otros hallazgos

- `core_course_get_updates_since` responde.
- La descarga de archivos por API está habilitada (`downloadfiles`).
- Hay un gateway (`MindFreeWebGateway`) delante del aula.

## 4. Impacto en el diseño

| Elemento | Impacto |
|---|---|
| D-13 Login | La verificación de la firma usa la `wwwroot` sin `/` final. |
| D-14 Sesión | Cerrar sesión en la app no revoca el token en el servidor. |
| D-15 Autologin | Enviar un User-Agent con `MoodleMobile`. Conservar la sesión del WebView tras el primer autologin y abrir el enlace normal mientras dure el límite de 6 minutos. |
| D-16 Vista de cursos | La vista propia debe compactar u ocultar las etiquetas y las secciones vacías, la principal fuente del desorden. |
| D-17 Sitio real | Debe ir en una ventana o webview nativa de Tauri, no en un `<iframe>`. |
| D-19 Calendario | El modo simple con iCal es viable en la USM. |
| RF-02 Notificaciones | `core_course_get_updates_since` es la base para detectar cambios. |
| RF-03 Descargas | `course_sync.py` de AulaVirtualSync aporta opciones útiles: filtro por semestre, archivos ignorados, nombre de carpeta por ramo y accesos directos para enlaces externos. |
| RF-06 Nota del ramo | El usuario debe ingresar las ponderaciones del programa del ramo. |
| RF-06 Asistencia | Limitado en la USM mientras no haya una vía por API. |
| P-03 Autologin por defecto | La validación respalda activarlo en la USM. |
| Riesgos | Nuevos: gateway, límite del autologin, token filtrado válido hasta reiniciar la clave, dependencia de `launch.php`. |

## 5. Pendiente

- Probar `tool_mobile_get_plugins_supporting_mobile` y `tool_mobile_get_content` como vía alternativa para la asistencia (RF-06).

## 6. Lección

Los tokens no deben aparecer en capturas, registros ni informes. Ante cualquier exposición, hay que reiniciar la clave de seguridad del servicio móvil, porque un token filtrado sigue siendo válido aunque se vuelva a iniciar sesión.
