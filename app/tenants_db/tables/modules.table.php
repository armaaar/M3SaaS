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
        return array_map(function ($dependency) {
            $dependency_module = $dependency->dependency_module;
            $dependency_module->version = $dependency->version;
            return $dependency_module;
        }, $this->has_many('Modules_Dependencies', 'module_id'));
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
