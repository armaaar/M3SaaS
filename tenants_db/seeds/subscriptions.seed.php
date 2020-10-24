<?php

class Subscriptions_Seed extends Stalker_Seed {
    public function main_seed() {
        return [
            [
                "id" => 1,
                "tenant_id" => 1,
                "module_id" => 1
            ]
        ];
    }
}
