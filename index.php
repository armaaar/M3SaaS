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

// Import core functions
require_once './core/functions.php';

// Import DBStalker
require_once './core/dbstalker/dbstalker.php';
Stalker_Configuration::set_stalker_configuration($configuration->databases->tenants);

// Import and register tenants Database
foreach ( glob("./tenants_db/tables/*.table.php") as $file ) {
    require_once $file;
}

foreach ( glob("./tenants_db/views/*.view.php") as $file ) {
    require_once $file;
}

foreach ( glob("./tenants_db/seeds/*.seed.php") as $file ) {
	require_once $file;
}
Stalker_Registerar::auto_register();

// Import Router
require_once "./core/miniRouter.php";
$router = new miniRouter();

// SaaS logic
$router->group(APP_SUB_DIRECTORY, function($router){
    setMigrationRoute($router);
    $router->group('/{:i}', function($router, $tenant_id) {
        $tenant = Tenants::get($tenant_id);
        if ($tenant) {
            // fetch tanent modules before changing db settings
            $modules = $tenant->modules;
        }
    });
});
$router->fallback(function() {
    http_response_code(404);
});

$router->start_routing();
