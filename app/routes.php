<?php

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
            // create tenant DB if it doesn't exists
            $is_new_database = $tenant->create_database($configuration->dbSalt);

            // fetch tenant modules before changing db settings
            $modules = $tenant->modules;

            // Switch to tenant database
            Stalker_Registerar::clear_registerar();
            Stalker_Configuration::set_stalker_configuration((object) [
                "database" => (object) [
                    "host" => $configuration->tenants->database->host,
                    "database" => $tenant->name,
                    "user" => $tenant->user,
                    "password" => salt_db_password($tenant->password, $configuration->dbSalt)
                ],
                "backup" => (object) [
                    "perDay" => $tenant->per_day_backups,
                    "max" => $tenant->per_day_backups
                ]
            ]);

            Stalker_Database::instance(true);

            // load core modules necessary files
            require_db_files("./core/cronjobs/db");

            // Load modules settings and routes
            foreach ($modules as $module) {
                // Import any Database files
                require_db_files("./modules/{$module->name}/v{$module->version}/db");

                // set module route
                $router->group("/{$module->name}/v({$module->version}(?!\d)(?:\.\d+)*)",
                    function($router, $tenant_id, $version_number) use($module) {
                        include_once "./modules/{$module->name}/v{$module->version}/{$module->name}.module.php";
                    }
                );
            }

            if ($is_new_database && !AUTO_MIGRATION) {
                Stalker_Migrator::migrate();
                if (ALWAYS_FORCE_MAIN_SEEDS) {
                    Stalker_Seeder::delete_main_seeds();
                }
                Stalker_Seeder::seed_main_seeds(ALWAYS_FORCE_MAIN_SEEDS);
            }

            // Add migration route for tenant
            setMigrationRoute($router);
        }
    });
});
$router->fallback(function() {
    http_response_code(404);
});

$router->start_routing();
