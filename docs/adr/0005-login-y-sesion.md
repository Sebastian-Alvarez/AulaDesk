---
status: aceptado
date: 2026-11-06
decisions: D-13, D-14, D-15
---

# Login y sesión

## Contexto y problema

En el aula virtual de la USM hay que iniciar sesión todos los días, mientras que en la app oficial de Android se inicia sesión una vez. La app oficial obtiene un token de la API mediante `admin/tool/mobile/launch.php`: tras el login, Moodle redirige a `moodlemobile://token=...` con el token codificado. MoodleConector, un toolkit propio en Python, ya demostró que ese flujo funciona con el SSO y la MFA de la USM y que el token persiste sin login diario.

Hay que decidir dónde se autentica el usuario, cómo llega el token a la app y cómo se verifica, dónde se guarda, y cómo se abre el sitio real ya autenticado (D-17), cuya sesión web la USM hace expirar.

## Factores de decisión

- La app nunca recibe la contraseña ni el segundo factor.
- Compatible con SSO y MFA en cualquier instancia (RF-01, RNF-03).
- Sesión persistente: volver a pedir login solo cuando Moodle rechace el token.
- Rechazar cualquier token que no provenga de un login iniciado por la app.
- Secretos fuera de archivos, bases de datos y logs (7.2).
- Comportamiento igual en Windows y Linux.

## Opciones consideradas

### Dónde se hace el login

| Opción | A favor | En contra |
|---|---|---|
| Navegador del sistema, con deep link | La app no ve las credenciales; aprovecha la sesión activa, el gestor de contraseñas y las passkeys del navegador; el SSO no depende del WebView de la app | Hay que registrar el esquema `moodlemobile://` (en Linux, con la app instalada) y manejar la instancia única; la primera vez el navegador pregunta si abrir la app; otra app que registre el mismo esquema podría recibir el enlace |
| WebView embebido que intercepta la redirección | No depende de registrar el esquema; todo ocurre en la app | La página de login corre dentro de la app; en Linux (WebKitGTK) algunos SSO pueden comportarse distinto; no aprovecha la sesión ni las credenciales guardadas del navegador |
| Navegador automatizado con Playwright (MoodleConector) | Ya probado con el SSO y la MFA de la USM | Depende de un navegador controlado por la herramienta, que hay que descargar aparte; excesivo para una app distribuible |
| `login/token.php` con usuario y contraseña | Lo más simple, sin navegador | La app recibe la contraseña; no funciona con SSO ni MFA |

### Dónde se guarda el token

| Opción | A favor | En contra |
|---|---|---|
| Keyring del sistema operativo | El sistema cifra y protege el secreto; sin contraseña maestra | En Linux requiere un servicio de secretos activo |
| Archivo cifrado (MoodleConector) | No depende del sistema operativo | Requiere una contraseña o guardar la clave de cifrado en otro lugar |

### Autologin en el sitio real

| Opción | A favor | En contra |
|---|---|---|
| Implementarlo como opción por instancia | El sitio real se abre autenticado, sin login diario en la web | Depende de que cada instancia lo permita |
| Solo guardar el `privatetoken`, para implementarlo después | Menos trabajo ahora, sin volver a pedir login más adelante | El WebView del sitio real pediría login mientras tanto |
| Dejarlo fuera | Lo más simple | No resuelve el login diario en el sitio real |

## Resultado de la decisión

- **Login (D-13):** en el navegador del sistema, mediante `launch.php`. El token llega por deep link (`moodlemobile://`), con instancia única. Cada login genera un passport aleatorio y la app verifica la firma `md5(wwwroot + passport)`; un enlace con una firma inválida o un passport que no esté pendiente se descarta. El flujo completo está en el [diagrama de secuencia del login](../diagramas/login.md).
- **Sesión (D-14):** el token persiste, se valida antes de usarse y solo se vuelve a pedir login si Moodle lo rechaza. Es el mecanismo de MoodleConector, con tres cambios: navegador del sistema en lugar de Playwright, verificación de la firma y keyring en lugar de un archivo cifrado.
- **Autologin (D-15):** opción por instancia, mediante `tool_mobile_get_autologin_key`, con alternativa automática al enlace normal si el sitio no lo soporta. El `privatetoken` se guarda desde la fase 1.

El WebView embebido queda pospuesto como respaldo del login para los casos en que el esquema no se pueda registrar (P-08). Si el autologin viene activado por defecto se decide en el diseño detallado (P-03).

## Consecuencias

**Positivas**

- La app nunca recibe la contraseña ni el segundo factor, y aprovecha lo que el usuario ya tiene en su navegador.
- El usuario inicia sesión una vez; todo lo que la app hace por la API funciona con el token guardado.
- Un enlace `moodlemobile://` que no corresponda a un login iniciado por la app se descarta.
- La validación de la fase 1 lo confirmó en la USM: el login funcionó con el SSO de Microsoft y MFA desde el navegador del sistema, y el autologin abrió el aula con la sesión iniciada ([informe](../validacion/fase-1.md)).

**Negativas**

- Si el esquema no se registra (por ejemplo, con un AppImage sin instalar), el login no funciona. Hay que distribuir paquetes instalables.
- El login depende de que cada instancia acepte `launch.php` con el navegador externo. La USM tiene configurado el navegador embebido (`typeoflogin = 3`) para la app oficial; el navegador externo funciona igual, pero si eso cambia habría que activar el respaldo (P-08).
- Moodle reutiliza el token y el `privatetoken` por usuario y servicio, así que cerrar sesión en la app no los revoca. La app debe indicar cómo reiniciar la clave de seguridad (7.2).
- El autologin exige un User-Agent con `MoodleMobile` y, en la USM, permite una clave cada 6 minutos: el WebView debe conservar la sesión tras el primer autologin.
- Sin autologin, el WebView del sitio real tiene su propia sesión y pide login dentro de la app, con el riesgo del SSO en WebKitGTK (ADR 0006).
