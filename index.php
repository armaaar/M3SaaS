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
require_db_files('./tenants_db');
Stalker_Registerar::auto_register();

// Import Router
require_once "./core/miniRouter.php";
$router = new miniRouter();

// SaaS logic
$router->group(APP_SUB_DIRECTORY, function($router) use($configuration) {
    // Add migration route
    setMigrationRoute($router);

    $router->group('/{:i}', function($router, $tenant_id) use($configuration) {
        $tenant = Tenants::get($tenant_id);
        if ($tenant) {
            // fetch tanent modules before changing db settings
            $modules = $tenant->modules;
            // Switch to tanent database
            Stalker_Registerar::clear_registerar();
            Stalker_Configuration::set_stalker_configuration(
                $configuration->databases->{$tenant->name}
            );
            Stalker_Database::instance(true);

            // Load modules settings and routes
            foreach ($modules as $module) {
                // Import any Database files
                require_db_files("./modules/{$module->name}/v{$module->version}/db");

                // set module route
                $router->group("/{$module->name}/v{$module->version}", function($router) use($module) {
                    include_once "./modules/{$module->name}/v{$module->version}/{$module->name}.module.php";
                });
            }

            // Add migration route for tanent
            setMigrationRoute($router);
        }
    });
});
$router->fallback(function() {
    http_response_code(404);
});

$router->start_routing();
