<?php

function update_animal_request($animal) {
    $animal->name = $_REQUEST['name'];

    $errors = $animal->validate();
    if ($errors) {
        http_response_code(400);
        echo json_encode([
            "errors" => $errors
        ]);
        return;
    }

    $animal->save();
    http_response_code(201);
    echo json_encode($animal);
}

$router->get('/animals', function() {
    echo json_encode(Test_Animals::fetch());
});

$router->post('/animals', function() {
    $animal = new Test_Animals();
    update_animal_request($animal);
});

$router->delete('/animals/{:i}', function($tanent_id, $version_number, $animal_id) {
    $animal = Test_Animals::get($animal_id);
    if ($animal) {
        $animal->delete();
        http_response_code(204);
    } else {
        http_response_code(400);
    }
});

if ($version_number >= '1.1') {
    $router->put('/animals/{:i}', function($tanent_id, $version_number, $animal_id) {
        $animal = Test_Animals::get($animal_id);
        if ($animal) {
            update_animal_request($animal);
        } else {
            http_response_code(400);
        }
    });
}
