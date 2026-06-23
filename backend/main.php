<?php

class Server
{
  public static array $server_data;

  public int $id;
  public array $vms;
  public int $revenue = 0;
  public int $used_cpu = 0;
  public int $used_ram = 0;
  public int $used_ssd = 0;
  public readonly int $max_cpu;
  public readonly int $max_ram;
  public readonly int $max_ssd;

  function __construct(
    int $id,
    array $vms,
    int $revenue,
    int $max_cpu,
    int $max_ram,
    int $max_ssd,
    int $used_cpu,
    int $used_ram,
    int $used_ssd
  ) {
    $this->id = $id;
    $this->vms = [];
    // Wenn es aus irgend einem grund ein objekt ist, zum array konvertieren
    foreach ((array)$vms as $key => $value) {
      $this->vms[$key] = is_object($value) ? (array)$value : (array)$value;
    }
    $this->revenue = $revenue;
    $this->max_cpu = $max_cpu;
    $this->max_ram = $max_ram;
    $this->max_ssd = $max_ssd;
    $this->used_cpu = $used_cpu;
    $this->used_ram = $used_ram;
    $this->used_ssd = $used_ssd;
  }

  function get_load(): int
  {
    return $this->used_cpu + $this->used_ram + $this->used_ssd;
  }

  static function get_all_vm_names(): array | null
  {
    $all_vm_names = [];
    foreach (Server::$server_data as $server) {
      foreach (array_keys($server->vms) as $vmname) {
        array_push($all_vm_names, $vmname);
      }
    }
    return $all_vm_names;
  }

  function get_vm_price(int $cpu, int $ram, int $ssd): int
  {
    $price = ($ram * 1.25) + ($ssd * 10);
    switch ($cpu) {
      case 1:
      case 2:
        $price += ($cpu * 5);
        break;
      case 4:
        $price += 18;
        break;
      case 8:
        $price += 30;
        break;
      case 16:
        $price += 45;
        break;
    }
    return $price;
  }

  function price_recalq(): int
  {
    $total = 0;
    foreach ($this->vms as $vm) {
      if (is_array($vm) && isset($vm['cost'])) {
        $total += $vm['cost'];
      }
    }
    return $total;
  }

  static function get_server_by_id(int $id): Server | null
  {
    foreach (Server::$server_data as $server) {
      if ($server->id == $id) {
        return $server;
      }
    }
    return null;
  }

  static function select_server(string $vm_name, int $cpu, int $ram, int $ssd): int | null
  {
    $all_vm_names = Server::get_all_vm_names();
    if (in_array($vm_name, $all_vm_names)) {
      return null;
    }

    $load = [];
    foreach (Server::$server_data as $server) {
      $load[$server->id] = $server->get_load();
    }

    asort($load);

    foreach ($load as $server_id => $load_value) {
      $server = Server::get_server_by_id($server_id);
      if (
        ($server->max_cpu - $server->used_cpu >= $cpu) &&
        ($server->max_ram - $server->used_ram >= $ram) &&
        ($server->max_ssd - $server->used_ssd >= $ssd)
      ) {
        $server->used_cpu += $cpu;
        $server->used_ram += $ram;
        $server->used_ssd += $ssd;

        $cost = $server->get_vm_price($cpu, $ram, $ssd);
        $server->vms[$vm_name] = [
          "vm_used_cpu" => $cpu,
          "vm_used_ram" => $ram,
          "vm_used_ssd" => $ssd,
          "cost" => $cost
        ];

        $server->revenue = $server->price_recalq();
        return $server->id;
      }
    }
    return null;
  }

  static function delete_server(string $vm_name_delete): bool
  {
    foreach (Server::$server_data as $server) {
      if (array_key_exists($vm_name_delete, $server->vms)) {
        $vm_array = (array) $server->vms[$vm_name_delete];

        $server->used_cpu -= ($vm_array["vm_used_cpu"] ?? 0);
        $server->used_ram -= ($vm_array["vm_used_ram"] ?? 0);
        $server->used_ssd -= ($vm_array["vm_used_ssd"] ?? 0);
        unset($server->vms[$vm_name_delete]);
        $server->revenue = $server->price_recalq();
        return true;
      }
    }
    return false;
  }
}
