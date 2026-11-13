<?php
// Configuración y datos de ejemplo del Moodle de pruebas (D-31).
// Lo ejecuta setup.sh después de instalar Moodle. Si los datos ya existen, no hace nada.

define('CLI_SCRIPT', true);

require(getenv('MOODLE_DIR') . '/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/testing/generator/lib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/calendar/lib.php');

$username = getenv('MOODLE_STUDENT_USERNAME') ?: 'estudiante';
$password = getenv('MOODLE_STUDENT_PASSWORD') ?: 'Estudiante-1234';

if ($DB->record_exists('user', ['username' => $username, 'deleted' => 0])) {
    cli_writeln("Los datos de ejemplo ya existen; no se modifican.");
    exit(0);
}

\core\session\manager::set_user(get_admin());

// Servicios web y servicio móvil, igual que al activarlos desde la administración.
$mobile = new admin_setting_enablemobileservice('enablemobilewebservice', '', '', 0);
$mobile->write_setting('1');

// Login por navegador embebido, como la USM (validación de la fase 1). Lo exige launch.php.
set_config('typeoflogin', \tool_mobile\api::LOGIN_VIA_EMBEDDED_BROWSER, 'tool_mobile');

// Zona horaria de la USM, para que los plazos se vean como en el aula.
set_config('timezone', 'America/Santiago');

// Portada: sin sesión iniciada, solo un mensaje de bienvenida, sin la lista de cursos.
set_config('frontpage', 'none');
course_create_sections_if_missing(SITEID, [1]);
$welcome = $DB->get_record('course_sections', ['course' => SITEID, 'section' => 1], '*', MUST_EXIST);
course_update_section(get_site(), $welcome, [
    'summary' => '<h3>Moodle de pruebas de AulaDesk</h3>'
        . '<p>Instancia local para desarrollar y probar AulaDesk. Inicia sesión con el usuario de prueba '
        . 'indicado en <code>CONTRIBUTING.md</code>.</p>',
    'summaryformat' => FORMAT_HTML,
]);

$generator = new testing_data_generator();
$day = DAYSECS;
$now = time();

// Pone nombre a las secciones del curso (por defecto Moodle las deja como "New section").
function name_sections(stdClass $course, array $names): void {
    global $DB;
    foreach ($names as $number => $name) {
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $number], '*', MUST_EXIST);
        course_update_section($course, $section, ['name' => $name]);
    }
}

$student = $generator->create_user([
    'username' => $username,
    'password' => $password,
    'firstname' => 'Estudiante',
    'lastname' => 'De Prueba',
    'email' => 'estudiante@example.com',
]);

// --- Curso 1: contenido variado, con varias etiquetas como en la USM ---
$hist = $generator->create_course([
    'fullname' => 'Historia Antigua',
    'shortname' => 'HIS204',
    'summary' => '<p>Ramo de prueba: Roma y Grecia.</p>',
    'numsections' => 3,
]);
$generator->enrol_user($student->id, $hist->id, 'student');
name_sections($hist, [1 => 'Unidad 1: Roma', 2 => 'Unidad 2: Grecia', 3 => 'Material complementario']);

$generator->create_module('label', ['course' => $hist->id, 'section' => 1,
    'intro' => '<p>Bienvenidos al ramo. Revisen el programa antes de la primera clase.</p>']);
$generator->create_module('page', ['course' => $hist->id, 'section' => 1,
    'name' => 'Programa del ramo', 'content' => '<p>Evaluaciones: ensayos (40 %) y certámenes (60 %).</p>']);
$generator->create_module('resource', ['course' => $hist->id, 'section' => 1, 'name' => 'Lectura 1: la República romana']);
$generator->create_module('url', ['course' => $hist->id, 'section' => 1,
    'name' => 'Perseus Digital Library', 'externalurl' => 'https://www.perseus.tufts.edu/']);
$generator->create_module('label', ['course' => $hist->id, 'section' => 2,
    'intro' => '<p>Unidad 2: la polis griega y la democracia ateniense.</p>']);

$ensayo1 = $generator->create_module('assign', ['course' => $hist->id, 'section' => 1,
    'name' => 'Ensayo 1: la caída de la República', 'duedate' => $now - 7 * $day, 'grade' => 100,
    'assignsubmission_onlinetext_enabled' => 1]);
$generator->create_module('assign', ['course' => $hist->id, 'section' => 2,
    'name' => 'Ensayo 2: la democracia ateniense', 'duedate' => $now + 7 * $day, 'grade' => 100,
    'assignsubmission_onlinetext_enabled' => 1]);
$generator->create_module('label', ['course' => $hist->id, 'section' => 3,
    'intro' => '<p>Material complementario.</p>']);

// Ensayo 1 ya calificado.
$item = grade_item::fetch(['itemtype' => 'mod', 'itemmodule' => 'assign',
    'iteminstance' => $ensayo1->id, 'courseid' => $hist->id]);
$generator->create_grade_grade(['itemid' => $item->id, 'userid' => $student->id, 'grade' => 85]);

// --- Curso 2: notas manuales y una tarea pendiente ---
$calc = $generator->create_course([
    'fullname' => 'Cálculo I',
    'shortname' => 'MAT021',
    'summary' => '<p>Ramo de prueba: límites y derivadas.</p>',
    'numsections' => 2,
]);
$generator->enrol_user($student->id, $calc->id, 'student');
name_sections($calc, [1 => 'Unidad 1: límites', 2 => 'Unidad 2: derivadas']);

$generator->create_module('page', ['course' => $calc->id, 'section' => 1,
    'name' => 'Programa del ramo', 'content' => '<p>Certamen 1 (50 %) y Certamen 2 (50 %).</p>']);
$generator->create_module('assign', ['course' => $calc->id, 'section' => 1,
    'name' => 'Guía de ejercicios', 'duedate' => $now + 14 * $day, 'grade' => 100]);

$c1 = $generator->create_grade_item(['courseid' => $calc->id, 'itemname' => 'Certamen 1', 'grademax' => 100]);
$generator->create_grade_item(['courseid' => $calc->id, 'itemname' => 'Certamen 2', 'grademax' => 100]);
$generator->create_grade_grade(['itemid' => $c1->id, 'userid' => $student->id, 'finalgrade' => 62]);

// --- Eventos del calendario (las tareas crean los suyos) ---
$generator->create_event(['name' => 'Certamen 2', 'eventtype' => 'course', 'courseid' => $calc->id,
    'timestart' => $now + 10 * $day]);
$generator->create_event(['name' => 'Reunión de grupo', 'eventtype' => 'user', 'userid' => $student->id,
    'timestart' => $now + 3 * $day]);
// El generador no guarda el curso de los eventos del sitio, así que este se crea directamente.
calendar_event::create(['name' => 'Semana de receso', 'eventtype' => 'site', 'courseid' => SITEID,
    'timestart' => $now + 21 * $day, 'timeduration' => 0], false);

cli_writeln("Datos de ejemplo creados: usuario {$username}, cursos HIS204 y MAT021.");
