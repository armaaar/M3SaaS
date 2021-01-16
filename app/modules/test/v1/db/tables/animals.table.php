<?php

class Test_Animals extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255);
        });
    }
}

Stalker_Registerar::register_table('Test_Animals');
