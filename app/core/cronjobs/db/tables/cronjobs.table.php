<?php

class Cronjobs extends Stalker_Table
{
    public function schema() {
        return Stalker_Schema::build( function ($table) {
            $table->varchar("name", 255)->unique();
            $table->date('last_run');
        });
    }
}
