<?php

// load config.json
$configuration = json_decode(file_get_contents("./config.json"));
if(json_last_error()!==JSON_ERROR_NONE) {
    error_log(json_last_error());
    die();
}

// initial settings
require_once "./settings/constants.php";
require_once "./settings/ini.php";

// set timezone
date_default_timezone_set(TIME_ZONE);

// Import DBStalker
require_once './core/dbstalker/dbstalker.php';

// Import Router
require_once "./core/miniRouter.php";
$router = new miniRouter();

// SaaS logic

Stalker_Registerar::auto_register();
$router->start_routing();
