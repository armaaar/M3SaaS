<?php

function setMigrationRoute($router) {
    if (ALLOW_MIGRATION) {
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
        });
    }
}
