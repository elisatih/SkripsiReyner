<!DOCTYPE html>
<?php
	//Creates new record as per request
	//Connect to database
	$hostname = "localhost";		//example = localhost or 192.168.0.0
	$username = "root";		//example = root
	$password = "";	
	$dbname = "skripsi";
	// Create connection
	$conn = mysqli_connect($hostname, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
		die("Connection failed !!!");
	} 

	$getData = mysqli_query($conn, "SELECT * FROM sensor"); 
	$sensors=array();
	$location=array();
	$data=array();
	$dataNodeAll=array();
	while($row= mysqli_fetch_array($getData)){
		$sensors[$row['idSensor']]=$row['namaSensor'];
		$location[$row['idSensor']]=$row['lokasi'];
	} 

	$dblabels = array();
	$pHnode = array();
	$suhunode = array();
	$kekeruhannode = array();
	$getData2 = mysqli_query($conn, "SELECT * FROM data_sensor order by waktu_tanggal desc"); 
	$allNodeData = array();
	$counter = array();
	while($row= mysqli_fetch_assoc($getData2)){
		if(!array_key_exists($sensors[$row['idSensor']], $counter)){
			$counter[$sensors[$row['idSensor']]]=0;
		}
		if($counter[$sensors[$row['idSensor']]]<7){
			$allNodeData[$sensors[$row['idSensor']]][] = $row;
			$dblabels[$sensors[$row['idSensor']]][]=$row['waktu_tanggal'];
			$pHnode[$sensors[$row['idSensor']]][]=$row['nilai_pH'];
			$suhunode[$sensors[$row['idSensor']]][]=$row['nilai_suhu'];
			$kekeruhannode[$sensors[$row['idSensor']]][]=$row['nilai_kekeruhan'];
			$counter[$sensors[$row['idSensor']]]+=1;
		}
	}

	$avgPerJam = array();
	$avgPerHari = array();

	foreach($sensors as $id => $sensor) {
		$query_hour = "SELECT idSensor, AVG( `nilai_pH` ) as avg_pH , AVG(`nilai_suhu`) as avg_suhu, AVG(`nilai_kekeruhan`) as avg_kekeruhan, YEAR(`waktu_tanggal`) as year, MONTH(`waktu_tanggal`) as month, DAY( `waktu_tanggal` ) as day, HOUR( `waktu_tanggal` ) as hour FROM data_sensor WHERE DATE_SUB(  `waktu_tanggal` , INTERVAL 1 HOUR ) AND `idSensor`='$id' GROUP BY HOUR( `waktu_tanggal` ) limit 7";
		$query_day = "SELECT idSensor, AVG( `nilai_pH` ) as avg_pH , AVG(`nilai_suhu`) as avg_suhu, AVG(`nilai_kekeruhan`) as avg_kekeruhan, YEAR(`waktu_tanggal`) as year, MONTH(`waktu_tanggal`) as month, DAY( `waktu_tanggal` ) as day FROM data_sensor WHERE DATE_SUB(  `waktu_tanggal` , INTERVAL 1 DAY ) AND `idSensor`='$id' GROUP BY DAY( `waktu_tanggal` ) limit 7";


		$getAvgHour = mysqli_query($conn, $query_hour); 
		$getAvgDay = mysqli_query($conn, $query_day);

		while($row= mysqli_fetch_assoc($getAvgHour)){
			$avgPerJam[$sensors[$row['idSensor']]][] = array(
				'waktu_tanggal' => $row['year'] . "-" . $row['month'] . "-" . $row['day'] . " " . $row['hour'] . ":00:00",
				'nilai_pH' => $row['avg_pH'],
				'nilai_suhu' => $row['avg_suhu'],
				'nilai_kekeruhan' => $row['avg_kekeruhan']
			);
			$avgHour_dblabels[$sensors[$row['idSensor']]][]=$row['year'] . "-" . $row['month'] . "-" . $row['day'] . " " . $row['hour'] . ":00:00";
			$avgHour_pHnode[$sensors[$row['idSensor']]][]=$row['avg_pH'];
			$avgHour_suhunode[$sensors[$row['idSensor']]][]=$row['avg_suhu'];
			$avgHour_kekeruhannode[$sensors[$row['idSensor']]][]=$row['avg_kekeruhan'];
		}

		while($row= mysqli_fetch_assoc($getAvgDay)){
			$avgPerHari[$sensors[$row['idSensor']]][] = array(
				'waktu_tanggal' => $row['year'] . "-" . $row['month'] . "-" . $row['day'],
				'nilai_pH' => $row['avg_pH'],
				'nilai_suhu' => $row['avg_suhu'],
				'nilai_kekeruhan' => $row['avg_kekeruhan']
			);
			$avgDay_dblabels[$sensors[$row['idSensor']]][]=$row['year'] . "-" . $row['month'] . "-" . $row['day'];
			$avgDay_pHnode[$sensors[$row['idSensor']]][]=$row['avg_pH'];
			$avgDay_suhunode[$sensors[$row['idSensor']]][]=$row['avg_suhu'];
			$avgDay_kekeruhannode[$sensors[$row['idSensor']]][]=$row['avg_kekeruhan'];
		}
	}
	
