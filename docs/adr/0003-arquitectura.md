---
status: aceptado
date: 2026-11-06
decisions: D-05, D-06, D-07
---

# Arquitectura

## Contexto y problema

AulaDesk sincroniza con Moodle en segundo plano y, cuando detecta un cambio, debe reaccionar de varias formas: notificar, descargar material, actualizar el calendario y exportar calificaciones (RF-02 a RF-05). Varias de esas salidas ya tienen variantes o extensiones previstas: tres modos de notificación (D-18), dos modos de calendario (D-19), acciones en las notificaciones (P-06), integración con otras herramientas (P-10) y foros y entregas nativos (P-09). Además, el mismo núcleo debe servir a dos interfaces: una CLI en la fase 1 y la app de escritorio en la fase 2.

Hay que decidir tres cosas: cuántos procesos tiene la app, cómo se organiza el núcleo y cómo se comunican sus partes, y cómo se separa el núcleo de las interfaces.

## Factores de decisión

- Seguridad: menor superficie de ataque y menos lugares por donde viajan datos sensibles.
- Simplicidad de instalación y actualización en Windows y Linux.
- Extensibilidad: agregar un modo o una integración sin tocar el núcleo.
- Resiliencia: un fallo en una integración (por ejemplo, Google Calendar) no debe detener las demás.
- Testeo del núcleo sin Moodle real y sin interfaz.
- Validar primero lo más incierto: qué permite la API de la USM.
- Liviano en reposo (RNF-01) y valor como portafolio (RNF-04).

## Opciones consideradas

### Modelo de proceso

| Opción | A favor | En contra |
|---|---|---|
| Un solo proceso residente en la bandeja | Un instalador y un binario; estado compartido en memoria, sin IPC; al cerrar la ventana se libera el WebView y queda solo el backend | La sincronización se detiene al elegir "Salir"; si la interfaz falla, cae todo |
| Servicio de sincronización separado, con IPC | Sincroniza aunque la app esté cerrada; varias interfaces conectadas a la vez; podría correr en otra máquina | Dos binarios cuyas versiones deben coincidir, un protocolo IPC y el registro del servicio en cada sistema operativo. El canal IPC es una entrada más: cualquier programa del usuario o, si es HTTP en `localhost`, una página web podría usarlo. El servicio podría correr con más privilegios de los necesarios |

La ventaja de aislamiento del servicio separado se obtiene de otra forma en un solo proceso: los WebViews ejecutan el contenido web en procesos propios, separados del backend, y el WebView del sitio real no tiene permisos hacia el backend (D-17). En reposo, el consumo de ambas opciones es similar, así que no fue un factor decisivo.

### Estilo de arquitectura

| Opción | A favor | En contra |
|---|---|---|
| Hexagonal con bus de eventos | Un modo o una integración nueva no toca el núcleo; los puertos se simulan en los tests; calza con reglas del tipo "evento → acción" | Más abstracciones (*traits*) y más archivos; el flujo es menos visible y más difícil de depurar; en Rust, los *traits* con async suman complejidad |
| Hexagonal con implementación mínima | Puertos solo para la API, el almacenamiento, las notificaciones y el calendario; eventos como un `enum` con una lista de suscriptores | Lo externo queda tratado de forma desigual: parte con puerto y parte sin él |
| Monolito modular con observer simple | Casi los mismos beneficios con mucho menos código; flujo fácil de seguir mientras se aprende el lenguaje | Los límites dependen de respetar una convención; si crecen las integraciones, hay que refactorizar hacia la hexagonal |
| Capas (presentación, negocio, datos) | La más simple y conocida | Las integraciones tienden a mezclarse con la lógica |
| Clean / Onion | Organización formal por casos de uso | Muy cercana a la hexagonal, con más ceremonia |
| *Vertical slices* | Cada función queda autocontenida | Todas comparten el cliente de la API y la sincronización, así que se duplicaría código |
| Orquestación directa, sin eventos | Flujo lineal y simple de depurar | Cada integración nueva obliga a modificar el orquestador |

### Estructura del núcleo

| Opción | A favor | En contra |
|---|---|---|
| Librería, con una CLI en la fase 1 y la app en la fase 2 | Valida la API de la USM sin construir interfaz; el trabajo de la fase 1 es el mismo que usa la app; la librería no sabe que existe una ventana, así que la lógica no puede mezclarse con la interfaz; se prueba sin levantar la app | Hay que diseñar la API pública de la librería; la CLI puede quedar sin uso después |
| Librería, directo a la app | Mantiene la separación sin el programa extra | Validar la API requiere construir la app primero |
| Todo dentro de la app | Arranque más rápido, un solo paquete | La lógica tiende a mezclarse con la interfaz, y separarla después cuesta más |

## Resultado de la decisión

- **Modelo de proceso (D-05):** una sola aplicación residente en la bandeja del sistema. Cerrar la ventana no detiene la sincronización ni las notificaciones, y "Salir" cierra la app por completo, igual que en Discord o Spotify. Se eligió sobre el servicio separado por simplicidad y seguridad: no hay un canal IPC que proteger, y no se necesita sincronizar sin la app ni desde otra máquina.
- **Estilo (D-06):** hexagonal con bus de eventos. El núcleo define puertos para todo lo externo (API de Moodle, almacenamiento, notificaciones, archivos, calendario y exportación), y los componentes se comunican con eventos tipados, como `grade_published` o `new_material`. Cada suscriptor del bus está aislado: si uno falla, el error se registra y los demás continúan.
- **Estructura (D-07):** el núcleo (cliente de la API, sincronización, base de datos y reglas) es la librería `auladesk-core`. La fase 1 es una CLI sobre ella (`auladesk-cli`) y la fase 2, la app de escritorio (`auladesk-desktop`).

Para el estilo, la recomendación inicial fue el monolito modular con observer simple, por la curva de aprendizaje de un proyecto de una sola persona en un lenguaje nuevo. Para evitar sesgo, la misma descripción del problema, sin esas opciones, se sometió a una evaluación independiente con otro asistente, que recomendó hexagonal con bus de eventos. Se adoptó esa variante completa.

## Consecuencias

**Positivas**

- Un solo instalador, sin servicios del sistema que registrar ni versiones que coordinar entre procesos.
- Las variantes e integraciones previstas (P-06, P-09, P-10) se agregan como adaptadores o suscriptores, sin tocar el núcleo. Por ejemplo, cambiar la librería de notificaciones solo cambia su adaptador (D-08).
- El núcleo se prueba con Moodle simulado (`mockito`, D-28) y con el Moodle de pruebas (D-31), sin interfaz.
- La fase 1 valida la API de la USM antes de diseñar pantallas ([informe de validación](../validacion/fase-1.md)).
- Otra interfaz, o un servicio separado si algún día se necesita, se construye sobre la misma librería sin reescribirla.

**Negativas**

- Al elegir "Salir", la sincronización se detiene hasta el próximo inicio. Si la interfaz falla, también cae la sincronización.
- Más abstracciones (*traits* y async) en un lenguaje que se está aprendiendo, y un flujo por eventos más difícil de depurar.
- La resiliencia no la da el bus por sí mismo: depende de que cada suscriptor se ejecute aislado y registre su error sin propagarlo. Es un requisito de la implementación.
- Al cerrar la ventana hay que destruirla, no solo ocultarla. Si se oculta, el WebView sigue en memoria y la app pesa bastante más en reposo.

## Pendiente

- Local-first: si la base local es la fuente de verdad de la interfaz (P-01).
- Diagrama de secuencia sincronización → notificación (P-05), para comprobar que la arquitectura encaja en el flujo central.
