# Login

Flujo de login de AulaDesk ([D-13 a D-15](../registro-de-decisiones.md#55-login-y-sesión)). El usuario se autentica en el navegador del sistema, así que AulaDesk nunca ve la contraseña ni el segundo factor, y el token llega a la app por deep link.

```mermaid
sequenceDiagram
    autonumber
    actor U as Usuario
    participant A as AulaDesk
    participant N as Navegador del sistema
    participant S as Sistema operativo
    participant M as Moodle
    participant K as Keyring del SO

    U->>A: Agrega URL del sitio
    A->>M: tool_mobile_get_public_config
    M-->>A: Tipo de login y launchurl
    A->>A: Genera passport aleatorio y lo guarda como pendiente
    A->>N: Abre launch.php?service=moodle_mobile_app&passport=X&urlscheme=moodlemobile
    N->>M: GET launch.php
    opt Sin sesión activa en el navegador
        M-->>N: Página de login real (SSO / 2FA)
        U->>N: Credenciales + segundo factor
        N->>M: Envía login
    end
    M-->>N: Redirect a moodlemobile://token=BASE64
    N->>U: ¿Abrir AulaDesk?
    U->>N: Acepta
    N->>S: Solicita abrir esquema moodlemobile://
    S->>A: Entrega el deep link a la instancia abierta
    A->>A: Decodifica firma, token y privatetoken
    A->>A: Verifica firma = md5(wwwroot + passport)
    alt Firma inválida o passport no pendiente
        A-->>U: Error, se descarta el token
    else Firma válida
        A->>K: Guarda token y privatetoken cifrados
        A->>M: core_webservice_get_site_info
        M-->>A: Usuario y funciones API habilitadas
        A-->>U: Cuenta lista, sesión persistente
    end
```

## Notas

- **Login opcional (pasos 7 a 9):** si el navegador ya tiene una sesión activa en Moodle, el redirect llega sin pedir credenciales.
- **Instancia única (paso 14):** si AulaDesk ya está abierta, el sistema operativo entrega el deep link a esa instancia en lugar de iniciar otra.
- **Firma (paso 16):** `wwwroot` es la URL del sitio sin `/` final. La firma y el passport pendiente impiden aceptar un enlace `moodlemobile://` que no provenga de un login iniciado por AulaDesk.
- **`privatetoken` (paso 18):** se guarda desde la fase 1 porque permite pedir las claves de autologin para abrir el sitio real ya autenticado ([D-15](../registro-de-decisiones.md#55-login-y-sesión)).
