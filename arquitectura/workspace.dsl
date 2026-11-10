workspace "AulaDesk" "Modelo C4 de AulaDesk: contexto (nivel 1) y contenedores (nivel 2)." {

    !identifiers hierarchical

    model {
        estudiante = person "Estudiante" "Estudiante con acceso a un aula virtual Moodle."

        auladesk = softwareSystem "AulaDesk" "App de escritorio que sincroniza el aula virtual y avisa novedades." {
            app = container "App de escritorio" "Reside en la bandeja, sincroniza en segundo plano y muestra la interfaz y el sitio real en un WebView aislado." "Rust + Tauri 2 + React/TypeScript"
            db = container "Base de datos local" "Estado de Moodle, sincronización, historial y datos del usuario." "SQLite" {
                tags "Base de datos"
            }
            carpeta = container "Carpeta local" "Material de los cursos ordenado por ramo." "Sistema de archivos" {
                tags "Carpeta"
            }
        }

        navegador = softwareSystem "Navegador web" "Chrome, Firefox, Edge u otro del sistema." {
            tags "Externo" "Navegador"
        }
        moodle = softwareSystem "Moodle" "Aula virtual con cursos, calificaciones y entregas; expone la API Web Services." {
            tags "Externo"
        }
        so = softwareSystem "Sistema operativo" "Notificaciones, keyring (almacén seguro del token) y deep links." {
            tags "Externo"
        }
        calendario = softwareSystem "Servicio de calendario" "Google Calendar, Outlook u otro compatible." {
            tags "Externo"
        }
        productividad = softwareSystem "Herramientas de productividad" "Excel u otras apps donde se exportan las calificaciones." {
            tags "Externo"
        }

        # Relación explícita del nivel 1. Va antes de las del nivel 2 para que
        # Structurizr no la derive solo de "Sincroniza contenido".
        auladesk -> moodle "Sincroniza contenido y muestra el sitio real"

        # Relaciones del nivel 2. Las del nivel 1 se derivan de estas.
        estudiante -> auladesk.app "Revisa novedades y configura alertas"
        estudiante -> navegador "Inicia sesión"
        auladesk.app -> navegador "Abre el login" "URL de launch.php"
        navegador -> moodle "Autentica al usuario" "HTTPS"
        so -> auladesk.app "Entrega el token de login" "Deep link"
        auladesk.app -> so "Notifica y guarda el token" "API nativa"
        so -> estudiante "Muestra alertas"
        auladesk.app -> moodle "Sincroniza contenido" "HTTPS/JSON"
        auladesk.app -> moodle "Muestra el sitio real" "HTTPS, WebView + autologin"
        auladesk.app -> auladesk.db "Lee y escribe" "SQL"
        auladesk.app -> auladesk.carpeta "Guarda descargas"
        auladesk.app -> productividad "Exporta calificaciones" "Archivo"
        auladesk.app -> calendario "Publica fechas de evaluación" "API de Google Calendar"
        calendario -> moodle "Se suscribe a las fechas" "URL iCal de Moodle"
    }

    views {
        systemContext auladesk "Contexto" {
            title "[Diagrama de contexto del sistema] AulaDesk"
            description "El sistema, sus usuarios y los sistemas externos."
            include *
            autoLayout
        }

        container auladesk "Contenedores" {
            title "[Diagrama de contenedores] AulaDesk"
            description "Piezas ejecutables y almacenes de datos del producto (fase 2). La CLI de la fase 1 es una herramienta interna y no aparece."
            include *
            autoLayout tb 300 150
        }

        terminology {
            person "Persona"
            softwareSystem "Sistema de software"
            container "Contenedor"
            component "Componente"
            relationship "Relación"
        }

        styles {
            element "Person" {
                shape Person
                background #08427b
                color #ffffff
            }
            element "Software System" {
                background #1168bd
                color #ffffff
            }
            element "Container" {
                background #438dd5
                color #ffffff
            }
            element "Externo" {
                background #999999
                color #ffffff
            }
            element "Base de datos" {
                shape Cylinder
            }
            element "Carpeta" {
                shape Folder
            }
            element "Navegador" {
                shape WebBrowser
            }
        }
    }

    configuration {
        scope softwaresystem
    }

}
