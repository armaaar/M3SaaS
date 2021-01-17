<?php

function register_crobjob(
    string $job_name,
    closure $job_fn,
    int $repeat_interval = 1,
    string $repeat = 'day'
) {
    // validate repeat values
    if (!in_array($repeat, ['day', 'week', 'month', 'year']) || $repeat_interval < 1) {
        trigger_error(
            "ERROR::CRONJOB job '$job_name' got invalid repeat value '$repeat_interval $repeat'",
            E_USER_WARNING
        );
        return false;
    }
    // get today date
    $today = date('Y-m-d');

    // get job data, or create it if it doesn't exist
    $new_job = false;
    $job = Cronjobs::where('name', $job_name) ->first();
    if (!$job) {
        $new_job = true;
        $job = new Cronjobs();
        $job->name = $job_name;
        $job->last_run = $today;
    }
    // calculate next run date
    $next_run = date('Y-m-d', strtotime("$job->last_run +$repeat_interval $repeat"));

    // run job if it should be run
    if ($new_job || $today >= $next_run) {
        $job_fn();
        // update job last run
        $job->last_run = $today;
        $job->save();
    }
    return true;
}
