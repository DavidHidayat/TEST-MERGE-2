<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != '')
    { 
        $prAttachment           = array();
        $prAttachmentTemp       = array();
        
        $prAttachment           = ($_SESSION['prAttachment']);
        $fileName               = basename($_FILES['uploadfile']['name']);
        $fileTemp               = $_FILES['uploadfile']['tmp_name'];
        $fileType               = $_FILES['uploadfile']['type'];
        $fileSize               = $_FILES['uploadfile']['size'];
        $itemNameFile           = $_POST['itemNameFile'];
        $itemNameFile           = strtoupper(trim($itemNameFile));
        $itemNameFile           = str_replace("'", "''", $itemNameFile);
        $itemNameFile           = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameFile);
        $itemNameFile           = preg_replace('/\s+/', ' ',$itemNameFile);
        $itemNameFile           = stripslashes($itemNameFile);
        $itemCdFile             = $_POST['itemCdFile'];
        $btnPrm                 = strtoupper($_POST['btnPrm']);
        $actionPrm              = strtoupper($_POST['actionPrm']);
        $prNoPrm                = $_POST['prNoPrm'];
        
        $currentMonth           = date(Ymd);
        $uploadDir              = $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/ATTACHMENT/";
        $uploadDirTemp          = $uploadDir."TEMPORARY/";
        $dirByDateTemp          = $uploadDirTemp.$currentMonth."/";
        $dirByUserTemp          = $dirByDateTemp.$sUserId."/";   
        $dirByPrTemp            = $uploadDirTemp.$prNoPrm."/";

        if($actionPrm == "CREATE" || $actionPrm == "EDIT" || $actionPrm == "REPLICATE" || $actionPrm == "APPROVAL")
        {
            if($actionPrm == "CREATE" || $actionPrm == "REPLICATE")
            {
                // Create directory by current date
                if(!is_dir($dirByDateTemp))
                {    
                    mkdir($dirByDateTemp);
                }
                // Create directory by user id
                if(!is_dir($dirByUserTemp))
                {
                    mkdir($dirByUserTemp);
                }
            }
            if($actionPrm == "EDIT")
            {
                // Create directory by PR No
                if(!is_dir($dirByPrTemp))
                {
                    mkdir($dirByPrTemp);
                }
            }
            
            
            if($btnPrm == "ADD")
            {
                if (!preg_match('/#/', $fileName)) 
                {
                    if($fileSize < 2000000)
                    {
                        if($fileSize>999999)
                        { //IF GREATER THAN 999KB, DISPLAY AS MB 
                            $theDiv = $fileSize/1000000;
                            $fileSize = round($theDiv,1)." MB";
                        }
                        else
                        {
                            $theDiv = $fileSize/1000;
                            $fileSize = round($theDiv,1)." KB";
                        }

                       /***************************
                        * UPLOAD FILE
                        ***************************/
                        if($actionPrm == "CREATE" || $actionPrm == "REPLICATE")
                        {
                            $file = $dirByUserTemp.$fileName;
                            $dirTemp = $dirByUserTemp;
                        }
                        if($actionPrm == "EDIT" || $actionPrm == "APPROVAL")
                        {
                            $file = $dirByPrTemp.$fileName;
                            $dirTemp = $dirByPrTemp;
                        }
                        if(!file_exists($dirTemp.$_FILES['uploadfile']['name']))
                        {
                            //$file = $uploadDir . $fileName; 
                            if (move_uploaded_file($fileTemp, $file)) 
                            { 
                                if($btnPrm == "ADD")
                                {
                                    $msg = "Success_Add";
                                }
                            }
                            else
                            {
                                $msg = "Error";
                            }
                        }
                        else
                        {
                            $msg = "DuplicateFile";
                            $msg = $msg."||";
                        }
                    }
                    else
                    {
                        $msg = "FileSizeError";
                        $msg = $msg."||";
                    }
                }
                else
                {
                    $msg = "FileNameError";
                    $msg = $msg."||";
                }
                
            }
            
            if($btnPrm == "DEL")
            {
                $attachmentSeqPrm = $_POST['attachmentSeqPrm'];
                $detailAttachment = array();
                $indexAttachment  = $attachmentSeqPrm;
                $fileNamePrm      = $_POST['fileNamePrm'];
                
                if($actionPrm == "CREATE" || $actionPrm == "REPLICATE")
                {
                    $file = $dirByUserTemp.$fileNamePrm;
                }
                if($actionPrm == "EDIT" || $actionPrm == "APPROVAL")
                {
                    $dirByPrTemp   	= $uploadDirTemp.$prNoPrm."/";
                    $file           = $dirByPrTemp.$fileNamePrm;
                }
                
               /***************************
                * Delete in array
                ***************************/
                $prAttachment       = ($_SESSION['prAttachment']);
                unset($prAttachment[$indexAttachment]);
                $_SESSION['prAttachment'] = array_values($prAttachment);
                
               /***************************
                * DELETE FILE
                ***************************/
                unlink($file);
                $msg = "Success_Del";
            }
        }
        else
        {
            $msg = "ActionFormError";
        }
        
        if($msg == "Success_Add" || $msg == "Success_Del")
        {
            if($msg == "Success_Add")
            {
                /***************************
                 * Add in array
                 ***************************/
                if(count($prAttachment) == 0)
                {
                    $sequences          = 1;
                    $fileSeqHidden      = $sequences;
                    $prAttachmentTemp[] = array(
                                                    'itemNameFile'=> $itemNameFile
                                                    ,'itemCdFile'=>$itemCdFile
                                                    ,'fileName'=> $fileName
                                                    ,'fileType'=> $fileType
                                                    ,'fileSize'=> $fileSize
                                                    ,'fileSeqHidden'=> $fileSeqHidden
                                                );
                    $addPrAttachment          = $prAttachmentTemp;
                    $_SESSION['prAttachment'] = $addPrAttachment;
                }
                else
                {
                    $sequences          = count($_SESSION['prAttachment']);
                    $fileSeqHidden      = $sequences + 1;

                    $prAttachmentTemp[]   = array(
                                                    'itemNameFile'=> $itemNameFile
                                                    ,'itemCdFile'=>$itemCdFile
                                                    ,'fileName'=> $fileName
                                                    ,'fileType'=> $fileType
                                                    ,'fileSize'=> $fileSize
                                                    ,'fileSeqHidden'=> $fileSeqHidden
                                                );
                    $addPrAttachment          = $prAttachmentTemp;
                    $result = array_merge($prAttachment,$addPrAttachment);
                    $_SESSION['prAttachment'] = $result;
                }
            }
            
           /***************************
            * Array for table
            ***************************/
            $json_names = array();

            foreach (array_values($_SESSION['prAttachment']) as $i => $value) 
            {
                $fileSeqHidden      = $i + 1;
                $itemNameFileVal    = $value['itemNameFile'];
                $itemCdFileVal      = $value['itemCdFile'];
                $fileNameVal        = $value['fileName'];
                $fileTypeVal        = $value['fileType'];
                $fileSizeVal        = $value['fileSize'];
                $fileTempVal        = $value['fileTemp'];
                
                $b = array('fileSeqHidden' => $fileSeqHidden
                            ,'itemCdFile' => $itemCdFileVal
                            ,'itemNameFile' => $itemNameFileVal
                            ,'fileNameVal' => $fileNameVal
                            ,'fileTypeVal' => $fileTypeVal
                            ,'fileSizeVal' => $fileSizeVal);
                array_push($json_names, $b);
            }

            $addMsg = json_encode($json_names);
            $countRow = count($_SESSION['prAttachment']);
          
            $msg = $msg."||".$countRow."||".$addMsg;
        }
    }
    else
    {	
        $msg = "SessionExpired";
    }
}
else
{	
    $msg = "SessionExpired";
}
echo $msg;
?>
