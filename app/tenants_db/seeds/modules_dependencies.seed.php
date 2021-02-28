<?php

class Modules_Dependencies_Seed extends Stalker_Seed {
    public function main_seed() {
        return [
            [
                "id" => 1,
                "module_id" => 1,
                "dependency_module_id" => 2,
            ],
            [
                "id" => 2,
                "module_id" => 2,
                "dependency_module_id" => 3,
            ],
            [
                "id" => 3,
                "module_id" => 2,
                "dependency_module_id" => 5,
            ],
            [
                "id" => 4,
                "module_id" => 5,
                "dependency_module_id" => 4,
            ],
            [
                "id" => 5,
                "module_id" => 6,
                "dependency_module_id" => 3,
            ],
            [
                "id" => 6,
                "module_id" => 7,
                "dependency_module_id" => 1,
            ]
        ];
    }
}
