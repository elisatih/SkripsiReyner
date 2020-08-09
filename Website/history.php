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

	foreach($sensors as $key => $value){
		$getData = mysqli_query($conn, "SELECT * FROM data_sensor WHERE idSensor='$key' order by waktu_tanggal desc"); 
		
		$data[$key]=array();
		while($row= mysqli_fetch_array($getData)){
			// echo var_dump($row);
			array_push($data[$key], $row);
			array_push($dataNodeAll, $row);
		}
	}

	$getData2 = mysqli_query($conn, "SELECT * FROM data_sensor"); 
	$allNodeData = array();
	while($row= mysqli_fetch_assoc($getData2)){
		$allNodeData[$sensors[$row['idSensor']]][] = $row;
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
		<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
 		<script type="text/javascript" src="DataTables/datatables.min.js"></script>
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
		<li class="nav-item active">
			<a class="nav-link" href="read_db.php">History</span></a>
		</li>
		<li class="nav-item ">
			<a class="nav-link" href="grafik.php">Grafik <span class="sr-only">(current)</span></a>
		</li>
		</ul>
	</div>
	</nav>
	<h1 class="text-center">HISTORY</h1>
	<div class="w-100 d-flex justify-content-center">
		<input type="radio" id="nodeAll" name="status" value="nodeAll">
		<label class="mr-3" for="nodeAll">Keseluruhan</label>
		<?php 
			foreach($sensors as $key=>$value){
				?>
				<input type="radio" id="<?php echo $value?>" name="status" value="<?php echo $value ?>">
				<label class="mr-3" for="<?php echo $key?>"><?php echo $value?></label><br>
				<?php
			}
		?>

	</div>
		<table class="datatables table-history table table-bordered table-striped">
			<thead>
				<tr>
					<th>waktu & tanggal</th>
					<th>idSensor</th> 
					<th>pH</th> 
					<th>Suhu</th>
					<th>kekeruhan</th>
				</tr>	
			</thead>
			<tbody></tbody>
		</table>

		<script>
			$(document).ready(function(){
				var allNodeData = <?php echo json_encode($allNodeData);?>;
				console.log(allNodeData);
				
				$(".datatables").DataTable();
				
				$("input[type='radio']").click(function(){
        			var radioValue = $("input[name='status']:checked").val();
					var table = $('.datatables').DataTable();
					table.clear().draw();
					if(radioValue === "nodeAll") {
						for(node in allNodeData) {
							allNodeData[node].forEach(function(item) {
								table.row.add([item['waktu_tanggal'], item['idSensor'],item['nilai_pH'],item['nilai_suhu'],item['nilai_kekeruhan']]).draw();
							});
						};
					} else {
						var nodeData = allNodeData[radioValue];
						nodeData.forEach(function(item){						
							table.row.add([item['waktu_tanggal'], item['idSensor'],item['nilai_pH'],item['nilai_suhu'],item['nilai_kekeruhan']]).draw();
						})
					}
        		});
			});

		</script>
	</body>
</html>