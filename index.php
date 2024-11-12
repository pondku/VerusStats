<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/" />

	<title>กระเป๋า VERUS</title>

	<link href="css/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
	<div class="wrapper">
		<div class="main">
			<main class="content">

				<div id="miningData">
					<?php include 'fetch_miner_data.php'; ?>
				</div>

				<script>
					function fetchMiningData() {
						// สร้าง AJAX request
						var xhr = new XMLHttpRequest();
						xhr.open('GET', 'fetch_miner_data.php', true);
						xhr.onload = function() {
							if (xhr.status === 200) {
								// อัปเดตเนื้อหาของ div ด้วยข้อมูลใหม่
								document.getElementById('miningData').innerHTML = xhr.responseText;
							}
						};
						xhr.send();
					}

					// เรียกฟังก์ชัน fetchMiningData ทุก ๆ 10 วินาที (10000 มิลลิวินาที)
					setInterval(fetchMiningData, 10000);
				</script>
			</main>
			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a class="text-muted" href="https://adminkit.io/" target="_blank"><strong>THEGHOSTMAN</strong></a>
							</p>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
</body>
<script src="js/app.js"></script>

</html>