<?php

$resultat = array(
    "code" => 0,
    "msg" => 'ok'
);

// Load provider class
if (is_null($get_information['provider_id']) || is_null($get_information['form'])) {
    $resultat['code'] = 1;
    $resultat['msg'] = 'please provide provider_id or form';
    return ;
}

$provider_name = null;
foreach ($register_providers as $name => $id) {
    if ($id == $get_information['provider_id']) {
        $provider_name = $name;
        break;
    }
}

if (is_null($provider_name) || !file_exists($centreon_open_tickets_path . 'providers/' . $provider_name . '/' . $provider_name . 'Provider.class.php')) {
    $resultat['code'] = 1;
    $resultat['msg'] = 'cannot find provider';
    return ;
}

require_once $centreon_open_tickets_path . 'providers/' . $provider_name . '/' . $provider_name . 'Provider.class.php';

$classname = $provider_name . 'Provider';
$centreon_provider = new $classname($db, $centreon_path, $centreon_open_tickets_path, $get_information['rule_id'], $get_information['form']);

try {
    $resultat['result'] = $centreon_provider->saveConfig();
} catch (Exception $e) {
    $resultat['code'] = 1;
    $resultat['message'] = $e->getMessage();
}

$fp = fopen('/tmp/debug.txt', 'a+');
fwrite($fp, print_r($get_information['form'], true));

?>