<!DOCTYPE html>
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

			.green {
				color:green;
			}

			.red {
				color:red;
			}
		</style>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<meta http-equiv="refresh" content="60">
	</head>
	
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
	while($row= mysqli_fetch_array($getData)){
		$sensors[$row['idSensor']]=$row['namaSensor'];
		$location[$row['idSensor']]=$row['lokasi'];
	} 

	?>
	<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		<li class="nav-item active">
			<a class="nav-link" href="home.php">Home</span></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="history.php">History</span></a>
		</li>
		<li class="nav-item ">
			<a class="nav-link" href="grafik.php">Grafik <span class="sr-only">(current)</span></a>
		</li>
		</ul>
	</div>

	<!-- Button trigger modal -->
	<button id="btnTambahNode" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
		Tambah Node
	</button>

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Tambah Sensor</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<form>
			<div class="form-group">
				<label for="idSensor">ID Sensor</label>
				<input type="text" class="form-control" id="idSensor" placeholder="ID Sensor">
			</div>
			<div class="form-group">
				<label for="namaSensor">Nama Sensor</label>
				<input type="text" class="form-control" id="namaSensor" placeholder="Nama Sensor">
			</div>
			<div class="form-group">
				<label for="lokasiSensor">Lokasi</label>
				<input type="text" class="form-control" id="lokasiSensor" placeholder="Lokasi Sensor">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			<button id="tambahNode" type="button" class="btn btn-primary">Tambah</button>
		</div>
		</div>
	</div>
	</div>
	</nav>
		<h1 class="text-center">Pemantauan hari ini</h1>
		<h2 id="tanggalHariIni" class="text-center" style="margin-bottom: 3em !important"></h2>
	</div>

	<?php
		foreach($sensors as $key => $value){
			?>
			<h5 class="text-center"><?php echo $value;?></h5>
			<h6 class="text-center"><?php echo $location[$key]?></h6>
			<div class="w-100 m-auto d-flex justify-content-center" style="margin-top:3em!important;margin-bottom:3em !important">
				<div class="card" style="width: 18rem; display: inline-block">
					<div class="card-body">
						<h5 class="card-title">Suhu</h5>
							<?php
							$table = mysqli_query($conn, "SELECT nilai_suhu FROM data_sensor where idSensor='$key' ORDER BY waktu_tanggal DESC LIMIT 1"); 
							while($row = mysqli_fetch_array($table))
							{
						?>
						<div id="suhu=<?php echo $value;?>" class="suhu"><?php echo $row['nilai_suhu']; ?></div>
						<?php
							}
						?>
						<p class="card-text"> &#176 C</p>
					</div>
				</div>

				<div class="card" style="width: 18rem; display: inline-block">
					<div class="card-body">
						<h5 class="card-title">pH</h5>
							<?php
							$table = mysqli_query($conn, "SELECT nilai_pH FROM data_sensor where idSensor='$key' ORDER BY waktu_tanggal DESC LIMIT 1"); 
							while($row = mysqli_fetch_array($table))
							{
						?>
						<div id="pH=<?php echo $value;?>" class="pH"><?php echo $row['nilai_pH']; ?></div>
						<?php
							}
						?>
						<p class="card-text">Batas : 6.5 - 9.0</p>
					</div>
				</div>

				<div class="card" style="width: 18rem; display: inline-block">
					<div class="card-body">
						<h5 class="card-title">Kekeruhan</h5>
							<?php
							$table = mysqli_query($conn, "SELECT nilai_kekeruhan FROM data_sensor where idSensor='$key' ORDER BY waktu_tanggal DESC LIMIT 1");
							while($row = mysqli_fetch_array($table))
							{
						?>
						<span id="kekeruhan=<?php echo $value;?>" class="kekeruhan"><?php echo $row['nilai_kekeruhan']; ?></span><span> v </span>
						<p class="card-text kategoriKekeruhan"></p>
						<?php
							}
						?>
						
					</div>
				</div>
			</div>
			<?php
		}
	?>

	</body>
	<script>
		$(document).ready(function(){
			var d = new Date();
			var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

			
			var date=d.getDate();
			var month=months[d.getMonth()];
			var year=d.getFullYear();
			var hour=d.getHours();
			var minute=d.getMinutes();
			var second=d.getSeconds();

			$("#tanggalHariIni").html(date+" "+month+" "+year+" "+hour+":"+minute+":"+second);	


			$(".pH").each(function(){
				if($(this).html()>=6.5 && $(this).html()<=9.0){
					$(this).addClass("green");
				}
				else{
					$(this).addClass("red");
				}
			})

			$(".kekeruhan").each(function(){
				if($(this).html()<=1.4){
					$(this).addClass("red");
				}
				else if($(this).html()>1.4 && $(this).html()<=3.1){
					$(this).addClass("text-warning");
				}
				else if($(this).html()>3.1 && $(this).html()<=3.5){
					$(this).addClass("green");
				}
				else if($(this).html()>3.5){
					$(this).addClass("green");
				}
			})
			$(".kategoriKekeruhan").each(function(){
				var parent=$(this).parent();
				if(parent.find(".kekeruhan").html()<=1.4){
					$(this).html("keruh");
				}
				else if(parent.find(".kekeruhan").html()>1.4 && parent.find(".kekeruhan").html()<=3.1){
					$(this).html("cukup");
				}
				else if(parent.find(".kekeruhan").html()>3.1 && parent.find(".kekeruhan").html()<=3.5){
					$(this).html("baik");
				}
				else if(parent.find(".kekeruhan").html()>3.5){
					$(this).html("jernih");
				}
			})
			$("#btnTambahNode").click(function(e){
				e.preventDefault();
			})
			$("#tambahNode").click(function(){
				jQuery.ajax({
					type:'POST',
					url:'ajax.php',
					data:{
						idSensor:$('#idSensor').val(),
						namaSensor:$("#namaSensor").val(),
						lokasiSensor:$("#lokasiSensor").val(),
						tambah:1
					},
					success:function(res){
						res=JSON.parse(res);
						if(res.status=="Error"){
							alert(res.messages);
						}
						else if (res.status=="Ok"){
							alert("Berhasil menambah node ke database.");
							location.reload();
						}
						
						
					}

				})
			})
		});

	</script>
</html>