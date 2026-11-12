---
title: AulaDesk - Registro de decisiones
project: AulaDesk (nombre temporal)
stage: Análisis y diseño inicial
updated: 2026-11-11
---

# AulaDesk - Registro de decisiones

AulaDesk es un nombre temporal. El nombre definitivo no puede incluir "Moodle", según la política de marca de Moodle.

| Campo | Valor |
|---|---|
| Etapa | Análisis y diseño inicial |
| Última actualización | 2026-11-11 |
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

MoodleConector, un toolkit propio en Python, demostró que el login mediante `launch.php` funciona con el SSO y la MFA de la USM, y que el token de la API persiste sin necesidad de iniciar sesión a diario. El mismo flujo funciona en AulaVirtualSync, un fork propio que además guarda la clave de cifrado en el keyring del sistema operativo.

---

## 2. Principios del proyecto

| Principio | Descripción |
|---|---|
| No bloquear | Evitar decisiones que impidan funciones futuras (acciones en notificaciones, macOS, autologin), aunque más adelante haya que reescribir código. |
| No anticipar (YAGNI) | No construir por adelantado lo que podría necesitarse, para no dejar integraciones incompletas. |
| Qué antes que cómo | En esta etapa se define qué hace el sistema; la implementación se define en el diseño detallado. |
| Secretos fuera del repositorio | Ningún token, credencial ni dato personal debe llegar al control de versiones. |
| Siempre fijar versiones | Toda herramienta, dependencia y entorno tiene su versión fijada en el repositorio. Nada se actualiza sin un PR que pase la CI. |
| Todo como código | Todo lo necesario para compilar, probar y levantar servicios vive en el repositorio, con un único punto de entrada que se usa igual en la máquina local y en la CI. |

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

Alcance confirmado por la [validación de la fase 1](validacion/fase-1.md):

- **RF-03:** `course_sync.py` de AulaVirtualSync sirve de referencia: filtro por semestre, archivos ignorados, nombre de carpeta por ramo y accesos directos para enlaces externos.
- **RF-06, nota del ramo:** las ponderaciones solo vienen en los ítems ya calificados, así que el usuario debe ingresar las del programa del ramo.
- **RF-06, asistencia:** la USM no expone funciones `mod_attendance_*`. Queda limitada mientras no haya otra vía por API.

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
| D-23 | Documentación | Carpeta `docs/`, publicada con GitHub Pages desde la fase 1 | Decidido |
| D-24 | Registro de decisiones | ADR en formato MADR mínimo; este documento como índice | Decidido |
| D-25 | Sitio de documentación | Zensical (mdBook como alternativa) | Decidido |
| D-26 | Diagramas | C4 en Structurizr DSL; diagramas de secuencia en Mermaid | Decidido |
| D-27 | Entorno de desarrollo | Windows con RustRover; Linux en dual boot para validar | Decidido |
| D-28 | Herramientas de calidad | rustfmt, clippy, cargo-deny, cargo-nextest, cargo-llvm-cov, cargo-machete, mockito e insta | Decidido |
| D-29 | Hooks y tareas | prek con `.pre-commit-config.yaml`; tareas con `just` | Decidido |
| D-30 | Flujo de Git | GitHub Flow con ramas cortas, Conventional Commits, PR con regla en `main` y Dependabot | Decidido |
| D-31 | Moodle de pruebas | Instancia local en Docker, definida en el repositorio | Decidido |
| D-32 | CI | Linux en cada push y Windows en los PR mientras el repositorio sea privado | Decidido |

---

## 5. Detalle de decisiones

### 5.1 Visión y alcance

**D-01 Usuarios.** Solo estudiantes. Los roles de profesor, ayudante y administrador implican flujos más complejos y no hay cuentas disponibles para probarlos. La app solo usa funciones de la API disponibles para el rol de alumno.

**D-02 Público.** Cualquier estudiante de cualquier instancia de Moodle, con la app publicada. Esto también aporta valor como portafolio.

**D-03 Plataformas.** Windows y Linux. macOS queda pospuesto, sin tomar decisiones que lo impidan.

**D-04 Idioma de la interfaz.** Español. No se prepara la app para traducción por ahora, en línea con el principio de no anticipar.

### 5.2 Arquitectura y tecnología

El detalle, con las alternativas consideradas, está en los ADR [Arquitectura](adr/0003-arquitectura.md) y [Tecnología](adr/0004-tecnologia.md).

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

