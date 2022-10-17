<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
if(isset($_SESSION['sNPK'])){
    $sNPK       = $_SESSION['sNPK'];
    if($sNPK != ''){
        $temp       = $_FILES['browseFile']['tmp_name'];
        $fileName   = json_encode(basename($_FILES['browseFile']['name']));
		$fileName   = preg_replace('/\s+/', ' ', $fileName);   
        $fileType   = $_FILES['browseFile']['type'];
        $fileSize   = $_FILES['browseFile']['size'];
        $prNo       = $_POST['prNo'];
        $oldPrNo    = $_POST['oldPrNoVal'];
        $actionPr   = $_POST['actionPrVal'];
        $userId     = $_POST['userIdLogin'];
        if($actionPr == 'Crea' || $actionPr == 'Uplo' || $actionPr == 'Repl'){
            if($oldPrNo != $prNo){
                $dirNameTemp= $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$oldPrNo.'-'.$userId.'-temp/';
                $fileTemp   = $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/" .$oldPrNo.'-'.$userId."-temp/".$_FILES['browseFile']['name']; 
				$newFileTemp= $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/".$oldPrNo.'-'.$userId."-temp/".preg_replace('/\s+/', ' ', $_FILES['browseFile']['name']); 
			}else{
                $dirNameTemp= $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo.'-'.$userId.'-temp/';
                $fileTemp   = $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/" .$prNo.'-'.$userId."-temp/".$_FILES['browseFile']['name']; 
				$newFileTemp= $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/".$prNo.'-'.$userId."-temp/".preg_replace('/\s+/', ' ', $_FILES['browseFile']['name']); 
            }
            
        }else{
            $dirNameTemp= $_SERVER['DOCUMENT_ROOT']."/EPS/db/Attachment/Temporary/".$prNo.'-temp/';
            $fileTemp   = $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/" .$prNo."-temp/".$_FILES['browseFile']['name']; 
			$newFileTemp= $_SERVER['DOCUMENT_ROOT']. "/EPS/db/Attachment/Temporary/".$prNo."-temp/".preg_replace('/\s+/', ' ', $_FILES['browseFile']['name']); 
        }

        // Create directory
        if(!is_dir($dirNameTemp)){    
            mkdir($dirNameTemp);
        }
        if(strlen($fileName) < 200){
            if($fileType == "image/pjpeg" || $fileType == "image/jpeg" || $fileType=="image/png"
                    || $fileType=="image/x-png" || $fileType=="image/gif" || $fileType=="image/tiff" 
                    || $fileType=="application/pdf" || $fileType=="application/vnd.ms-powerpoint"
                    || $fileType=="application/vnd.ms-excel" || $fileType=="application/msword"
					|| $fileType=="application/vnd.openxmlformats-officedocument.presentationml.presentation"
					|| $fileType=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
					|| $fileType=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
                if($fileSize<2000000){
                    if(!file_exists($fileTemp)){
                            move_uploaded_file($temp,$fileTemp);
                            if($fileSize>999999){ //IF GREATER THAN 999KB, DISPLAY AS MB 
                                $theDiv=$fileSize/1000000;
                                $fileSize=round($theDiv,1)." MB";
                            }
                            else{
                                $theDiv=$fileSize/1000;
                                $fileSize=round($theDiv,1)." KB";
                            }
							rename($fileTemp,$newFileTemp);
                            echo '{success:true, msg: '.json_encode(array('message'=>'Success')).', 
                                    fileName:'.$fileName.', fileType:'.json_encode($fileType).', 
                                    fileSize:'.json_encode($fileSize).', temp:'.json_encode($temp).'}';
                        }
                        else{
                            echo '{success:true, msg:'.json_encode(array('message'=>'ErrorDuplicate')).'}';
                        }
                }
                else{
                    echo '{success:true, msg:'.json_encode(array('message'=>'ErrorFileSize')).'}';
                }
            }
            else{
                echo '{success:true, msg:'.json_encode(array('message'=>'ErrorFileType')).'}';
            }
        }
        else{
            echo '{success:true, msg:'.json_encode(array('message'=>'ErrorLengthFileName')).'}';
        }   
    }else{
        echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
    }
}else{
    echo '{success: true, msg:'.json_encode(array('message'=>'SessionTimeout')).'}';
}
?>
