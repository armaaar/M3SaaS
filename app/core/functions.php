<?php

function setMigrationRoute($router) {
    if (AUTO_MIGRATION) {
        Stalker_Migrator::migrate();
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
    }

    foreach ( glob($db_directory_path."/views/*.view.php") as $file ) {
        require_once $file;
    }
}

function salt_db_password($password, $salt = "") {
    // using MD5 because MYSQL max password length is 32 characters
    return md5($salt . $password . $salt);
}