El detalle, con las alternativas consideradas, está en el ADR [Login y sesión](adr/0005-login-y-sesion.md). El flujo completo está en el [diagrama de secuencia del login](diagramas/login.md).

**D-13 Login.**

- El login se realiza en el navegador externo mediante `launch.php`. La app nunca ve la contraseña ni el segundo factor.
- El token llega a la app por deep link (`moodlemobile://`), con instancia única.
- Cada login genera un passport aleatorio y la app verifica la firma `md5(wwwroot + passport)`, con la `wwwroot` sin `/` final. Se rechaza cualquier token que no provenga de un login iniciado por la app.
- Validado en la USM con el SSO de Microsoft y MFA, desde el navegador del sistema.

**D-14 Sesión.** El token persiste, se valida antes de usarse y solo se vuelve a pedir login si Moodle lo rechaza. Es el mecanismo de MoodleConector, con tres cambios: navegador del sistema en lugar de Playwright, verificación de la firma y keyring en lugar de un archivo cifrado.

Moodle reutiliza el token y el `privatetoken` por usuario y servicio: un login nuevo no los renueva. Solo se invalidan al reiniciar la clave de seguridad o cuando caducan, por lo que cerrar sesión en la app no los revoca en el servidor.

**D-15 Autologin.** Se implementa como opción por instancia, mediante `tool_mobile_get_autologin_key`, con alternativa automática al enlace normal si el sitio no lo soporta. El `privatetoken` se guarda desde la fase 1.

Restricciones comprobadas en la USM:

- `tool_mobile_get_autologin_key` exige un User-Agent que contenga `MoodleMobile`; sin él responde `apprequired`.
- Se permite una clave cada 6 minutos (`tool_mobile_autologinmintimebetweenreq = 360`). El WebView conserva la sesión tras el primer autologin y abre el enlace normal mientras dure el límite.

### 5.6 Visualización

El detalle, con las alternativas consideradas, está en el ADR [Vista de cursos y sitio real](adr/0006-vista-de-cursos-y-sitio-real.md).

**D-16 Vista de cursos.** La vista propia, construida con la API, es la vista por defecto, con la opción de ver el curso en su formato normal. Compacta u oculta las etiquetas (`label`) y las secciones vacías, la principal fuente del desorden: en la USM, 164 de 303 actividades son etiquetas.

**D-17 Sitio real.** Se abre dentro de la app, en un WebView aislado, para poder controlar cómo se presenta el contenido. Los cuestionarios, foros y entregas se realizan en el sitio real.

El sitio se muestra en una ventana o webview nativa de Tauri, no en un `<iframe>`: la política `frame-ancestors` del aula solo permite `localhost` y los esquemas de la app oficial, así que un `<iframe>` funcionaría en desarrollo y fallaría en la app instalada.

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

La exportación iCal está activa en la USM.

### 5.8 Publicación y documentación

**D-20 Licencia.** Apache 2.0. Es compatible con todas las dependencias del proyecto y con GPLv3, en caso de un cambio futuro (que no sería retroactivo).

**D-21 Visibilidad del repositorio.** Privado durante la fase 1. Público al pasar a la fase 2, cuando la CLI funcione, con un README que indique que el proyecto está en desarrollo.

**D-22 Nombre.** AulaDesk como nombre temporal (`auladesk-core`, `auladesk-cli`, `auladesk-desktop`).

**D-23 Documentación.** Se escribe en `docs/` dentro del repositorio y se publica con GitHub Pages desde la fase 1, aunque el repositorio sea privado. El sitio es público y solo contiene documentación, sin código ni secretos. La publicación se controla con la variable `PAGES_ENABLED` del workflow Docs.

**D-24 Registro de decisiones.** Un ADR por decisión importante, en formato MADR (variante mínima), en `docs/adr/`. Este documento funciona como índice.

**D-25 Sitio de documentación.** Zensical, sucesor de Material for MkDocs. mdBook queda como alternativa según el formato y el diseño; no se cambia por ahora.

**D-26 Diagramas.** Diagramas C4 en Structurizr DSL (`arquitectura/workspace.dsl`), publicados como sitio estático en `/arquitectura/`. Diagramas de secuencia en Mermaid, en `docs/diagramas/`. El detalle, con las alternativas consideradas, está en el ADR [Diagramas C4 como código](adr/0002-diagramas-c4-como-codigo.md).

### 5.9 Entorno de desarrollo

