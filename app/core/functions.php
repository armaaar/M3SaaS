<?php

function setMigrationRoute($router) {
    if (AUTO_MIGRATION) {
        Stalker_Migrator::migrate();
        if (ALWAYS_FORCE_MAIN_SEEDS) {
            Stalker_Seeder::delete_main_seeds();
        }
        Stalker_Seeder::seed_main_seeds(ALWAYS_FORCE_MAIN_SEEDS);
    } else {
        $router->get('/migrate/{:a}?', function($seed=false) {
            Stalker_Migrator::migrate();
            if($seed == "force") {
                Stalker_Seeder::delete_main_seeds();
                Stalker_Seeder::seed_main_seeds(true);
            } else {
                Stalker_Seeder::seed_main_seeds();
            }
            if ($seed == "seed") {
                Stalker_Seeder::seed_temporary_seeds();
            } elseif($seed == "deseed") {
                Stalker_Seeder::delete_temporary_seeds();
            }
            http_response_code(204);
        });
    }
}

function require_db_files($db_directory_path) {

    foreach ( glob($db_directory_path."/seeds/*.seed.php") as $file ) {
        require_once $file;
    }

    foreach ( glob($db_directory_path."/tables/*.table.php") as $file ) {
        require_once $file;
        Stalker_Registerar::register_table(get_file_name($file));
    }

    foreach ( glob($db_directory_path."/views/*.view.php") as $file ) {
        require_once $file;
        Stalker_Registerar::register_view(get_file_name($file));
    }
}

function load_module(Modules $module, Closure $on_load, bool $reset_loaded_modules = false) {
    static $loaded_modules  = [];

    if ($reset_loaded_modules) {
        $loaded_modules  = [];
    }


    if (in_array($module->id, $loaded_modules)) return;

    if ($module->dependency_modules) {
        foreach ($module->dependency_modules as $dependency_module) {
            load_module($dependency_module, $on_load);
        }
    }

    call_user_func_array($on_load, [$module]);

    $loaded_modules[] = $module->id;
}

function salt_db_password($password, $salt = "") {
    // using MD5 because MYSQL max password length is 32 characters
    return md5($salt . $password . $salt);
}

function get_file_name($path) {
    return explode('.', end(explode('/', $path)))[0];
}

function var_dump_log($x) {
    ob_start();
    var_dump($x);
    error_log(ob_get_clean());
}
