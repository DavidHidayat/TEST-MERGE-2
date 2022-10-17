<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
if(isset($_SESSION['sNPK']))
{      
    $sNPK       = $_SESSION['sNPK'];
    $sNama      = $_SESSION['sNama'];
    $sBunit     = $_SESSION['sBunit'];
    $sSeksi     = $_SESSION['sSeksi'];
    $sKdper     = $_SESSION['sKdper'];
    $sNmPer     = $_SESSION['sNmper'];
    $sKdPlant   = $_SESSION['sKDPL'];
    $sNmPlant   = $_SESSION['sNMPL'];
    $sRoleId    = $_SESSION['sRoleId'];
    $sInet      = $_SESSION['sinet'];
    $sNotes     = $_SESSION['snotes'];
    $sUserId    = $_SESSION['sUserId'];
    $sBuLogin   = $_SESSION['sBuLogin'];
    $sUserType  = $_SESSION['sUserType'];
    $action     = 'Edit';
    
}else{	
?>
    <script language="javascript"> alert("Sorry, you are not authorized to access this page.");
    document.location="../db/Login/Logout.php"; </script>
<?
}
$criteria       = trim($_GET['criteria']);
$prNo           = strtoupper(trim($_GET['prNoPrm']));
$refItemName    = strtoupper(trim($_GET['refItemNamePrm']));
$refItemName    = str_replace("'", "''", $refItemName);
$refItemName    = stripslashes($refItemName);

if($criteria == 'AttachmentPRHeader'){
    $htmlTable      = 
        "
                    <table class='table table-striped table-bordered' id='table-attach-item'>
                        <thead>
                            <th>ITEM NAME</th>
                            <th>FILE NAME</th>
                            <th>SIZE</th>
                            <th>TYPE</th>
                        </thead>
                        <tbody>";
    $query = "select 
                PR_NO
                ,ITEM_CD
                ,ITEM_NAME
                ,FILE_NAME
                ,FILE_TYPE
                ,FILE_SIZE
            from 
                EPS_T_PR_ATTACHMENT
            where
                PR_NO = '$prNo'";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNoVal       = $row['PR_NO'];
        $itemCdVal     = $row['ITEM_CD'];
        $itemNameVal   = $row['ITEM_NAME'];
        $fileNameVal   = $row['FILE_NAME'];
        $fileTypeVal   = $row['FILE_TYPE'];
        $fileSizeVal   = $row['FILE_SIZE'];
        $htmlTable .= "<tr>
                    <td>$itemNameVal</td>
                    <td>
                        <a href='../db/Attachment/Fixed/$prNoVal/$fileNameVal' target='_blank' class='attachment-link'>$fileNameVal</a>
                    </td>
                    <td>$fileSizeVal</td>
                    <td>$fileTypeVal</td>
                </tr>";
    }
    $htmlTable      .= "</tbody>
                    </table>";
}
if($criteria == 'AttachmentPRItem'){
    $htmlTable      = 
        "
                    <table class='table table-striped table-bordered' id='table-attach-item'>
                        <thead>
                            <th>FILE NAME</th>
                            <th>SIZE</th>
                            <th>TYPE</th>
                        </thead>
                        <tbody>";
    $query = "select 
                PR_NO
                ,ITEM_CD
                ,ITEM_NAME
                ,FILE_NAME
                ,FILE_TYPE
                ,FILE_SIZE
            from 
                EPS_T_PR_ATTACHMENT
            where
                PR_NO = '$prNo'";
//				and replace(replace(replace(ITEM_NAME, char(13), ''), char(9), ''), ' ', '') = replace('$refItemName', ' ', '')";
    $sql = $conn->query($query);
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $prNoVal       = $row['PR_NO'];
        $itemCdVal     = $row['ITEM_CD'];
        $itemNameVal   = $row['ITEM_NAME'];
        $fileNameVal   = $row['FILE_NAME'];
        $fileTypeVal   = $row['FILE_TYPE'];
        $fileSizeVal   = $row['FILE_SIZE'];
        $url = "../db/Attachment/Fixed/$prNoVal/$fileNameVal";
        $htmlTable .= "<tr>
                    <td>
                        <a href='$url' target='_blank' class='attachment-link'>$fileNameVal</a>
                    </td>
                    <td>$fileSizeVal</td>
                    <td>$fileTypeVal</td>
                </tr>";
    }
    $htmlTable      .= "</tbody>
                    </table>";
}

echo $htmlTable; 
?>
