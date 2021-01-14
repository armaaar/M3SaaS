<?php

class Tenants extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255)->unique();
            $table->varchar("user", 255)->unique();
            $table->password("password");
            $table->int("per_day_backups", 2)->def(1);
            $table->int("max_backups", 2)->def(10);
        });
    }

    public function modules() {
        return array_map(function ($subscription) {
            $module = $subscription->module;
            $module->version = $subscription->version;
            return $module;
        }, $this->has_many('Subscriptions', 'tenant_id'));
    }

    public function create_database($salt = "") {
        if (!Information_Schema::database_exists($this->name)) {
            $settings = Stalker_Configuration::table_settings();
            $salted_password = salt_db_password($this->password, $salt);

            Stalker_Database::instance()->unprepared_execute(
                "CREATE DATABASE `{$this->name}`
                    CHARACTER SET {$settings->charset}
                    COLLATE {$settings->collation};
                CREATE USER '{$this->user}' IDENTIFIED BY '$salted_password';
                GRANT ALL ON `{$this->name}`.* TO '{$this->user}';
                FLUSH PRIVILEGES;"
            );
            return true;
        }
        return false;
    }
}
