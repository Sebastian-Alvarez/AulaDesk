---
status: aceptado
date: 2026-11-06
decisions: D-16, D-17
---

# Vista de cursos y sitio real

## Contexto y problema

La organización de los cursos en la USM es poco práctica, con secciones y pestañas que casi no se usan, y es uno de los problemas que motivaron el proyecto. AulaDesk debe ofrecer una vista personalizada de los cursos (RF-07) y, a la vez, dar acceso al sitio real para lo que la app no hace: cuestionarios, foros y entregas (RF-08).

Hay que decidir qué vista de los cursos se muestra por defecto y dónde se abre el sitio real.

## Factores de decisión

- Control sobre cómo se presenta el contenido, independiente del tema de cada institución.
- Experiencia integrada: que el uso diario no obligue a saltar entre aplicaciones.
- Seguridad: el contenido remoto no debe tener acceso al backend ni a los tokens.
- Sesión del sitio real: entrar ya autenticado, sin login diario (D-15).
- No anticipar: los foros y las entregas nativos quedan fuera de la fase 1.

## Opciones consideradas

### Vista de cursos

| Opción | A favor | En contra |
|---|---|---|
| Vista propia por defecto, con opción de vista normal | Ordena el contenido igual en cualquier instancia; la vista normal sigue disponible cuando hace falta | Hay que construirla con la API |
| Solo la vista normal del sitio | Sin trabajo extra | Mantiene el desorden que motivó el proyecto |
| Solo la vista propia | Una sola forma de ver los cursos | Sin acceso al formato original cuando la vista propia no muestra algo |

### Dónde se abre el sitio real

| Opción | A favor | En contra |
|---|---|---|
| WebView aislado dentro de la app | Todo ocurre dentro de la app; la presentación se puede ajustar inyectando CSS | El WebView tiene una sesión propia y depende del autologin; requiere una ventana aislada y una CSP estricta; el CSS depende del tema de cada institución |
| Navegador del sistema | Usa la sesión del navegador, sin el riesgo del SSO en WebKitGTK; la app no carga contenido remoto | Cada enlace salta al navegador; personalizar el sitio requiere una extensión aparte en cada navegador |
| `<iframe>` en la interfaz de la app | Integrado en la misma vista que el resto de la app | La política `frame-ancestors` del aula solo permite `localhost` y los esquemas de la app oficial: funciona en desarrollo y falla en la app instalada |
| Foros y entregas nativos, sin sitio real | Experiencia uniforme | Mucho trabajo antes de validar la API; no cubre los cuestionarios |

En las dos primeras opciones la app tiene su propia ventana, vive en la bandeja y construye la vista propia con la API. La diferencia es solo qué ocurre al abrir el sitio real.

## Resultado de la decisión

- **Vista de cursos (D-16):** la vista propia, construida con la API, es la vista por defecto, con la opción de ver el curso en su formato normal. La vista normal se abre en el mismo WebView del sitio real.
- **Sitio real (D-17):** se abre dentro de la app, en un WebView aislado, como las apps de escritorio que muestran su propio contenido en su ventana (por ejemplo, Discord o Spotify). Los cuestionarios, foros y entregas se realizan ahí.

El WebView va en una ventana o webview nativa de Tauri, no en un `<iframe>`, por la política `frame-ancestors` que encontró la validación de la fase 1 ([informe](../validacion/fase-1.md)). Los foros y entregas nativos quedan pospuestos (P-09).

## Consecuencias

**Positivas**

- La vista propia compacta u oculta las etiquetas (`label`) y las secciones vacías, la principal fuente del desorden: en la USM, 164 de 303 actividades son etiquetas.
- El uso diario ocurre en una sola aplicación, incluidos los cuestionarios, foros y entregas.
- El sitio real no tiene permisos hacia el backend: Tauri no le da acceso por defecto, las navegaciones se filtran con `on_navigation` y el script de CSS valida el origen antes de ejecutarse (D-08).

**Negativas**

- El WebView tiene su propia sesión: entra autenticado solo con el autologin (D-15). Sin él, hay que iniciar sesión dentro de la app, y en Linux (WebKitGTK) algunos SSO pueden comportarse distinto. Esto pesa al decidir si el autologin viene activado por defecto (P-03).
- La personalización del sitio real con CSS depende del tema de cada institución y puede romperse si cambia. Por eso la vista propia es la vista por defecto.
- Cargar contenido remoto exige mantener el aislamiento del WebView (7.2).
