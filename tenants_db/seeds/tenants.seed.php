<?php

class Tenants_Seed extends Stalker_Seed {
    public function main_seed() {
        return [
            [
                "id" => 1,
                "name" => "first_tenant",
            ]
        ];
    }
}
