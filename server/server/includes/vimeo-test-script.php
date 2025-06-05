<?php
require '../../vendor/autoload.php';
use Vimeo\Vimeo;

$client = new Vimeo("92aee3ed57c154519a678d2b8776fa340a89e7e3", "eHbqh6B/CUVj7ut51AmZlqbDu8WnDFlHKfSjUdOdnLwpA2+CdhmzN05R0tKa6U0Aq7RKT+8W0xH51/GqT24UPB/++L3AxioKNRnBWMHGb7DPPdKCfl5W1a4yy9z4tYYX", "43dfff1e1d75387ae5285011ca07fafd");

$response = $client->request('/tutorial', array(), 'GET');
print_r($response);