# Política de seguridad

## Versiones con soporte

AulaDesk está en desarrollo y todavía no tiene versiones publicadas. Las correcciones de seguridad se hacen solo sobre `main`.

## Reportar una vulnerabilidad

Esta política cubre la seguridad de AulaDesk; revisa el [alcance](#alcance) antes de reportar.

**No abras un issue público.** Repórtala en privado desde la pestaña **Security** del repositorio, con [**Report a vulnerability**](https://github.com/Sebastian-Alvarez/AulaDesk/security/advisories/new). Solo el mantenedor y tú pueden ver el reporte.

Incluye:

- Descripción de la vulnerabilidad y su impacto.
- Pasos para reproducirla.
- Versión o commit de AulaDesk, sistema operativo y, si la falla depende de ella, la instancia de Moodle donde ocurre.

**Nunca incluyas tokens, `privatetoken`, cookies ni contraseñas reales**, aunque sean tuyos. Si hace falta mostrar un valor, reemplázalo por uno ficticio.

## Qué esperar

- AulaDesk lo mantiene una sola persona, así que la respuesta puede tardar varios días.
- Una vez confirmada la vulnerabilidad, la corrección se prepara en privado.
- Al publicar la corrección se publica también el aviso de seguridad, con crédito a quien la reportó, si lo desea.

## Alcance

| Incluido | Fuera de alcance |
|---|---|
| El código de este repositorio | Vulnerabilidades de Moodle, de una institución o de dependencias que no afectan a AulaDesk: repórtalas solo al proyecto o la institución correspondiente |
| El manejo de tokens, credenciales y datos locales por parte de AulaDesk | Errores que no son de seguridad: ábrelos como [issue](https://github.com/Sebastian-Alvarez/AulaDesk/issues), según [cómo reportar un problema](CONTRIBUTING.md#reportar-un-problema) |
| Vulnerabilidades de Moodle, de la configuración de una institución, de dependencias, bibliotecas o plugins que afectan a quien usa AulaDesk | |

Si la vulnerabilidad es de Moodle, de una institución o de una dependencia y afecta a AulaDesk, repórtala aquí para mitigarla en la app y también por el canal de seguridad de quien puede corregirla. Mientras no se corrija en su origen, el aviso de AulaDesk no la hace pública.

## Si tu token quedó expuesto

El token de Moodle sigue siendo válido aunque cierres sesión en AulaDesk. Para revocarlo, reinicia la clave de seguridad en Moodle: **Preferencias → Claves de seguridad**.
