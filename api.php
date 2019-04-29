<?php
/*
define('FS_FOLDER', __DIR__);

/// cargamos las constantes de configuración
require_once 'config.php';
require_once 'base/config2.php';
require_once 'base/fs_core_log.php';
require_once 'base/fs_db2.php';
$db = new fs_db2();

require_once 'base/fs_extended_model.php';
require_once 'base/fs_log_manager.php';
require_once 'base/fs_api.php';
require_all_models();

$db->connect();

if ($db->connected()) {
    $api = new fs_api();
    echo $api->run();
} else {
    echo 'ERROR al conectar a la base de datos';
}

/// guardamos los errores en el log
$log_manager = new fs_log_manager();
$log_manager->save();

$db->close();*/