El detalle, con las alternativas consideradas, está en el ADR [Entorno de desarrollo](adr/0001-entorno-de-desarrollo.md).

**D-27 Entorno de desarrollo.**

- Sistema principal: Windows, en el computador de escritorio. Linux en dual boot en el ThinkPad cuando se necesite un entorno Linux completo (WebKitGTK, bandeja, Secret Service, registro del deep link).
- Editor: RustRover, con la licencia educativa de JetBrains (GitHub Student Developer Pack). Su HTTP Client se usa para explorar la API y su visor de bases de datos para SQLite. Bruno queda como alternativa si el HTTP Client no basta.
- Si compilar en el ThinkPad resulta muy lento: desarrollo remoto de RustRover, con el escritorio como servidor.
- Rust fijado con `rust-toolchain.toml` (`channel = "1.99"`, componentes `rustfmt`, `clippy` y `llvm-tools-preview`).
- `.gitattributes` con `* text=auto eol=lf`, para no mezclar finales de línea entre Windows y Linux.
- La toolchain de JavaScript se define en la fase 2 (P-13).

**D-28 Herramientas de calidad.**

| Herramienta | Uso |
|---|---|
| `rustfmt` | Formato estándar. `rustfmt.toml` mínimo, con `edition` y `style_edition`. RustRover formatea con rustfmt al guardar. |
| `clippy` | Linter, con `-D warnings` |
| `cargo-deny` | Licencias compatibles con Apache 2.0 (D-20), vulnerabilidades (RustSec), dependencias duplicadas y orígenes |
| `cargo-nextest` | Ejecución de tests |
| `cargo-llvm-cov` | Cobertura de tests; el umbral y la publicación se definen después (P-15) |
| `cargo-machete` | Dependencias declaradas sin usar, en la CI |
| `mockito` | Servidor HTTP simulado para los tests del cliente de la API, con `assert_on_drop` |
| `insta` | Tests de snapshot |

Descartados: `bacon` (redundante con RustRover) y `cargo-audit` (cubierto por `cargo-deny`).

**D-29 Hooks y tareas.**

- prek ejecuta `.pre-commit-config.yaml`, el formato estándar de pre-commit, sin depender de Python, e instala cada herramienta con su versión fijada.

| Momento | Revisión |
|---|---|
| pre-commit | Betterleaks y `rustfmt` |
| commit-msg | Conventional Commits |
| pre-push | `clippy` y tests |

- `just` es el único punto de entrada para las tareas (`just check`, `just test`, `just coverage`, `just moodle-up`). La CI usa los mismos comandos.

**D-30 Flujo de Git.**

