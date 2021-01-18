<?php

function register_crobjob(
    string $job_name,
    closure $job_fn,
    string $repeat = 'day',
    int $repeat_interval = 1,
    int $day = null,
    int $month = null
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
    $day_pattern = ($repeat === 'month' || $repeat === 'year') && $day ? date('d', strtotime("1995-1-$day")) : 'd';
    $month_pattern = $repeat === 'year' && $month ? date('m', strtotime("1995-$month")) : 'm';
    $next_run = date("Y-$month_pattern-$day_pattern", strtotime("$job->last_run +$repeat_interval $repeat"));

    $job_should_run = ($new_job || $today >= $next_run) && (
        !$day && !$month || (
            $repeat === 'week' && intval(date('w')) === $day || // week day number (0 - 6) (Sunday=0)
            $repeat === 'month' && intval(date('j')) === $day || // month day number (1 - 31)
            $repeat === 'year' && intval(date('j')) === $day &&  intval(date('n')) === $month // month number (1 - 12)
        )
    );

    // run job if it should be run
    if ($job_should_run) {
        $job_fn();
        // update job last run
        $job->last_run = $today;
        $job->save();
    }
    return true;
}
