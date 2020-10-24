<?php

class Tenants extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255)->unique();
        });
    }

    public function modules() {
        return array_map(function ($subscription) {
            $module = $subscription->module;
            $module->version = $subscription->version;
            return $module;
        }, $this->has_many('Subscriptions', 'tenant_id'));
    }
}
