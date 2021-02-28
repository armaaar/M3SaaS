<?php

class Modules_Dependencies extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->id("module_id")->index();
            $table->id("dependency_module_id")->index();
            $table->int("version", 2)->def(1);
        });
    }

    public function module() {
        return $this->belongs_to("Modules", "module_id");
    }

    public function dependency_module() {
        return $this->belongs_to("Modules", "dependency_module_id");
    }
}
