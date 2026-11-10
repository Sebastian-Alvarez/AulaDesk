---
status: aceptado
date: 2026-11-06
decisions: D-08, D-09
---

# Tecnología

## Contexto y problema

AulaDesk es una app de escritorio para Windows y Linux (D-03), liviana (RNF-01), residente en la bandeja (D-05) y con un núcleo separado como librería (D-07). Hay que elegir el lenguaje y el framework de escritorio, y el framework web de la interfaz.

Las decisiones anteriores fijan los criterios:

1. Bandeja, proceso único y arranque con el sistema (D-05).
2. Deep link `moodlemobile://` con instancia única, en Windows y Linux (D-13).
3. WebView del sitio real aislado del backend, con inyección de CSS (D-17).
4. Notificaciones nativas, sin impedir acciones en ellas más adelante (D-18, P-06).
5. No impedir macOS (D-03, P-07).
6. Liviano y con valor para el portafolio (RNF-01, RNF-04).

## Factores de decisión

- Cobertura de los criterios con plugins oficiales, sin desarrollos propios.
- Madurez y tiempo en producción del framework.
- Consumo de recursos en reposo, porque la app vive en la bandeja.
- Valor como portafolio y aprendizaje: el proyecto sirve también para aprender Rust.
- Curva de aprendizaje para una sola persona.

## Opciones consideradas

### Lenguaje y framework de escritorio

| Opción | A favor | En contra |
|---|---|---|
| Rust + Tauri 2 | Usa el WebView del sistema; plugins oficiales para bandeja, `autostart`, `deep-link`, `single-instance` y notificaciones; el que más tiempo lleva estable entre los livianos; Rust suma al portafolio | Curva de aprendizaje alta (*ownership*, *lifetimes*, async) |
| Go + Wails v3 | Casi igual de liviano y mucho más fácil de aprender; trae acciones en notificaciones integradas | Estable desde el 2026-10-26, con mucho menos recorrido en producción |
| C# (NativeAOT) + Avalonia | Lenguaje ya conocido | Más pesado; integrar un WebView en Linux es más complicado |
| Electron | Ecosistema enorme; un solo lenguaje | Pesado en disco y en RAM para una app que vive en la bandeja |
| Flutter | El mismo código para escritorio y móvil | Integración de escritorio menos madura y WebView más limitado |
| Fork de `moodleapp` (Ionic/Angular) | La interfaz de alumno, la autenticación y el cliente de la API ya hechos | Pesado; su versión de escritorio está abandonada; difícil agregar integraciones profundas con el sistema operativo |
| Nativo por sistema operativo | La integración más profunda con cada sistema | Una app distinta por plataforma |

### Framework de la interfaz

| Opción | A favor | En contra |
|---|---|---|
| React | El mayor ecosistema de componentes (calendario, tablas, listas) y el mayor mercado laboral | Más pesado y con más código repetitivo que Svelte |
| Vue | Curva baja; plantillas HTML familiares | Ecosistema y mercado menores que React |
| Svelte | El más liviano y con menos código repetitivo; reacciona bien a eventos del backend | Ecosistema de componentes y mercado medianos |
| Solid, Preact, Lit | Muy livianos | Pocos componentes propios y poco peso en el mercado |
| Angular | Framework completo, común en empresas | Pesado y con curva alta |
| TypeScript sin framework | Sin dependencias | Todo a mano; se vuelve difícil de mantener al crecer |
| Leptos, Yew o Dioxus (Rust con WebAssembly) | Todo en un solo lenguaje | Ecosistemas pequeños y curva alta; atan la interfaz a Tauri |

## Resultado de la decisión

- **Tecnología (D-08):** Rust + Tauri 2.
- **Interfaz (D-09):** React + TypeScript. TypeScript en lugar de JavaScript, porque los tipos detectan errores antes de ejecutar.

Tauri 2 cubre todos los criterios:

| Criterio | Cobertura en Tauri 2 |
|---|---|
| Bandeja, proceso único, arranque con el sistema | Bandeja en el núcleo; plugin oficial `autostart` |
| Deep link con instancia única | Plugins oficiales `deep-link` y `single-instance` |
| WebView del sitio real aislado | Sin acceso al backend por defecto; filtro de navegación (`on_navigation`) |
| Inyección de CSS en el sitio real | Script de inicialización con validación del origen |
| Notificaciones nativas | Plugin oficial; en Windows requiere la app instalada |
| Acciones en notificaciones (futuro) | Plugin de la comunidad `tauri-plugin-notifications` o crate `notify-rust`, detrás del puerto de notificaciones |
| Interfaz en React + TypeScript | Plantilla oficial |
| Compatibilidad futura con macOS | Soportada |

Inicialmente, Tauri tenía una ventaja clara: su WebView permitía interceptar la navegación para capturar el token del login. Cuando el login pasó al navegador del sistema (D-13), esa ventaja desapareció, y Wails v3 llegó a su versión estable, por lo que Go + Wails v3 también cubre todos los criterios. Se eligió Tauri 2 porque lleva más tiempo en producción, es la combinación más probada para estos requisitos y Rust suma más al portafolio.

Entre las tres opciones razonables para la interfaz (React, Vue y Svelte), se eligió React, la de mayor ecosistema y mercado laboral. React funciona igual con Tauri y con Wails, así que no condicionó la elección del framework de escritorio.

## Consecuencias

**Positivas**

- Los criterios se cubren con plugins oficiales; solo las acciones en notificaciones dependen de una librería externa, aislada detrás de un puerto (D-06).
- La app usa el WebView del sistema, sin incluir un navegador propio.
- El núcleo en Rust es el mismo para la CLI y la app (D-07), y no impide llevar la app a macOS.
- React ofrece componentes listos para el calendario, las tablas y las listas de la vista propia (D-16).

**Negativas**

- La curva de Rust es alta. En la fase 1 se mitiga con una CLI simple antes de sumar la interfaz.
- El WebView es distinto en cada sistema (WebView2 en Windows, WebKitGTK en Linux): el sitio real y el SSO pueden comportarse distinto, así que hay que probar en ambos (D-27).
- En Linux, la bandeja requiere `libayatana-appindicator` y el keyring un servicio de secretos activo (GNOME Keyring o KWallet).
- Las notificaciones en Windows solo muestran el nombre y el ícono de la app cuando está instalada; en desarrollo aparecen como PowerShell.
- La fase 2 suma Node y su toolchain (P-13).
- El logo de Tauri no se puede usar en la identidad de la app (sección 7.3 del registro).
