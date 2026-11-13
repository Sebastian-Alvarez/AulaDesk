<?php
// Muestra el token del estudiante de prueba para el servicio móvil (D-31).
// Uso: just moodle-token. Moodle reutiliza el mismo token en cada ejecución, como en la USM.
// Es un token del Moodle local: no sirve fuera de él, pero tampoco se guarda en el repositorio.

define('CLI_SCRIPT', true);

require(getenv('MOODLE_DIR') . '/config.php');
require_once($CFG->libdir . '/clilib.php');

$username = getenv('MOODLE_STUDENT_USERNAME') ?: 'estudiante';
$student = $DB->get_record('user', ['username' => $username, 'deleted' => 0], '*', MUST_EXIST);
$service = $DB->get_record('external_services', ['shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE], '*', MUST_EXIST);

\core\session\manager::set_user($student);
$token = \core_external\util::generate_token_for_current_user($service);

cli_writeln("wwwroot:      {$CFG->wwwroot}");
cli_writeln("usuario:      {$username}");
cli_writeln("wstoken:      {$token->token}");
cli_writeln("privatetoken: {$token->privatetoken}");
