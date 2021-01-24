<?php

register_crobjob('test_daily_notification', function() {
    $mqtt = MQTT_Client::instance();
    $mqtt->publish("{$tenant->name}/notifications", "Here is your daily notification!");
}, 'day');

register_crobjob('test_weekly_offer', function() {
    $mqtt = MQTT_Client::instance();
    $mqtt->publish("{$tenant->name}/notifications", "Don't miss our offer!");
}, 'week');