- Mensajes con Conventional Commits, validados por el hook `commit-msg`.
- Los cambios llegan a `main` mediante PR, con una regla que exige pasar la CI. En un repositorio privado requiere GitHub Pro, incluido en el GitHub Student Developer Pack.
- Estrategia de ramas: GitHub Flow. `main` es la única rama permanente. Cada cambio se hace en una rama corta creada desde `main`, que se integra con squash merge y se borra al integrarse. El título del PR queda como mensaje del commit en `main`, por lo que también sigue Conventional Commits.
- Nombres de rama: `tipo/descripcion-corta`, en inglés y en minúsculas con guiones, con los tipos de Conventional Commits (`feat/`, `fix/`, `docs/`, `refactor/`, `test/`, `ci/`, `chore/`). `spike/` se usa para experimentos que pueden descartarse sin integrarse.
- Versiones: tags SemVer anotados (`vX.Y.Z`) sobre `main`, en `0.x` mientras la API no sea estable. Una rama `release/X.Y` se crea solo si hay que corregir una versión anterior sin incluir lo nuevo de `main`.
- Descartado: Git Flow (rama `develop` y ramas de release y hotfix permanentes). Sirve para mantener varias versiones publicadas a la vez; con un solo desarrollador agrega pasos sin beneficio.
- Dependabot actualiza crates, GitHub Actions, hooks y la versión de Rust.
- `Cargo.lock` se versiona.
- Los pasos para trabajar con este flujo están en [`CONTRIBUTING.md`](https://github.com/Sebastian-Alvarez/AulaDesk/blob/main/CONTRIBUTING.md).

**D-31 Moodle de pruebas.**

- Instancia local de Moodle 4.5 (la versión de la USM) en Docker, definida en `dev/moodle/` y levantada con `just moodle-up`.
- Configuración y datos de ejemplo automáticos: servicios web y servicio móvil, un usuario estudiante de prueba, cursos, calificaciones, tareas y eventos.
- El código de Moodle (GPL) se descarga al iniciar, con un tag fijo, y no se incluye en el repositorio (D-20).
- Se usa para el desarrollo diario, el HTTP Client y los tests de integración. La USM real se usa solo para validar lo que no se reproduce localmente (SSO y MFA, gateway, autologin, CSP), mediante la CLI o `validate_usm.py`.

**D-32 CI.**

| Etapa | Cada push | PR hacia `main` |
|---|---|---|
| Repositorio privado (fase 1) | Linux: revisiones en un solo job y tests | Linux y Windows; cobertura en Linux; tests de integración con el Moodle de pruebas |
| Repositorio público (fase 2) | Linux y Windows | Igual que en la fase 1 |

- Caché de compilación, cancelación de ejecuciones obsoletas y exclusión de los cambios que solo afectan a `docs/`.
- Las compilaciones completas de la app (instaladores) corren solo en los PR hacia `main` o al crear una versión.

---

## 6. Decisiones pospuestas

| ID | Tema | Cuándo se define |
|---|---|---|
| P-01 | Local-first: si la base local es la fuente de verdad de la interfaz | Diseño detallado |
| P-02 | Prioridad entre objetivos | Planificación de la primera versión |
| P-03 | Autologin activado por defecto (la validación lo respalda en la USM) | Diseño detallado |
| P-04 | Formato de la exportación de calificaciones | Diseño detallado |
| P-05 | Diagrama de secuencia sincronización → notificación | Al terminar la etapa exploratoria |
| P-06 | Acciones dentro de las notificaciones | Después de la fase 2 |
| P-07 | Soporte para macOS | Con el software estable |
| P-08 | WebView embebido como respaldo del login | Sin fecha |
| P-09 | Foros y entregas nativos | Después de la fase 1 |
| P-10 | Integración con Notion u otras herramientas | Sin fecha |
| P-11 | Traducción de la interfaz | Sin fecha |
| P-12 | Primera versión pública: nombre definitivo, identificador de la app, firma en Windows, canales de distribución, actualizaciones automáticas, modelo e identidad visual | Antes de la primera versión pública |
| P-13 | Toolchain de JavaScript: pnpm o npm, y cómo fijar la versión de Node | Fase 2 |
| P-14 | `proptest`, `cargo doc` sin advertencias y `typos` | Cuando el núcleo lo requiera (`proptest` con RF-06) |
| P-15 | Umbral mínimo de cobertura y publicación de resultados (Codecov o similar) | Cuando el núcleo tenga tests |

El identificador de la app (P-12) debe definirse antes de publicar: cambiarlo después hace que los usuarios pierdan su configuración y su sesión.

---

## 7. Lineamientos

### 7.1 Secretos en el repositorio

| Medida | Estado |
|---|---|
| `.gitignore` estricto desde el primer commit: bases de datos locales, cachés, `.env`, credenciales, respuestas guardadas de la API (incluida `.idea/httpRequests/`), `http-client.private.env.json` y descargas | Decidido |
| Escáner de secretos (`betterleaks`) como hook previo al commit, ejecutado por prek con versión fijada, y en la CI | Decidido |
| Regla propia de Betterleaks para detectar valores de `wstoken` y `privatetoken` | Decidido |
| Revisión del historial completo con el escáner antes de hacer público el repositorio, incluidas las fixtures de los tests | Propuesto |

### 7.2 Seguridad de la aplicación

| Medida | Estado |
|---|---|
| Token y `privatetoken` solo en el keyring del sistema operativo; nunca en SQLite, archivos ni logs | Propuesto |
| WebView del sitio real aislado, sin permisos hacia el backend | Propuesto |
| Solo conexiones HTTPS hacia Moodle | Propuesto |
| Indicar al usuario cómo revocar el token (reiniciar la clave de seguridad), porque cerrar sesión no lo invalida | Propuesto |
| Token de la USM durante el desarrollo: nunca en archivos; solo a través de la CLI (keyring) o de `validate_usm.py`. El HTTP Client usa únicamente el token de prueba del Moodle local (D-31). Si el token se filtra, reiniciar la clave de seguridad. | Decidido |

### 7.3 Marca

- No usar "Moodle" en el nombre de la app, el dominio ni la publicidad.
- No usar los logos de Moodle, Tauri ni la USM.
- Incluir en el README un aviso de no afiliación con Moodle Pty Ltd (Propuesto).

---

## 8. Validación en la fase 1

Realizada el 2026-11-07. El detalle está en el [informe de validación](validacion/fase-1.md).

- [x] Funciones de la API habilitadas para alumnos (`core_webservice_get_site_info`): 463 funciones, incluidas todas las previstas.
- [x] Funcionamiento del autologin en la USM: funciona, con las restricciones de D-15.
- [x] Disponibilidad del plugin de asistencia (`mod_attendance_*`): no hay funciones disponibles.
- [x] Disponibilidad de la exportación iCal de Moodle: activa.
- [ ] Asistencia mediante `tool_mobile_get_plugins_supporting_mobile` y `tool_mobile_get_content` (pendiente).

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
| Gateway (`MindFreeWebGateway`) delante del aula de la USM | Podría limitar o bloquear clientes automatizados | Volumen moderado y reintentos con espera |
| Límite de una clave de autologin cada 6 minutos | Abrir varias páginas seguidas podría pedir login | Conservar la sesión del WebView (D-15) |
| Un token o `privatetoken` filtrado sigue válido hasta reiniciar la clave de seguridad | Acceso no autorizado a la cuenta | Nunca registrarlos en logs, capturas ni informes (7.2) |
| El login por navegador externo depende de que la instancia siga aceptando `launch.php` (en la USM, con `typeoflogin = 3`) | El login deja de funcionar | Activar el respaldo con WebView (P-08) |

---

## 10. Correcciones registradas

| Afirmación anterior | Corrección |
|---|---|
| La app oficial `moodleapp` tiene licencia GPLv3 | Su licencia es Apache 2.0. Su código se puede reutilizar conservando los avisos de copyright. |
| Wails v3 está en beta | Es estable desde el 2026-10-26. |
| Material for MkDocs como generador del sitio | Llegó al fin de su soporte el 2026-11-05; se reemplaza por Zensical. |
| Notion como parte del alcance | Fue una sugerencia durante el análisis, no un requisito; queda pospuesta (P-10). |
| Suscripción ICS desde un archivo local | Google y Outlook requieren una URL pública. |
| `gitleaks` como escáner de secretos | Desde marzo de 2026 solo recibe parches de seguridad. Se reemplaza por Betterleaks, su sucesor del mismo autor, por su mayor cobertura de tipos de secretos. |

---

## 11. Referencias

### 11.1 Artefactos del proyecto

| Artefacto | Ubicación |
|---|---|
| Diagramas C4 (niveles 1 y 2) | `arquitectura/workspace.dsl`, publicado en [Arquitectura (C4)](https://sebastian-alvarez.github.io/AulaDesk/arquitectura/) |
| Diagrama de secuencia del login | [Login](diagramas/login.md) |
| MoodleConector | Proyecto local previo (Python) |
| AulaVirtualSync | Fork propio de MoodleConector, pensado para cualquier estudiante: [`Sebastian-Alvarez/AulaVirtualSync`](https://github.com/Sebastian-Alvarez/AulaVirtualSync) |
| `validate_usm.py` | Script de validación de la fase 1 (fuera del repositorio) |
| Informe de validación | [Validación de la fase 1](validacion/fase-1.md) |

### 11.2 Proyectos relacionados

| Proyecto | Relevancia | Licencia |
|---|---|---|
| `moodleapp` (app oficial) | Referencia de endpoints y flujos; código reutilizable con atribución | Apache 2.0 |
| Muster | App de escritorio similar (Tauri y keyring) para el Moodle de Monash | No verificada |
| Edu Sync | CLI en Rust para sincronizar Moodle; solo como referencia de lectura | GPL-3.0 |
| moodle-dl | Descargador y notificador mediante la API móvil | No verificada |
| `Jabir-Srj/moodle-connector` | Proyecto del que deriva MoodleConector; el flujo con `launch.php` llegó en su PR #1, de SebaG20xx | MIT |

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
| Clave de seguridad | En Moodle, el token de un servicio, visible en Preferencias → Claves de seguridad. Reiniciarla revoca el token y el `privatetoken`. |
| Conventional Commits | Convención para los mensajes de commit (`feat:`, `fix:`, etc.) que permite generar el changelog automáticamente. |
| Hook de Git | Script que Git ejecuta en ciertos momentos (antes de un commit o de un push) y que cancela la operación si falla. |
| WebView | Componente que muestra contenido web dentro de una aplicación de escritorio. |
