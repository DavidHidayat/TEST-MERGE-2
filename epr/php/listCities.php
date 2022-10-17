<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
	//chama o arquivo de conexão com o bd
	

	$stateId = $_REQUEST['stateId'];

//	$queryString = "SELECT * FROM EPS_M_CITY WHERE state_id = " . $stateId;
//	$queryString = "SELECT * FROM EPS_M_CITY WHERE state_id = " . $stateId;

	//$query = "SELECT * FROM EPS_M_CITY WHERE state_id = " . "$stateId";
	$query = "SELECT * FROM EPS_M_CITY WHERE state_id";
    $sql = $conn->query($query);
    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

    if ($row>0){
        echo '{success:true, data:'.json_encode($row).'}';
    }else{
        echo '{success:false}';
    }
?>