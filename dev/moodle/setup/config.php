<?php
// Configuración del Moodle de pruebas (D-31). setup.sh la copia a la carpeta del código.
// Solo para desarrollo local: los valores vienen de compose.yaml.

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'pgsql';
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('MOODLE_DB_HOST') ?: 'db';
$CFG->dbname    = getenv('MOODLE_DB_NAME') ?: 'moodle';
$CFG->dbuser    = getenv('MOODLE_DB_USER') ?: 'moodle';
$CFG->dbpass    = getenv('MOODLE_DB_PASSWORD') ?: 'moodle';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = ['dbpersist' => 0, 'dbport' => '', 'dbsocket' => ''];

$CFG->wwwroot   = getenv('MOODLE_WWWROOT') ?: 'http://localhost:8090';
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 02777;

// Entorno de desarrollo: Moodle no envía correos. La depuración queda apagada,
// como en la USM, para que las respuestas de la API no traigan avisos de PHP.
$CFG->noemailever = true;

require_once(__DIR__ . '/lib/setup.php');
