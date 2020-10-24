<?php

class Tenants extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255)->unique();
        });
    }

    public function modules() {
        return $this->has_many_through("Modules", "Subscriptions", "module_id", "tenant_id");
    }
}
