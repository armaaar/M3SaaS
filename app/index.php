<?php

chdir(__DIR__);

// initial settings
require_once "./settings/ini.php";

// Read configurations
$config_file_path = './config.json';
if (isset($_ENV['CONFIG_FILE'])) {
    $config_file_path = $_ENV['CONFIG_FILE'];
}

if (!file_exists($config_file_path)) {
    trigger_error("ERROR: Config file not defined", E_USER_WARNING);
    die();
}

// load config.json
$configuration = json_decode(file_get_contents($config_file_path));
if(json_last_error()!==JSON_ERROR_NONE) {
    error_log(json_last_error());
    die();
}

// define constants
require_once "./settings/constants.php";

// set timezone
date_default_timezone_set(TIME_ZONE);

// Import core functions
require_once './core/functions.php';

// Import MQTT Client
require_once './core/mqtt_client.php';
if (property_exists($configuration, 'mqtt')) {
    if (property_exists($configuration->mqtt, 'username') && property_exists($configuration->mqtt, 'password')) {
        MQTT_Client::instance(
            $configuration->mqtt->host,
            intval($configuration->mqtt->port),
            $configuration->mqtt->username,
            $configuration->mqtt->password
        );
    } else {
        MQTT_Client::instance($configuration->mqtt->host, intval($configuration->mqtt->port));
    }
}

// Import DBStalker
require_once './core/dbstalker/dbstalker.php';
Stalker_Configuration::set_stalker_configuration($configuration->tenants);

// Import and register tenants Database
require_db_files('./tenants_db');
// Import core modules db files
require_db_files("./core/cronjobs/db");
Stalker_Registerar::auto_register();

if (isset($_ENV['CRON_JOBS']) && $_ENV['CRON_JOBS'] === 'true') {
    require_once './cron.php';
} else {
    require_once './routes.php';
}
