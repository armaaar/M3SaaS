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

    public function dependency_modules() {
        return $this->has_many_through("Modules", "Modules_Dependencies", "dependency_module_id", "module_id");
    }

    public function fetch_dependency_modules_recursively() {
        $this->dependency_modules = $this->dependency_modules();
        if ($this->dependency_modules) {
            foreach ($this->dependency_modules as $dependency_module) {
                $dependency_module->fetch_dependency_modules_recursively();
            }
        }
    }
}
