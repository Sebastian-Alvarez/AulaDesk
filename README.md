# AulaDesk

> **Proyecto en desarrollo.** Todavía no hay una versión utilizable.

Cliente de escritorio para Moodle con login persistente, notificaciones, descargas automáticas e integración con calendarios.

## Objetivo

En muchas instancias de Moodle hay que iniciar sesión todos los días desde el computador, mientras que en la app móvil la sesión persiste. AulaDesk busca llevar esa experiencia al escritorio (Windows y Linux), conectándose a cualquier instancia de Moodle mediante su API oficial y agregando integraciones con el sistema operativo.

## Estado

Etapa de análisis y diseño inicial. La documentación está publicada en [sebastian-alvarez.github.io/AulaDesk](https://sebastian-alvarez.github.io/AulaDesk/), y su fuente está en [`docs/`](docs/):

- [Registro de decisiones](docs/registro-de-decisiones.md)
- [ADR](docs/adr/index.md)
- [Diagramas de arquitectura (C4)](https://sebastian-alvarez.github.io/AulaDesk/arquitectura/)
- [Validación de la fase 1](docs/validacion/fase-1.md)

## Documentación local

La documentación se publica con [Zensical](https://zensical.org/) en [sebastian-alvarez.github.io/AulaDesk](https://sebastian-alvarez.github.io/AulaDesk/) al integrar cada cambio en `main`.
Para revisarla en local antes de publicar se necesitan [`just`](https://just.systems/) y [`uv`](https://docs.astral.sh/uv/):

```sh
just docs-serve   # vista previa en http://localhost:8000
just docs-build   # construye el sitio en site/
```

## Desarrollo

El núcleo y la CLI están en Rust, en un workspace de Cargo (`auladesk-core` y `auladesk-cli`). Para preparar el entorno se necesitan [rustup](https://rustup.rs/), [`just`](https://just.systems/), [prek](https://prek.j178.dev/) y las herramientas de cargo indicadas en [`CONTRIBUTING.md`](CONTRIBUTING.md#requisitos). La versión de Rust está fijada en `rust-toolchain.toml` y rustup la instala al compilar por primera vez.

```sh
prek install   # activa los hooks de Git (una vez por copia del repositorio)
just check     # formato, clippy y cargo-deny
just test      # tests
```

Para probar contra la API hay un Moodle de pruebas local en Docker (`just moodle-up`); los detalles están en [`CONTRIBUTING.md`](CONTRIBUTING.md#moodle-de-pruebas).

## Contribuir

Cómo reportar problemas y el flujo de trabajo con Git están en [`CONTRIBUTING.md`](CONTRIBUTING.md). Las vulnerabilidades se reportan en privado, según la [política de seguridad](SECURITY.md).

## Licencia

Distribuido bajo la licencia [Apache 2.0](LICENSE).

## Aviso

AulaDesk es un proyecto independiente. No está afiliado, asociado ni respaldado por Moodle Pty Ltd ni por ninguna institución educativa. Moodle™ es una marca registrada de Moodle Pty Ltd.
