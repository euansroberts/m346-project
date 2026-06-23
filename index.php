<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="frontend/styles.css">
  <title>UberCloud</title>
</head>


<body>
  <header>
    <h1>UberCloud - Demo (Modul 346)</h1>
  </header>

  <main>
    <?php // Validation
    require __DIR__ . "/backend/init.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (
        !empty($_POST["vm_name"]) && 30 >= strlen($_POST["vm_name"]) &&
        !empty($_POST["cpu"]) &&
        !empty($_POST["ram"]) &&
        !empty($_POST["ssd"])
      ) {
        $vm_name = htmlspecialchars(trim($_POST["vm_name"]));
        $cpu = htmlspecialchars($_POST["cpu"]);
        $ram = htmlspecialchars($_POST["ram"]);
        $ssd = htmlspecialchars($_POST["ssd"]);

        Server::select_server($vm_name, $cpu, $ram, $ssd);
        file_put_contents(__DIR__ . "/backend/data.json", json_encode(Server::$server_data, JSON_PRETTY_PRINT));
      } else if (!empty($_POST["vm_name_delete"])) {
        Server::delete_server($_POST["vm_name_delete"]);
        file_put_contents(__DIR__ . "/backend/data.json", json_encode(Server::$server_data, JSON_PRETTY_PRINT));
      }
    }

    foreach (Server::$server_data as $server) {
      if ($server->id == 0) {
        $s_bar = array(
          "cpu" => ($server->max_cpu != 0) ? round(($server->used_cpu / $server->max_cpu) * 100, 2) : 0,
          "ram" => ($server->max_ram != 0) ? round(($server->used_ram / $server->max_ram) * 100, 2) : 0,
          "ssd" => ($server->max_ssd != 0) ? round(($server->used_ssd / $server->max_ssd) * 100, 2) : 0
        );
      }

      if ($server->id == 1) {
        $m_bar = array(
          "cpu" => ($server->max_cpu != 0) ? round(($server->used_cpu / $server->max_cpu) * 100, 2) : 0,
          "ram" => ($server->max_ram != 0) ? round(($server->used_ram / $server->max_ram) * 100, 2) : 0,
          "ssd" => ($server->max_ssd != 0) ? round(($server->used_ssd / $server->max_ssd) * 100, 2) : 0
        );
      }

      if ($server->id == 2) {
        $b_bar = array(
          "cpu" => ($server->max_cpu != 0) ? round(($server->used_cpu / $server->max_cpu) * 100, 2) : 0,
          "ram" => ($server->max_ram != 0) ? round(($server->used_ram / $server->max_ram) * 100, 2) : 0,
          "ssd" => ($server->max_ssd != 0) ? round(($server->used_ssd / $server->max_ssd) * 100, 2) : 0
        );
      }
    }
    ?>
    <div class="server">
      <!-- SMALL -->
      <div class="small">
        <h2 class="smallText centered_h2">Small</h2>
        <div class="diagrammSmall">
          <div class="bar cpu" style="height:<?= $s_bar['cpu'] ?>%;"><?= $s_bar['cpu'] ?>%</div>
          <div class="bar ram" style="height:<?= $s_bar['ram'] ?>%;"><?= $s_bar['ram'] ?>%</div>
          <div class="bar ssd" style="height:<?= $s_bar['ssd'] ?>%;"><?= $s_bar['ssd'] ?>%</div>
        </div>
        <div class="diagrammText">
          <h4 class="cpu">CPU</h4>
          <h4 class="ram">RAM</h4>
          <h4 class="ssd">SSD</h4>
        </div>
      </div>

      <!-- MEDIUM -->
      <div class="medium">
        <h2 class="mediumText centered_h2">Medium</h2>
        <div class="diagrammMedium">
          <div class="bar cpu" style="height:<?= $m_bar['cpu'] ?>%;"><?= $m_bar['cpu'] ?>%</div>
          <div class="bar ram" style="height:<?= $m_bar['ram'] ?>%;"><?= $m_bar['ram'] ?>%</div>
          <div class="bar ssd" style="height:<?= $m_bar['ssd'] ?>%;"><?= $m_bar['ssd'] ?>%</div>
        </div>
        <div class="diagrammText">
          <h4 class="cpu">CPU</h4>
          <h4 class="ram">RAM</h4>
          <h4 class="ssd">SSD</h4>
        </div>
      </div>

      <!-- BIG -->
      <div class="big">
        <h2 class="bigText centered_h2">Big</h2>
        <div class="diagrammBig">
          <div class="bar cpu" style="height:<?= $b_bar['cpu'] ?>%;"><?= $b_bar['cpu'] ?>%</div>
          <div class="bar ram" style="height:<?= $b_bar['ram'] ?>%;"><?= $b_bar['ram'] ?>%</div>
          <div class="bar ssd" style="height:<?= $b_bar['ssd'] ?>%;"><?= $b_bar['ssd'] ?>%</div>
        </div>
        <div class="diagrammText">
          <h4 class="cpu">CPU</h4>
          <h4 class="ram">RAM</h4>
          <h4 class="ssd">SSD</h4>
        </div>
      </div>
    </div>

    <div class="grid">

      <!-- VM anlegen -->
      <div class="card">
        <h2 class="centered_h2">Virtuelle Maschine mieten</h2>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
          <label for="vm_name">Name der Virtuellen Maschine</label>
          <input type="text" id="vm_name" name="vm_name" placeholder="z.B webserver-01 (max 30 zeichen)" required>

          <label for="cpu">Bedarf an Prozessoren (Kerne)</label>
          <select name="cpu" id="cpu" required>
            <option value="">Bitte wählen</option>
            <option value="1">1 Core (CHF 5)</option>
            <option value="2">2 Cores (CHF 10)</option>
            <option value="4">4 Cores (CHF 18)</option>
            <option value="8">8 Cores (CHF 30)</option>
            <option value="16">16 Cores (CHF 45)</option>
          </select>

          <label for="ram">Bedarf an Arbeitsspeicher in Gigabyte</label>
          <select name="ram" id="ram" required>
            <option value="">Bitte wählen</option>
            <option value="8">8 GB Arbeitsspeicher (CHF 10)</option>
            <option value="16">16 GB Arbeitsspeicher (CHF 20)</option>
            <option value="32">32 GB Arbeitsspeicher (CHF 40)</option>
            <option value="64">64 GB Arbeitsspeicher (CHF 80)</option>
            <option value="128">128 GB Arbeitsspeicher (CHF 160)</option>
          </select>

          <label for="ssd">Bedarf an Speicherplatz in Terabyte</label>
          <select name="ssd" id="ssd" required>
            <option value="">Bitte wählen</option>
            <option value="2">2 TB Speicher (CHF 20)</option>
            <option value="4">4 TB Speicher (CHF 40)</option>
            <option value="8">8 TB Speicher (CHF 80)</option>
            <option value="16">16 TB Speicher (CHF 160)</option>
            <option value="32">32 TB Speicher (CHF 320)</option>
          </select>

          <button type="submit">VM erstellen</button>
        </form>
      </div>

      <!-- VM lösche -->
      <div class="card" id="delete_card">
        <h2 class="centered_h2">Virtuelle Maschine löschen</h2>
        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
          <label for="vm_name_delete">Name der VM</label>
          <input type="text" id="vm_name_delete" name="vm_name_delete" placeholder="z.B webserver-01" required>

          <button type="submit" class="delete">VM löschen</button>
        </form>
      </div>

      <!-- Liste der VMs -->
      <div class="card" id="vm_table_div">

        <?php
        // Calculating server revenue used in Laufende VMs and Gesammtumsatz
        $total = 0;
        foreach (Server::$server_data as $server) {
          $total += $server->revenue;
        }
        $revenue_is_zero = ($total === 0) ? true : false;
        echo ($revenue_is_zero) ? "<h2 class='centered_h2'>Keine Laufenden Virtuellen Maschinen</h2>" : "<h2 id='gesammtumsatz_header'>Laufende Virtuelle Maschine(n)</h2>";
        echo (!$revenue_is_zero) ? "" : "<p class='large_p'>---<p>";
        ?>
        <?php
        if (!$revenue_is_zero) {
          echo '<table class="table">';
          echo "<tr>";
          echo  "<th>Name</th>";
          echo  "<th>CPU</th>";
          echo  "<th>RAM</th>";
          echo  "<th>SSD</th>";
          echo  "<th>Gewinn</th>";
          echo "</tr>";

          function get($vm, $prop): int
          {
            return is_array($vm) ? $vm[$prop] : $vm->$prop;
          }

          foreach (Server::$server_data as $server) {
            foreach ($server->vms as $vmname => $values) {
              $vm_used_cpu  = get($values, 'vm_used_cpu');
              $vm_used_ram  = get($values, 'vm_used_ram');
              $vm_used_ssd  = get($values, 'vm_used_ssd');
              $cost = get($values, 'cost');
              echo "<tr>";
              echo "<td>$vmname</td>";
              echo "<td>$vm_used_cpu GB</td>";
              echo "<td>$vm_used_ram GB</td>";
              echo "<td>$vm_used_ssd GB</td>";
              echo "<td>CHF $cost</td>";
              echo "</tr>";
            }
          }
        }          ?>
        </table>
      </div>

      <!-- Preis -->
      <div class="card">
        <h2 class="centered_h2">Gesamtumsatz pro Monat</h2>
        <?php
        $total = 0;
        foreach (Server::$server_data as $server) {
          $total += $server->revenue;
        }
        echo (!$revenue_is_zero) ? "<p class='large_p green_text'>CHF $total</p>" : "<p class='large_p'>---</p>";
        ?>
      </div>

    </div>
  </main>

  <footer>
    UberCloud
    Erstellt von:
    Luka Ilikj
    Lukas Thommen
    Dany El-Ali
    IMS Basel
  </footer>


</body>

</html>
