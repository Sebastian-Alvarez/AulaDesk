---
title: AulaDesk - Registro de decisiones
project: AulaDesk (nombre temporal)
stage: Análisis y diseño inicial
updated: 2026-11-06
---

# AulaDesk - Registro de decisiones

AulaDesk es un nombre temporal. El nombre definitivo no puede incluir "Moodle", según la política de marca de Moodle.

| Campo | Valor |
|---|---|
| Etapa | Análisis y diseño inicial |
| Última actualización | 2026-11-06 |
| Estados usados | Decidido, Pospuesto, Pendiente, Propuesto |

## Contenido

1. [Contexto](#1-contexto)
2. [Principios del proyecto](#2-principios-del-proyecto)
3. [Alcance](#3-alcance)
4. [Índice de decisiones](#4-índice-de-decisiones)
5. [Detalle de decisiones](#5-detalle-de-decisiones)
6. [Decisiones pospuestas](#6-decisiones-pospuestas)
7. [Lineamientos](#7-lineamientos)
8. [Validación en la fase 1](#8-validación-en-la-fase-1)
9. [Riesgos y limitaciones](#9-riesgos-y-limitaciones)
10. [Correcciones registradas](#10-correcciones-registradas)
11. [Referencias](#11-referencias)
12. [Glosario](#12-glosario)

---

## 1. Contexto

### 1.1 Problema

- En el aula virtual de la USM hay que iniciar sesión todos los días. En la app oficial de Android se inicia sesión una vez y la sesión persiste.
- En el computador no existen integraciones equivalentes a las de la app móvil: notificaciones, descargas automáticas, calendario.
- La organización de los cursos en la USM es poco práctica, con secciones y pestañas que casi no se usan.
- La app oficial Moodle Desktop fue descontinuada.

### 1.2 Propuesta

Una aplicación de escritorio que se conecta a cualquier instancia de Moodle mediante su API oficial (Web Services), con un login persistente equivalente al de la app móvil, y que agrega integraciones con el sistema operativo y funciones que Moodle no ofrece.

### 1.3 Antecedente

MoodleConector, un toolkit propio en Python, demostró que el login mediante `launch.php` funciona con el SSO y la MFA de la USM, y que el token de la API persiste sin necesidad de iniciar sesión a diario.

---

## 2. Principios del proyecto

| Principio | Descripción |
|---|---|
| No bloquear | Evitar decisiones que impidan funciones futuras (acciones en notificaciones, macOS, autologin), aunque más adelante haya que reescribir código. |
| No anticipar (YAGNI) | No construir por adelantado lo que podría necesitarse, para no dejar integraciones incompletas. |
| Qué antes que cómo | En esta etapa se define qué hace el sistema; la implementación se define en el diseño detallado. |
| Secretos fuera del repositorio | Ningún token, credencial ni dato personal debe llegar al control de versiones. |

---

## 3. Alcance

### 3.1 Requisitos funcionales

| ID | Requisito |
|---|---|
| RF-01 | Login persistente compatible con 2FA y SSO. |
| RF-02 | Notificaciones de contenido nuevo, calificaciones publicadas y entregas habilitadas. |
| RF-03 | Descarga automática del material a una carpeta local, ordenado por ramo. |
| RF-04 | Fechas de evaluaciones y entregas en un calendario. |
| RF-05 | Exportación de las calificaciones disponibles. |
| RF-06 | Cálculo de la nota del ramo y de la asistencia, sujeto a los datos que entregue cada instancia. |
| RF-07 | Vista personalizada del contenido de los cursos. |
| RF-08 | Acceso al sitio real para interactuar: foros, cuestionarios y entregas. |

### 3.2 Requisitos no funcionales

| ID | Requisito |
|---|---|
| RNF-01 | Liviano en consumo de recursos. |
| RNF-02 | Multiplataforma: Windows y Linux. |
| RNF-03 | Compatible con múltiples instancias de Moodle, adaptándose a lo que cada una tenga habilitado. |
| RNF-04 | Proyecto presentable como portafolio en GitHub. |

### 3.3 Fuera de alcance

- Roles de profesor, ayudante y administrador.
- macOS (pospuesto).
- Integraciones con Notion u otras herramientas de productividad (pospuesto).

---

## 4. Índice de decisiones

| ID | Tema | Decisión | Estado |
|---|---|---|---|
| D-01 | Usuarios | Solo estudiantes | Decidido |
| D-02 | Público | Cualquier estudiante de cualquier Moodle; app publicada | Decidido |
| D-03 | Plataformas | Windows y Linux | Decidido |
| D-04 | Idioma de la interfaz | Español, sin preparación para traducción | Decidido |
| D-05 | Modelo de proceso | Una sola app residente en la bandeja del sistema | Decidido |
| D-06 | Arquitectura | Hexagonal con bus de eventos | Decidido |
| D-07 | Estructura | Núcleo como librería; fase 1 con CLI, fase 2 con app | Decidido |
| D-08 | Tecnología | Rust + Tauri 2 | Decidido |
| D-09 | Interfaz | React + TypeScript | Decidido |
| D-10 | Repositorio | Workspace de Cargo y CI con GitHub Actions | Decidido |
| D-11 | Idioma del repositorio | Código y commits en inglés; documentación en español | Decidido |
| D-12 | Datos | SQLite, incluyendo la respuesta cruda de Moodle | Decidido |
| D-13 | Login | Navegador externo, deep link y validación del passport | Decidido |
| D-14 | Sesión | Token persistente, validado antes de usarse | Decidido |
| D-15 | Autologin | Opcional por instancia; `privatetoken` guardado desde la fase 1 | Decidido |
| D-16 | Vista de cursos | Vista propia por defecto, con opción de vista normal | Decidido |
| D-17 | Sitio real | Dentro de la app, en un WebView aislado | Decidido |
| D-18 | Notificaciones | Nativas, centro propio, contador en bandeja y modos configurables | Decidido |
| D-19 | Calendario | Modo simple (iCal de Moodle) y modo integrado (Google Calendar API) | Decidido |
| D-20 | Licencia | Apache 2.0 | Decidido |
| D-21 | Visibilidad del repositorio | Privado en la fase 1; público con la CLI funcionando | Decidido |
| D-22 | Nombre | AulaDesk como nombre temporal | Decidido |
| D-23 | Documentación | Carpeta `docs/`, publicada con GitHub Pages al abrir el repositorio | Decidido |
| D-24 | Registro de decisiones | ADR en formato MADR mínimo; este documento como índice | Decidido |
| D-25 | Sitio de documentación | Zensical (mdBook como alternativa) | Decidido |
| D-26 | Diagramas | C4 en Lucidchart; diagramas de secuencia en Mermaid | Decidido |

---

## 5. Detalle de decisiones

### 5.1 Visión y alcance

**D-01 Usuarios.** Solo estudiantes. Los roles de profesor, ayudante y administrador implican flujos más complejos y no hay cuentas disponibles para probarlos. La app solo usa funciones de la API disponibles para el rol de alumno.

**D-02 Público.** Cualquier estudiante de cualquier instancia de Moodle, con la app publicada. Esto también aporta valor como portafolio.

**D-03 Plataformas.** Windows y Linux. macOS queda pospuesto, sin tomar decisiones que lo impidan.

**D-04 Idioma de la interfaz.** Español. No se prepara la app para traducción por ahora, en línea con el principio de no anticipar.

### 5.2 Arquitectura y tecnología

**D-05 Modelo de proceso.** Una sola aplicación residente en la bandeja del sistema, con el mismo comportamiento que Discord o Spotify: cerrar la ventana no detiene la sincronización ni las notificaciones, y "Salir" cierra la app por completo. No hay un servicio separado ni comunicación entre procesos.

**D-06 Arquitectura.** Hexagonal con bus de eventos:

- El núcleo define interfaces (puertos) para todo lo externo: API de Moodle, almacenamiento, notificaciones, archivos, calendario y exportación.
- Los componentes se comunican mediante eventos tipados (por ejemplo, `grade_published` o `new_material`).
- Cada suscriptor del bus está aislado: si uno falla, el error se registra y los demás continúan.

Alternativas consideradas: arquitectura en capas, Clean/Onion, *vertical slices*, orquestación directa, y monolito modular con observer simple. Una evaluación independiente también recomendó hexagonal con bus de eventos.

**D-07 Estructura.** El núcleo (cliente API, sincronización, base de datos y reglas) es una librería separada:

- Fase 1: una CLI que usa la librería, para validar la API de la USM antes de diseñar la interfaz.
- Fase 2: la aplicación de escritorio, sobre la misma librería.

**D-08 Tecnología.** Rust + Tauri 2, verificado contra los criterios del proyecto:

| Criterio | Cobertura en Tauri 2 |
|---|---|
| Bandeja, proceso único, arranque con el sistema | Bandeja en el núcleo; plugin oficial `autostart` |
| Deep link con instancia única | Plugins oficiales `deep-link` y `single-instance` |
| WebView del sitio real aislado | Sin acceso al backend por defecto; filtro de navegación (`on_navigation`) |
| Inyección de CSS en el sitio real | Script de inicialización con validación del origen |
| Notificaciones nativas | Plugin oficial; en Windows requiere la app instalada |
| Acciones en notificaciones (futuro) | Plugin de la comunidad `tauri-plugin-notifications` o crate `notify-rust`, detrás del puerto de notificaciones |
| Compatibilidad futura con macOS | Soportada |

Alternativa considerada: Go + Wails v3 (estable desde el 2026-10-26). Cubre todos los criterios y es más fácil de aprender, pero Tauri 2 es la opción más probada para estos requisitos.

**D-09 Interfaz.** React + TypeScript.

### 5.3 Repositorio

**D-10 Repositorio.** Un repositorio con un workspace de Cargo:

```
auladesk/
├── auladesk-core/      # librería: API, sincronización, almacenamiento, reglas
├── auladesk-cli/       # fase 1
├── auladesk-desktop/   # fase 2
└── docs/               # documentación
```

Integración continua con GitHub Actions: compilación y pruebas en Windows y Linux.

**D-11 Idioma del repositorio.** Código, comentarios y commits en inglés, que es el estándar de la industria. Documentación (README, ADR, diagramas) en español.

### 5.4 Datos

**D-12 Base de datos.** SQLite como base de datos local. Se guarda también la respuesta cruda de Moodle en formato JSON; es una decisión reversible.

| Tipo de dato | Contenido | Reconstruible |
|---|---|---|
| Derivado de Moodle | Copia del estado para detectar cambios, estado de sincronización, registro de descargas | Sí, sincronizando de nuevo sin notificar todo otra vez |
| Propio de la app | Cuentas, preferencias, alertas, ponderaciones manuales, vista personalizada, historial de notificaciones | No |

La base de datos no guarda tokens (van en el keyring del sistema operativo), archivos descargados (van en la carpeta local) ni contraseñas (la app nunca las recibe).

### 5.5 Login y sesión

**D-13 Login.**

- El login se realiza en el navegador externo mediante `launch.php`. La app nunca ve la contraseña ni el segundo factor.
- El token llega a la app por deep link (`moodlemobile://`), con instancia única.
- Cada login genera un passport aleatorio y la app verifica la firma `md5(url + passport)`. Se rechaza cualquier token que no provenga de un login iniciado por la app.

**D-14 Sesión.** El token persiste, se valida antes de usarse y solo se vuelve a pedir login si Moodle lo rechaza. Es el mecanismo de MoodleConector, con tres cambios: navegador del sistema en lugar de Playwright, verificación de la firma y keyring en lugar de un archivo cifrado.

**D-15 Autologin.** Se implementa como opción por instancia, mediante `tool_mobile_get_autologin_key`, con alternativa automática al enlace normal si el sitio no lo soporta. El `privatetoken` se guarda desde la fase 1.

### 5.6 Visualización

**D-16 Vista de cursos.** La vista propia, construida con la API, es la vista por defecto, con la opción de ver el curso en su formato normal.

**D-17 Sitio real.** Se abre dentro de la app, en un WebView aislado, para poder controlar cómo se presenta el contenido. Los cuestionarios, foros y entregas se realizan en el sitio real.

### 5.7 Notificaciones e integraciones

**D-18 Notificaciones.**

- Notificaciones nativas del sistema operativo.
- Centro de notificaciones propio, con historial y estado de leído.
- Contador en el ícono de la bandeja.
- Ajuste de modo: Sistema, Solo en la app o Ninguna.

**D-19 Calendario.**

| Modo | Mecanismo | Uso |
|---|---|---|
| Simple | URL iCal del propio Moodle | Sin login adicional |
| Integrado | API de Google Calendar con login | Calendario personalizado y automatizaciones |

### 5.8 Publicación y documentación

**D-20 Licencia.** Apache 2.0. Es compatible con todas las dependencias del proyecto y con GPLv3, en caso de un cambio futuro (que no sería retroactivo).

**D-21 Visibilidad del repositorio.** Privado durante la fase 1. Público cuando la CLI funcione, con un README que indique que el proyecto está en desarrollo.

**D-22 Nombre.** AulaDesk como nombre temporal (`auladesk-core`, `auladesk-cli`, `auladesk-desktop`).

**D-23 Documentación.** Se escribe en `docs/` dentro del repositorio y se publica con GitHub Pages cuando el repositorio sea público. Un sitio de GitHub Pages es público aunque el repositorio sea privado.

**D-24 Registro de decisiones.** Un ADR por decisión importante, en formato MADR (variante mínima), en `docs/adr/`. Este documento funciona como índice.

**D-25 Sitio de documentación.** Zensical, sucesor de Material for MkDocs. mdBook queda como alternativa según el formato y el diseño; no se cambia por ahora.

**D-26 Diagramas.** Diagramas C4 en Lucidchart, en el documento "AulaDesk - C4 Diagrams" (niveles 1 y 2). Diagramas de secuencia en Mermaid.

---

## 6. Decisiones pospuestas

| ID | Tema | Cuándo se define |
|---|---|---|
| P-01 | Local-first: si la base local es la fuente de verdad de la interfaz | Diseño detallado |
| P-02 | Prioridad entre objetivos | Planificación de la primera versión |
| P-03 | Autologin activado por defecto | Diseño detallado |
| P-04 | Formato de la exportación de calificaciones | Diseño detallado |
| P-05 | Diagrama de secuencia sincronización → notificación | Al terminar la etapa exploratoria |
| P-06 | Acciones dentro de las notificaciones | Después de la fase 2 |
| P-07 | Soporte para macOS | Con el software estable |
| P-08 | WebView embebido como respaldo del login | Sin fecha |
| P-09 | Foros y entregas nativos | Después de la fase 1 |
| P-10 | Integración con Notion u otras herramientas | Sin fecha |
| P-11 | Traducción de la interfaz | Sin fecha |
| P-12 | Primera versión pública: nombre definitivo, identificador de la app, firma en Windows, canales de distribución, actualizaciones automáticas, modelo e identidad visual | Antes de la primera versión pública |

El identificador de la app (P-12) debe definirse antes de publicar: cambiarlo después hace que los usuarios pierdan su configuración y su sesión.

---

## 7. Lineamientos

### 7.1 Secretos en el repositorio

| Medida | Estado |
|---|---|
| `.gitignore` estricto desde el primer commit: bases de datos locales, cachés, `.env`, credenciales, respuestas guardadas de la API y descargas | Decidido |
| Escáner de secretos (`gitleaks`) como hook previo al commit y en la CI | Propuesto |
| Revisión del historial completo con el escáner antes de hacer público el repositorio | Propuesto |

### 7.2 Seguridad de la aplicación

| Medida | Estado |
|---|---|
| Token y `privatetoken` solo en el keyring del sistema operativo; nunca en SQLite, archivos ni logs | Propuesto |
| WebView del sitio real aislado, sin permisos hacia el backend | Propuesto |
| Solo conexiones HTTPS hacia Moodle | Propuesto |

### 7.3 Marca

- No usar "Moodle" en el nombre de la app, el dominio ni la publicidad.
- No usar los logos de Moodle, Tauri ni la USM.
- Incluir en el README un aviso de no afiliación con Moodle Pty Ltd (Propuesto).

---

## 8. Validación en la fase 1

- [ ] Funciones de la API habilitadas para alumnos (`core_webservice_get_site_info`).
- [ ] Funcionamiento del autologin en la USM.
- [ ] Disponibilidad del plugin de asistencia (`mod_attendance_*`).
- [ ] Disponibilidad de la exportación iCal de Moodle.

---

## 9. Riesgos y limitaciones

| Riesgo o limitación | Impacto | Mitigación |
|---|---|---|
| El esquema `moodlemobile://` no se registra (por ejemplo, con un AppImage sin instalar) | El login no funciona | Distribuir paquetes instalables; respaldo con WebView pospuesto (P-08) |
| El WebView tiene una sesión propia | Sin autologin, hay que iniciar sesión dentro del WebView; en Linux (WebKitGTK) algunos SSO pueden comportarse distinto | Autologin como opción por instancia (D-15); evaluar el valor por defecto (P-03) |
| La personalización del sitio real mediante CSS depende del tema de cada institución | Los ajustes pueden romperse con cambios de tema | Vista propia como vista por defecto (D-16) |
| Google Calendar solo se suscribe a URL públicas | No puede leer un archivo local | Modo simple con la URL iCal de Moodle (D-19) |
| Notificaciones en Windows requieren la app instalada; las acciones en Linux dependen del entorno de escritorio | Comportamiento distinto en desarrollo y entre escritorios | Probar con instaladores; acciones pospuestas (P-06) |
| Uso automatizado de la API | Posible conflicto con políticas de TI de cada institución | Volumen moderado de peticiones y sincronización incremental |

---

## 10. Correcciones registradas

| Afirmación anterior | Corrección |
|---|---|
| La app oficial `moodleapp` tiene licencia GPLv3 | Su licencia es Apache 2.0. Su código se puede reutilizar conservando los avisos de copyright. |
| Wails v3 está en beta | Es estable desde el 2026-10-26. |
| Material for MkDocs como generador del sitio | Llegó al fin de su soporte el 2026-11-05; se reemplaza por Zensical. |
| Notion como parte del alcance | Fue una sugerencia durante el análisis, no un requisito; queda pospuesta (P-10). |
| Suscripción ICS desde un archivo local | Google y Outlook requieren una URL pública. |

---

## 11. Referencias

### 11.1 Artefactos del proyecto

| Artefacto | Ubicación |
|---|---|
| Diagramas C4 (niveles 1 y 2) | Lucidchart: "AulaDesk - C4 Diagrams" |
| Diagrama de secuencia del login | Mermaid |
| MoodleConector | Proyecto local previo (Python) |

### 11.2 Proyectos relacionados

| Proyecto | Relevancia | Licencia |
|---|---|---|
| `moodleapp` (app oficial) | Referencia de endpoints y flujos; código reutilizable con atribución | Apache 2.0 |
| Muster | App de escritorio similar (Tauri y keyring) para el Moodle de Monash | No verificada |
| Edu Sync | CLI en Rust para sincronizar Moodle; solo como referencia de lectura | GPL-3.0 |
| moodle-dl | Descargador y notificador mediante la API móvil | No verificada |

---

## 12. Glosario

| Término | Definición |
|---|---|
| ADR | *Architecture Decision Record*: nota breve que registra una decisión, su contexto, las alternativas y sus consecuencias. |
| MADR | *Markdown ADR*: plantilla de ADR que incluye opciones consideradas con sus pros y contras. |
| Autologin | Mecanismo de Moodle que genera una URL de un solo uso para abrir el sitio con una sesión ya iniciada. |
| Deep link | Enlace con un esquema propio (por ejemplo, `moodlemobile://`) que el sistema operativo entrega a una app instalada. |
| Local-first | Enfoque en que la copia local de los datos es la fuente de verdad de la interfaz. |
| Passport | Valor aleatorio que la app genera en cada login para verificar que el token recibido corresponde a una solicitud propia. |
| `privatetoken` | Token adicional que entrega Moodle en el login y que permite solicitar el autologin. |
| WebView | Componente que muestra contenido web dentro de una aplicación de escritorio. |
