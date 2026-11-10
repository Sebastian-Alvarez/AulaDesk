---
status: aceptado
date: 2026-11-08
decisions: D-27, D-28, D-29, D-30, D-31, D-32
---

# Entorno de desarrollo

## Contexto y problema

AulaDesk se desarrolla en Rust (núcleo y CLI en la fase 1) y Tauri con React y TypeScript (fase 2), para Windows y Linux (D-03). El desarrollo ocurre en dos equipos: un computador de escritorio con Windows y un ThinkPad que se usa por movilidad. El proyecto debe servir como portafolio (RNF-04), el repositorio es privado durante la fase 1 (D-21) y ningún secreto puede llegar al control de versiones.

Hay que definir el editor, el sistema de trabajo, las herramientas de calidad, los hooks de Git, el flujo de trabajo con Git, la CI y cómo probar contra Moodle sin exponer el token de la USM.

## Factores de decisión

- Estándares de la industria y del ecosistema Rust.
- Valor como portafolio.
- Buenas prácticas de DevOps y CI/CD: las mismas revisiones en la máquina local y en la CI.
- Principios del proyecto: no anticipar, secretos fuera del repositorio, siempre fijar versiones y todo como código.
- Pocas dependencias y bajo costo de la CI mientras el repositorio sea privado.

## Opciones consideradas

| Tema | Opciones | Elegida | Razón |
|---|---|---|---|
| Sistema | Windows nativo, WSL2, Linux en dual boot, VM, Dev Container | Windows + dual boot en el ThinkPad | Las integraciones (notificaciones, keyring, deep link) solo se prueban de verdad en el sistema real; WSL2 no reproduce el escritorio Linux |
| Editor | VS Code, RustRover, Zed, Neovim o Helix | RustRover | Cubre Rust, TypeScript y React, integra nextest, cobertura, HTTP Client y visor de bases de datos; licencia educativa disponible |
| Escáner de secretos | gitleaks, Betterleaks | Betterleaks | gitleaks solo recibe parches de seguridad; Betterleaks, del mismo autor, detecta más tipos de secretos |
| Gestor de hooks | Hook manual, pre-commit, prek, lefthook | prek | Usa el formato estándar `.pre-commit-config.yaml`, no requiere Python y fija la versión de cada herramienta |
| Herramientas de Rust | rustfmt, clippy, cargo-deny, cargo-audit, cargo-nextest, bacon, cargo-llvm-cov, cargo-machete | Todas menos `cargo-audit` y `bacon` | `cargo-deny` ya cubre las vulnerabilidades; `bacon` es redundante con RustRover |
| HTTP simulado | wiremock, mockito | mockito | Equivalentes en lo esencial; mockito incluye el matcher de formularios que usa la API de Moodle |
| Actualizaciones | Dependabot, Renovate | Dependabot | Integrado en GitHub; actualiza crates, Actions, hooks y la versión de Rust |
| Tareas | `just`, `cargo xtask` | `just` | El proyecto combina Rust, Docker y, en la fase 2, Node; las tareas son secuencias de comandos |
| Cliente HTTP | HTTP Client de RustRover, Bruno, Hoppscotch, Postman, Insomnia, curl, scripts | HTTP Client de RustRover | Sin otra aplicación; Bruno como alternativa |
| Visor SQLite | DBeaver, visor de RustRover | Visor de RustRover | Ya incluido en el editor |
| Token para explorar la API | Token de la USM en un archivo, sin token en archivos, Moodle local | Moodle local | El token de prueba no es un riesgo; el de la USM solo se usa a través del keyring |

## Resultado de la decisión

El detalle de cada decisión está en el [registro de decisiones](../registro-de-decisiones.md) (D-27 a D-32). En resumen:

- **Entorno:** Windows con RustRover; Linux en dual boot en el ThinkPad; desarrollo remoto de RustRover como respaldo si el ThinkPad es lento. Rust fijado en `1.99` con `rust-toolchain.toml` y `.gitattributes` con finales de línea LF.
- **Calidad:** `rustfmt`, `clippy -D warnings`, `cargo-deny`, `cargo-nextest`, `cargo-llvm-cov`, `cargo-machete`, `mockito` e `insta`.
- **Hooks (prek):** Betterleaks y `rustfmt` en pre-commit; Conventional Commits en commit-msg; `clippy` y tests en pre-push.
- **Tareas:** `just` como punto de entrada único, usado también por la CI.
- **Git:** Conventional Commits, PR hacia `main` con una regla que exige la CI, Dependabot y `Cargo.lock` versionado.
- **Moodle de pruebas:** Moodle 4.5 local en Docker, definido en `dev/moodle/`, con configuración y datos de ejemplo automáticos.
- **CI:** con el repositorio privado, Linux en cada push y Windows en los PR hacia `main`; con el repositorio público (fase 2), ambos en cada push.

## Consecuencias

**Positivas**

- Las mismas revisiones corren en ambas máquinas y en la CI, con versiones fijadas.
- Los secretos se bloquean antes de llegar a un commit, y el token de la USM nunca se guarda en un archivo.
- El Moodle local permite probar casos que no se pueden provocar en la USM y reduce las peticiones a su gateway.
- El repositorio muestra prácticas reconocibles: hooks estándar, CI, PR, Conventional Commits y actualización automática de dependencias.

**Negativas**

- Más herramientas que instalar en cada máquina (`just`, prek, Docker Desktop y las herramientas de cargo).
- Betterleaks y prek son relativamente nuevos; si alguno falla, el formato `.pre-commit-config.yaml` permite volver a pre-commit sin cambiar la configuración.
- El Moodle local no reproduce el SSO con MFA, el gateway ni la configuración propia de la USM; esos casos se siguen validando contra la USM.
- RustRover es pesado en el ThinkPad.

## Pendiente

- Toolchain de JavaScript (P-13), `proptest`, `cargo doc` y `typos` (P-14), y el umbral y la publicación de la cobertura (P-15).
