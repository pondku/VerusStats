<?php
// api ใส่เลขกระเป๋าเรา
$apiUrl = "https://luckpool.net/verus/miner/เลขกระเป๋า";
$response = file_get_contents($apiUrl);

// ฟังก์ชั่นสำหรับดึงข้อมูลราคา VRSC จาก API
function fetch_vrsc_price()
{
  $url = "https://api.coingecko.com/api/v3/simple/price?ids=verus-coin&vs_currencies=thb";

  // ดึงข้อมูลจาก API
  $res_coin = file_get_contents($url);

  // แปลงข้อมูล JSON เป็น array
  return json_decode($res_coin, true);
}

// ตั้งเวลาหมดอายุของข้อมูล (เช่น 10 นาที)
// $cache_duration = 10 * 60; // 10 นาที
$cache_duration = 10 * 60;

// ตั้งค่าชื่อไฟล์เก็บข้อมูล
$cache_file = 'vrsc_price_cache.json';

// ตรวจสอบว่าไฟล์ cache มีอยู่หรือไม่ และไม่เก่ากว่าเวลาที่กำหนด
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
  // ถ้าไฟล์ cache ยังไม่หมดอายุ ให้ใช้ข้อมูลจากไฟล์
  $data = json_decode(file_get_contents($cache_file), true);
} else {
  // ถ้าไฟล์ cache หมดอายุหรือไม่มีไฟล์ ให้ดึงข้อมูลใหม่จาก API
  $data = fetch_vrsc_price();

  // เก็บข้อมูลลงในไฟล์ cache
  file_put_contents($cache_file, json_encode($data));
}

// แสดงราคาของ VRSC ใน THB
$vrsc_price_in_thb = $data['verus-coin']['thb'];

if ($response === FALSE) {
  echo "<p class='text-danger'>Error: ไม่สามารถดึงข้อมูลจาก API ได้</p>";
  exit;
}

$data = json_decode($response, true);
if ($data === NULL) {
  echo "<p class='text-danger'>Error: ไม่สามารถแปลง JSON ได้</p>";
  exit;
}

// ฟังก์ชันการจัดเรียงตามชื่อ Worker
usort($data['workers'], function ($a, $b) {
  $nameA = explode(":", $a)[0];
  $nameB = explode(":", $b)[0];
  return strcmp($nameA, $nameB);
});

// นับจำนวน Online และ Offline
$onlineCount = 0;
$offlineCount = 0;

foreach ($data['workers'] as $workerData) {
  // แยกข้อมูลใน worker แต่ละตัวออกมา
  $workerInfo = explode(":", $workerData);

  // ตรวจสอบสถานะจากตำแหน่งที่ 3
  $status = $workerInfo[3] === "on" ? "Online" : "Offline";
  if ($status === "Online") {
    $onlineCount++;
  } else {
    $offlineCount++;
  }
}

// echo "Online: $onlineCount<br>";
// echo "Offline: $offlineCount<br>";
?>


<div class="container-fluid p-0">
  <h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>
  <div class="row">
    <div class="col-xl-12 col-xxl-12 d-flex">
      <div class="w-100">
        <div class="row">
          <!-- Card 1 -->
          <div class="col-sm-2">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">Hashrate (ปัจจุบัน)</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $data['hashrateString']; ?></h1>
              </div>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="col-sm-3">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">Balance</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $data['balance'] . " VRSC"; ?></h1>
              </div>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="col-sm-3">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">เหรียญในกระเป๋า</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $data['paid'] + 0.732495037 . " VRSC"; ?></h1>
                <!-- 0.732495037 = ส่วนต่างในกระเป๋าเรา(ดูจำนวนเหรียญในกระเป๋าแล้วลบด้วย Total Paid ใน Luckpool) -->
                <!-- จำนวนเหรียญใน LuckPool (Total Paid) + ส่วนต่างในกระเป๋าเรา(ดูจำนวนเหรียญในกระเป๋าแล้วลบด้วย Total Paid ใน Luckpool) จะได้จำนวนในกระเป๋าเรา -->
              </div>
            </div>
          </div>

          <!-- Card 4 -->
          <div class="col-sm-2">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">ราคาปัจจุบัน</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $vrsc_price_in_thb . " บาท" ?></h1>
              </div>
            </div>
          </div>

          <!-- Card 5 -->
          <div class="col-sm-2">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">ราคาทั้งหมด</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo number_format(($data['paid'] + 0.732495037) * $vrsc_price_in_thb, 2) . " บาท"; ?></h1>
                <!-- 0.732495037 = ส่วนต่างในกระเป๋าเรา(ดูจำนวนเหรียญในกระเป๋าแล้วลบด้วย Total Paid ใน Luckpool) -->
              </div>
            </div>
          </div>
        </div>


        <div class="row mt-4">
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">จำนวนเครื่องที่ทำงานทั้งหมด</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo count($data['workers']) . ' เครื่อง' ?></h1>
              </div>
            </div>
          </div>

          <!-- Card 5 -->
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">Online</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $onlineCount . " เครื่อง" ?></h1>
              </div>
            </div>
          </div>

          <!-- Card 6 -->
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col mt-0">
                    <h5 class="card-title">Offline</h5>
                  </div>
                </div>
                <h1 class="mt-1 mb-3"><?php echo $offlineCount . " เครื่อง" ?></h1>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-12 col-lg-12 col-xxl-12 d-flex">
      <div class="card flex-fill">
        <div class="card-header">

          <h5 class="card-title mb-0">Workers</h5>
        </div>
        <table class="table table-hover my-0">
          <thead>
            <tr>
              <th>ลำดับ</th>
              <th>ชื่อ</th>
              <th class="d-none d-xl-table-cell">แรงขุด</th>
              <th>Shares</th>
              <th>สถานะ</th>
              <th class="d-none d-md-table-cell">แท็ก</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $index = 1;
            $totalWorkers = count($data['workers']);

            echo "<p>จำนวนทั้งหมด: {$totalWorkers} รายการ</p>";
            foreach ($data['workers'] as $worker) {
              $workerData = explode(":", $worker);
              $status = $workerData[3] === "on" ? "Online" : "Offline";
              $statusClass = $workerData[3] === "on" ? "badge bg-success" : "badge bg-danger";
              echo "<tr>
                <td>{$index}</td>
                <td class='d-none d-xl-table-cell'>{$workerData[0]}</td>
                <td class='d-none d-xl-table-cell'>" . number_format($workerData[1]) . " H/s</td>
                <td class='d-none d-xl-table-cell'>{$workerData[2]}</td>
                <td><span class='{$statusClass}'>{$status}</span></td>
                <td class='d-none d-xl-table-cell'>{$workerData[4]}</td>
            </tr>";
              $index++;
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>