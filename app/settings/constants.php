<?php

define("TIME_ZONE","Africa/Cairo");

if (property_exists($configuration, "sub_directory")) {
    define("APP_SUB_DIRECTORY", $configuration->sub_directory);
} else {
    define("APP_SUB_DIRECTORY", '');
}

define("AUTO_MIGRATION", true);
define("ALWAYS_FORCE_MAIN_SEEDS", false);