?>
<html>
	<head>
		<style>
			table {
				border-collapse: collapse;
				width: 100%;
				color: #1f5380;
				font-family: monospace;
				font-size: 20px;
				text-align: left;
			} 
			th {
				background-color: #1f5380;
				color: white;
			}
			tr:nth-child(even) {background-color: #f2f2f2}
		</style>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
	</head>
	
	<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		<li class="nav-item">
			<a class="nav-link" href="home.php">Home</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="history.php">History</span></a>
		</li>
		<li class="nav-item active">
			<a class="nav-link" href="grafik.php">Grafik <span class="sr-only">(current)</span></a>
		</li>
		</ul>
	</div>
	</nav>
	<h1 class="text-center">GRAFIK</h1>
	
	<select id="avg_filter" class="form-control form-control-sm">
		<option value="None" disabled selected>-- Select Filter --</option>
		<option value="menit">Per Menit</option>
		<option value="jam">Per Jam</option>
		<option value="hari">Per Hari</option>
	</select>
	
		<?php
			foreach($dblabels as $key => $value){
				?>
				<div style="margin-bottom:20px">
					<h4 class="text-center"><?php echo $key;?></h4>
					<div style="display:inline-block; width:31%; margin-left:10px; margin-right:10px;">
						<canvas id="pH-<?php echo $key;?>" width="80px" height="45px"></canvas>
					</div>
					<div style="display:inline-block; width:31%; margin-left:10px; margin-right:10px;">
						<canvas id="suhu-<?php echo $key;?>" width="80px" height="45px"></canvas>
					</div>
					<div style="display:inline-block; width:31%; margin-left:10px; margin-right:10px;">
						<canvas id="kekeruhan-<?php echo $key;?>" width="80px" height="45px"></canvas>
					</div>
				</div>
				<?php
			}
		?>	


	<script>
	jQuery(document).ready(function($) {
		console.log($("#avg_filter").val());

		$("#avg_filter").change(function() {
			let filter = $(this).val();

			let data_labels;
			let data_pH;
			let data_suhu;
			let data_kekeruhan;

			if(filter === "menit") {
				data_labels = <?php echo json_encode($dblabels);?>;
				data_pH = <?php echo json_encode($pHnode);?>;
				data_suhu = <?php echo json_encode($suhunode);?>;
				data_kekeruhan = <?php echo json_encode($kekeruhannode);?>;
			} else if(filter === "jam") {
				data_labels = <?php echo json_encode($avgHour_dblabels);?>;
				data_pH = <?php echo json_encode($avgHour_pHnode);?>;
				data_suhu = <?php echo json_encode($avgHour_suhunode);?>;
				data_kekeruhan = <?php echo json_encode($avgHour_kekeruhannode);?>;
			} else if(filter === "hari") {
				data_labels = <?php echo json_encode($avgDay_dblabels);?>;
				data_pH = <?php echo json_encode($avgDay_pHnode);?>;
				data_suhu = <?php echo json_encode($avgDay_suhunode);?>;
				data_kekeruhan = <?php echo json_encode($avgDay_kekeruhannode);?>;
			}


			var chart_pH=[];
			var chart_suhu=[];

			for (const key in data_labels) {
				var warna;
				if(key == "Node a"){
					warna= "#3e95cd";
				}
				else if(key == "Node b"){
					warna= "#eb3434";
				}

				if(chart_pH[key]){
					chart_pH[key].destory();
				}else if(chart_suhu[key]){
					chart_suhu[key].destroy();
				}


				chart_pH[key] = new Chart(document.getElementById("pH-"+key), {
				type: 'line',
				data: {
					labels: data_labels[key],
					datasets: [{ 
						data: data_pH[key],
						label: key,
						borderColor: warna,
						fill: false
					}
					]
				},
				options: {
					title: {
					display: true,
					text: 'pH'
					},
					events:[]
				}
				});

				console.log(chart_pH[key]);
				

				chart_suhu[key] = new Chart(document.getElementById("suhu-"+key), {
				type: 'line',
				data: {
					labels: data_labels[key],
					datasets: [{ 
						data: data_suhu[key],
						label: key,
						borderColor: warna,
						fill: false
					}
					]
				},
				options: {
					title: {
					display: true,
					text: 'Suhu'
					},
					events:[]
				}
				});
				
				var chart_kekeruhan = new Chart(document.getElementById("kekeruhan-"+key), {
				type: 'line',
				data: {
					labels: data_labels[key],
					datasets: [{ 
						data: data_kekeruhan[key],
						label: key,
						borderColor: warna,
						fill: false
					}
					]
				},
				options: {
					title: {
					display: true,
					text: 'Kekeruhan'
					},
					events:[]
				}
				});
			}


			
			<?php
			foreach($dblabels as $key => $value){
			?>
				"<?php echo $key;?>", <?php echo json_encode($value);?>;
				
				<?php
					
				}
			?>
		});
	});
	</script>
	</body>
</html>