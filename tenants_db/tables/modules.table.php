<?php

class Modules extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255)->unique();
        });
    }

    public function tenants() {
        return $this->has_many_through("Tenants", "Subscriptions", "tenant_id", "module_id");
    }
}
