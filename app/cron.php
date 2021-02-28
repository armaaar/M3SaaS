<?php
// include cronjobs methods
require_once './core/cronjobs/cronjobs.methods.php';

// Run backup cron job for tenants_db
register_crobjob('database_backup', function() {
    Stalker_Backup::create_backup();
}, 'month');

// run cron jobs for all tanents
$tenants = Tenants::fetch();
if ($tenants) {
    foreach ($tenants as $tenant) {
        // fetch tenant modules before changing db settings
        $modules = $tenant->modules;
        // fetch dependecies recursively
        foreach ($modules as $module) {
            $module->fetch_dependency_modules_recursively();
        }

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

        // Add database backup cronjob
        register_crobjob('database_backup', function() {
            Stalker_Backup::create_backup();
        }, 'month');

        // Load modules settings and jobs
        $is_first_module = true;
        foreach ($modules as $module) {
            load_module($module, function($module) use(&$is_first_module) {
                $is_first_module = false;
                // Import any Database files
                require_db_files("./modules/{$module->name}/v{$module->version}/db");

                if (file_exists("./modules/{$module->name}/v{$module->version}/{$module->name}.cron.php")) {
                    // set module route
                    include "./modules/{$module->name}/v{$module->version}/{$module->name}.cron.php";
                }
            }, $is_first_module);
        }
    }
}
