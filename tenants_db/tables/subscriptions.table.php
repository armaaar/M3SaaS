<?php

class Subscriptions extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->id("tenant_id")->index();
            $table->id("module_id")->index();
            $table->int("version", 2)->def(1);
        });
    }

    public function tenant() {
        return $this->belongs_to("Tenants", "tenant_id");
    }

    public function module() {
        return $this->belongs_to("Modules", "module_id");
    }
}
