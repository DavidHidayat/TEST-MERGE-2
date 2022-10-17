<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
if(isset($_SESSION['sNPK'])){   
    $sNPK       = $_SESSION['sNPK'];
    if($sNPK != ''){
        $uploadId       = $_GET['uploadId'];
        $userId         = $_POST['userIdLoginVal'];
        $prNo           = $_POST['prNo'];
        $oldPrNo        = $_POST['oldPrNoVal'];
        $itemAttachment = $_POST['itemAttachment'];
        $actionPr       = $_POST['actionPrVal'];

        if(isset($_GET['action'])){
            $action= $_GET['action'];
        }
        if(isset($_GET['action2'])){
            $action2= $_GET['action2'];
        }
        if($action=='attachmentItem'){
            $itemAttachment = json_decode(stripslashes($itemAttachment));
            for($x=0 ; $x < count($itemAttachment); $x++){
                $fileName = $itemAttachment[$x][0];
                if($actionPr == 'Crea' || $actionPr == 'Uplo' || $actionPr == 'Repl'){
                    if($oldPrNo != $prNo){
                        unlink($_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$oldPrNo.'-'.$userId.'-temp/'.$fileName);
                    }else{
                        unlink($_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$prNo.'-'.$userId.'-temp/'.$fileName);
                    }
                }else{
                    unlink($_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$prNo.'-temp/'.$fileName);
                }
            }
        }
        if($action=='attachmentPr'){
            if($actionPr == 'Crea' || $actionPr == 'Uplo' || $actionPr == 'Repl'){
                if($oldPrNo != $prNo){
                    $dirNameTemp = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$oldPrNo.'-'.$userId.'-temp/';
                }else{
                    $dirNameTemp = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$prNo.'-'.$userId.'-temp/';
                }
            }else{
                $dirNameTemp = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Attachment/Temporary/".$prNo.'-temp/';
            }
            if(is_dir($dirNameTemp)){
                $files = scandir($dirNameTemp);
                unset($files[array_search(".",$files)]);
                unset($files[array_search("..",$files)]);

                if(count($files) == 0) {
					@unlink($dirNameTemp.'Thumbs.db');
                    rmdir($dirNameTemp);
                }else{
                    $dh = opendir($dirNameTemp);
                    while($file = readdir($dh)){
                        if(!is_dir($file)){
                            @unlink($dirNameTemp.$file);
                        }
                    }
                    @unlink($dirNameTemp.'Thumbs.db');
                    closedir($dh);
                    rmdir($dirNameTemp);
                }
            }
        }
        if($action2=='deleteUpload'){
            $query = "delete from EPS_T_PR_UPLOAD where UPLOAD_ID='$uploadId'";
            $sql = $conn->query($query);
        }
    }else{
        echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
    }
}else{
    echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
}
?>
