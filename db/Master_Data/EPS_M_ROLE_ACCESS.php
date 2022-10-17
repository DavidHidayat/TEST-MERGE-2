<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
$userId = $_SESSION['sNPK'];

$json = '['; // start the json array element
$json_names = array();
$query = "select     
            EPS_M_ROLE_ACCESS.COMPONENT_NAME
            ,EPS_M_USER.USERID
          from         
            EPS_M_USER 
          left join
            EPS_M_ROLE_ACCESS 
          on 
            EPS_M_ROLE_ACCESS.ROLE_ID = EPS_M_USER.ROLE_ID
          where     
            (EPS_M_USER.USERID = '$userId')";
$sql = $conn->query($query);
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $componentName = $row['COMPONENT_NAME'];
    $json_names[] = "{ COMPONENT_NAME: '$componentName'
                     }";
}
$json .= implode(',', $json_names); // join the objects by commas;
$json .= ']'; // end the json array element
echo '{success:true, rows:'.$json.'}';
    
?>
