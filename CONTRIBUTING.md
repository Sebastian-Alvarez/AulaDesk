# Cómo contribuir

AulaDesk está en la etapa de análisis y diseño inicial. Esta guía describe cómo reportar problemas y el flujo de trabajo con Git, definido en la decisión D-30 del [registro de decisiones](docs/registro-de-decisiones.md).

## Reportar un problema

Antes de abrir un [issue](https://github.com/Sebastian-Alvarez/AulaDesk/issues), revisa si ya existe uno sobre lo mismo.

Incluye:

- Versión o commit de AulaDesk y sistema operativo (Windows o Linux, con su versión).
- Instancia de Moodle y su versión, si la conoces.
- Pasos para reproducir el problema, qué esperabas y qué pasó.
- Mensajes de error o logs relevantes.

**Nunca incluyas tokens, `privatetoken`, cookies, contraseñas ni capturas con datos de tu sesión.** Revisa los logs antes de pegarlos.

Si encuentras una vulnerabilidad, no abras un issue público: sigue la [política de seguridad](SECURITY.md).

## Requisitos

- [Git](https://git-scm.com/) y, opcionalmente, [GitHub CLI](https://cli.github.com/) (`gh`)
- [`just`](https://just.systems/), el punto de entrada de todas las tareas (`just` sin argumentos las lista)
- [rustup](https://rustup.rs/). La versión de Rust está fijada en `rust-toolchain.toml` y se instala sola al compilar por primera vez.
- [prek](https://prek.j178.dev/), que ejecuta los hooks de Git
- Herramientas de cargo: [`cargo-deny`](https://github.com/EmbarkStudios/cargo-deny), [`cargo-nextest`](https://nexte.st/), [`cargo-llvm-cov`](https://github.com/taiki-e/cargo-llvm-cov) y [`cargo-machete`](https://github.com/bnjbvr/cargo-machete)
- [`uv`](https://docs.astral.sh/uv/), para construir la documentación
- [Docker](https://docs.docker.com/get-started/get-docker/), para los diagramas C4 y el Moodle de pruebas

Las versiones que usa la CI están en [`.github/workflows/rust.yml`](.github/workflows/rust.yml).

## Obtener el código

Haz un fork y clónalo:

```sh
gh repo fork Sebastian-Alvarez/AulaDesk --clone
cd AulaDesk
```

`gh repo fork --clone` deja tu fork como `origin` y el repositorio original como `upstream`.

Sin `gh`, haz el fork desde GitHub, clónalo y agrega el remoto a mano:

```sh
git remote add upstream https://github.com/Sebastian-Alvarez/AulaDesk.git
```

Si tienes permiso de escritura en el repositorio, clónalo directamente y usa `origin` donde esta guía dice `upstream`.

Después de clonar, activa los hooks de Git:

```sh
prek install
```

| Momento | Revisión |
|---|---|
| Commit | Secretos con Betterleaks y formato con `rustfmt` |
| Mensaje del commit | Conventional Commits, con `committed` |
| Push | `clippy` y tests |

La primera ejecución tarda unos minutos, porque prek compila Betterleaks y `committed`.

## Moodle de pruebas

Para desarrollar y probar contra la API sin usar una cuenta real, el repositorio incluye un Moodle 4.5 local en Docker ([`dev/moodle/`](dev/moodle/compose.yaml)). La primera vez descarga Moodle, lo instala y crea los datos de ejemplo; tarda unos minutos.

```sh
just moodle-up      # levanta el Moodle en http://localhost:8090
just moodle-token   # muestra el token del estudiante de prueba
just moodle-down    # lo detiene y conserva los datos
just moodle-reset   # lo borra; el próximo moodle-up lo instala de nuevo
```

| Usuario | Contraseña | Uso |
|---|---|---|
| `estudiante` | `Estudiante-1234` | Estudiante inscrito en HIS204 y MAT021, con notas, tareas y eventos |
| `admin` | `Admin-1234!` | Administración del sitio |

Son credenciales de prueba, solo para este Moodle local. Nunca uses aquí, ni guardes en archivos, el token o la contraseña de una cuenta real.

## Flujo de trabajo

El proyecto usa GitHub Flow: `main` es la única rama permanente y todo cambio llega a ella mediante un PR.

### 1. Crear una rama

Siempre desde la última versión de `main`:

```sh
git fetch upstream
git switch -c feat/course-sync upstream/main
```

El nombre sigue el formato `tipo/descripcion-corta`, en inglés y en minúsculas con guiones:

| Prefijo | Uso |
|---|---|
| `feat/` | Nueva funcionalidad |
| `fix/` | Corrección de un error |
| `docs/` | Documentación |
| `refactor/` | Cambio interno sin cambiar el comportamiento |
| `test/` | Tests |
| `ci/` | Workflows de GitHub Actions |
| `chore/` | Mantenimiento: dependencias, configuración, herramientas |
| `spike/` | Experimento que puede descartarse sin integrarse |

Una rama corresponde a un solo cambio. Si aparece otro, va en una rama aparte.

### 2. Hacer commits

Los mensajes siguen [Conventional Commits](https://www.conventionalcommits.org/es/v1.0.0/), en inglés:

```sh
git commit -m "feat: sync course list from the API"
```

Los commits dentro de la rama pueden ser pequeños o provisionales: al integrar el PR se combinan en uno solo.

### 3. Revisar antes del PR

```sh
just check            # formato, clippy y cargo-deny
just test             # tests
just docs-build       # el sitio se construye sin advertencias
just diagrams-check   # el modelo C4 es válido (si cambiaste arquitectura/)
```

Un PR tiene más probabilidades de aceptarse si:

- Contiene un solo cambio y es lo más pequeño posible.
- Incluye tests para el código nuevo o corregido.
- Actualiza la documentación afectada. Si cambia una decisión, actualiza también el [registro de decisiones](docs/registro-de-decisiones.md) o el [ADR](docs/adr/index.md) correspondiente.
- Tiene un título en Conventional Commits.
- No incluye secretos ni datos personales.

### 4. Subir la rama y abrir el PR

Antes, trae lo último de `main` a tu rama (si no avanzó, no cambia nada):

```sh
git fetch upstream
git rebase upstream/main
```

Luego sube la rama y abre el PR:

```sh
git push -u origin feat/course-sync
gh pr create --fill
```

- El título del PR debe seguir Conventional Commits, porque se convierte en el mensaje del commit en `main`.
- Si el cambio no está terminado, ábrelo como borrador (`gh pr create --draft`) para que la CI corra desde el principio.
- Subir la rama también sirve para continuar el trabajo desde otro computador. Allá, antes de seguir, trae lo último con `git pull` en la rama.
- Si ya habías subido la rama antes del `rebase`, súbela con `git push --force-with-lease`.
- Si `main` avanza después de abrir el PR, la regla de `main` exige actualizar la rama antes de integrar: repite el `rebase` y súbela con `git push --force-with-lease`, o usa el botón **Update branch** del PR.


### 5. Integrar

Cuando la CI está en verde, el PR se integra con **Squash and merge**. La rama remota se borra automáticamente; la local se borra con:

```sh
git switch main
git pull upstream main
git branch -D feat/course-sync
```

Se usa `-D` porque, con squash merge, Git no reconoce la rama como integrada.

## Versiones

Las versiones se marcan con tags SemVer anotados sobre `main` (`v0.1.0`, `v0.2.0`, …). Mientras la API no sea estable, se usa `0.x`.

## Documentación

```sh
just docs-serve   # vista previa en http://localhost:8000
just docs-build   # construye el sitio y falla ante cualquier advertencia
```

Las decisiones nuevas o los cambios a una existente se registran en el [registro de decisiones](docs/registro-de-decisiones.md), y las decisiones con alternativas que vale la pena explicar, en un [ADR](docs/adr/index.md).
