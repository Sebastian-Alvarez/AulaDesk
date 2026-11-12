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

## Contribuir

Cómo reportar problemas y el flujo de trabajo con Git están en [`CONTRIBUTING.md`](CONTRIBUTING.md). Las vulnerabilidades se reportan en privado, según la [política de seguridad](SECURITY.md).

## Licencia

Distribuido bajo la licencia [Apache 2.0](LICENSE).

## Aviso

AulaDesk es un proyecto independiente. No está afiliado, asociado ni respaldado por Moodle Pty Ltd ni por ninguna institución educativa. Moodle™ es una marca registrada de Moodle Pty Ltd.
