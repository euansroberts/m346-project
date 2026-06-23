<?php
require __DIR__ . "/main.php";
$json = json_decode(file_get_contents(__DIR__ . "/data.json"));

// Eingerückte elementie wie "vms" werden zu arrays konvertiert
$convert_vms = function ($vms_source) {
  $out = [];
  foreach ((array)$vms_source as $key => $value) {
    $out[$key] = is_object($key) ? (array)$value : (array)$value;
  }
  return $out;
};

$small_data = $json->small;
$medium_data = $json->medium;
$big_data = $json->big;

Server::$server_data = [
  "small" => new Server(
    $small_data->id,
    $convert_vms($small_data->vms),
    $small_data->revenue,
    $small_data->max_cpu,
    $small_data->max_ram,
    $small_data->max_ssd,
    $small_data->used_cpu,
    $small_data->used_ram,
    $small_data->used_ssd
  ),
  "medium" => new Server(
    $medium_data->id,
    $convert_vms($medium_data->vms),
    $medium_data->revenue,
    $medium_data->max_cpu,
    $medium_data->max_ram,
    $medium_data->max_ssd,
    $medium_data->used_cpu,
    $medium_data->used_ram,
    $medium_data->used_ssd
  ),
  "big" => new Server(
    $big_data->id,
    $convert_vms($big_data->vms),
    $big_data->revenue,
    $big_data->max_cpu,
    $big_data->max_ram,
    $big_data->max_ssd,
    $big_data->used_cpu,
    $big_data->used_ram,
    $big_data->used_ssd
  ),
];
