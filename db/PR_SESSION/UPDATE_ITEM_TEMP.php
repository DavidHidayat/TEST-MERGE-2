<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    $sKdper     = $_SESSION['sKdper'];
    if($sUserId != '')
    {  
        $prItem     = array();
        $prItemTemp = array();
        
        $prItem             = ($_SESSION['prItem']);
        $itemSeqHidden      = strtoupper(trim($_GET["itemSeqHiddenPrm"]));
        $itemType           = strtoupper(trim($_GET["itemTypePrm"]));
        $expNo              = strtoupper(trim($_GET["expNoPrm"]));
        $rfiNo              = strtoupper(trim($_GET["rfiNoPrm"]));
        $faCd               = strtoupper(trim($_GET["faCdPrm"]));
        $itemCd             = strtoupper(trim($_GET["itemCdPrm"]));
        $itemName           = strtoupper(trim($_GET["itemNamePrm"]));
        $itemName           = str_replace("'", "''", $itemName);
        $itemName           = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemName);
        $itemName           = preg_replace('/\s+/', ' ',$itemName);
        $itemName           = stripslashes($itemName);
        $itemNameRefHidden  = strtoupper(trim($_GET["itemNameRefHiddenPrm"]));
        $itemNameRefHidden  = str_replace("'", "''", $itemNameRefHidden);
        $itemNameRefHidden  = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameRefHidden);
        $itemNameRefHidden  = preg_replace('/\s+/', ' ',$itemNameRefHidden);
        $itemNameRefHidden  = stripslashes($itemNameRefHidden);
        $supplierCd         = strtoupper(trim($_GET["supplierCdPrm"]));
        $supplierName       = strtoupper(trim($_GET["supplierNamePrm"]));
        $um                 = strtoupper(trim($_GET["umPrm"]));
        $price              = trim($_GET["pricePrm"]); ;
        $qty                = trim($_GET["qtyPrm"]); ;
        $amount             = trim($_GET["amountPrm"]); 
        $deliveryDate       = trim($_GET["deliveryDatePrm"]); 
        $remark             = strtoupper(trim($_GET["remarkPrm"]));
        $remark             = str_replace("'", "''", $remark);
        $remark             = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $remark);
        $remark             = preg_replace('/\s+/', ' ',$remark);
        $actionForm         = strtoupper(trim($_GET['actionPrm']));
        $validateItemName   = 0;
        
        if($supplierCd == "")
        {
            $supplierCd == "SUP99";
        }
        if(!is_numeric($price))
        {
            $price = 0;
        }
        
        if(!is_numeric($qty))
        {
            $qty = 0;
        }
        
        if(!is_numeric($amount))
        {
            $amount = 0;
        }
        
        $faCodeFmt = "/^[A-Z]{2}-[0-9]{4}-[0-9]{2}-[0-9]{3}$/";
        
        if($itemCd == "")
        {
            $itemCd = "99";
        }
          
        if($supplierCd != "SUP99")
        {
            $query_select_eps_m_supplier = "select 
                                                SUPPLIER_NAME
                                            from
                                                EPS_M_SUPPLIER
                                            where
                                                SUPPLIER_CD = '$supplierCd'";
            $sql_select_eps_m_supplier = $conn->query($query_select_eps_m_supplier);
            $row_select_eps_m_supplier = $sql_select_eps_m_supplier->fetch(PDO::FETCH_ASSOC);
            $supplierName = $row_select_eps_m_supplier['SUPPLIER_NAME'];
        }
        
        $the_word  = "||";
        if($actionForm == 'ADD')
        {
            
            if(count($prItem) > 0)
            {
                foreach (array_values($_SESSION['prItem']) as $x => $value) 
                {
                    /**
                    * Check duplicate item name in array
                    */
                    
                    $itemNameCheck      = $value['itemName'];
                    $itemNameCheck      = strtoupper(trim($itemNameCheck));
                    // Remove single quote
                    $itemNameCheck      = str_replace("'", "''", $itemNameCheck);
                    $itemNameCheck      = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $itemNameCheck);
                    // Remove space
                    $itemNameCheck      = preg_replace('/\s+/', ' ',$itemNameCheck);
                    $itemNameCheck      = stripslashes($itemNameCheck);
                    // Remove all whitespace
                    $itemNameCheck      = trim(preg_replace('/\s+/', '', $itemNameCheck));
                    if(trim(preg_replace('/\s+/', '', $itemName)) == $itemNameCheck)
                    {
                        $validateItemName++;
                    }
                }
            }
            if($itemType == "" || $itemCd == "" || $itemName == "" || $um == "" || $deliveryDate == "" )
            {
                $msg = "Mandatory_1";
            }
            else if($qty <= 0 || $price <= 0 || $amount <= 0)
            {
                $msg = "Mandatory_2";
            }
            else if(($itemType == "1" || $itemType == "3" || $itemType == "4" || $itemType == "5" || $itemType == "6") && $expNo == "")
            {
                $msg = "Mandatory_4";
            }
            else if(($itemType == "2" || $itemType == "7") && $rfiNo == "")
            {
                $msg = "Mandatory_5";
            }
//            else if(($itemType == "2" || $itemType == "7")  && ($sKdper == "D" || $sKdper == "S" || $sKdper == "H")  && $rfiNo != "" && !preg_match($faCodeFmt, $faCd))
//            {
//                $msg = "Mandatory_6";
//            }
            /*else if(($itemType == "2" || $itemType == "7")  && ($sKdper == "D" || $sKdper == "S") && $rfiNo != '' && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $rfiNo))
            {           
                $msg = 'Mandatory_7';
            }
            else if(($itemType == "2" || $itemType == "7")  && $sKdper == "H" && $rfiNo != '' && !preg_match("/^[0-9]{3}-[0-9]{2}$/", $rfiNo))
            {           
                $msg = 'Mandatory_7';
            }*/
            // Type RFI EFAM
            else if($itemType == "2" && $rfiNo != '' && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $rfiNo))
            {          
                $msg = 'Mandatory_7';
            }
            // Type RFI Non EFAM
            //else if($itemType == "7" && $rfiNo != '' && !preg_match("/^[0-9]{3}-[0-9]{2}$/", $rfiNo))
			else if($itemType == "7" && $rfiNo == '')
            {           
                $msg = 'Mandatory_7';
            }
            else if (strpos($remark,$the_word ) !== false || strpos($itemName,$the_word ) !== false) 
            {
                $msg = 'Mandatory_8';
            }
            else if($validateItemName > 0)
            {
                $msg = 'Duplicate';
            }
            else
            {
                /***************************
                 * Add in array
                 ***************************/
                if(count($prItem) == 0)
                {
                    $sequences          = 1;
                    $itemSeqHidden      = $sequences;
                    $prItemTemp[]   = array(
                                            'itemSeqHidden'=> $itemSeqHidden
                                            ,'itemType'=> $itemType
                                            ,'expNo'=> $expNo
                                            ,'rfiNo'=> $rfiNo
                                            ,'faCd'=> $faCd
                                            ,'itemCd'=> $itemCd
                                            ,'itemName'=> $itemName
                                            ,'itemNameRef'=> $itemName
                                            ,'supplierCd'=> $supplierCd
                                            ,'supplierName'=> $supplierName
                                            ,'um'=> $um
                                            ,'price'=> $price
                                            ,'qty'=> $qty
                                            ,'amount'=> $amount
                                            ,'deliveryDate'=> $deliveryDate
                                            ,'remark'=> $remark
                                        );
                    $addPrItem          = $prItemTemp;
                    $_SESSION['prItem'] = $addPrItem;
                }
                else
                {
                    $sequences          = count($_SESSION['prItem']);
                    $itemSeqHidden      = $sequences + 1;
                    
                    $prItemTemp[]   = array(
                                            'itemSeqHidden'=> $itemSeqHidden
                                            ,'itemType'=> $itemType
                                            ,'expNo'=> $expNo
                                            ,'rfiNo'=> $rfiNo
                                            ,'faCd'=> $faCd
                                            ,'itemCd'=> $itemCd
                                            ,'itemName'=> $itemName
                                            ,'itemNameRef'=> $itemName
                                            ,'supplierCd'=> $supplierCd
                                            ,'supplierName'=> $supplierName
                                            ,'um'=> $um
                                            ,'price'=> $price
                                            ,'qty'=> $qty
                                            ,'amount'=> $amount
                                            ,'deliveryDate'=> $deliveryDate
                                            ,'remark'=> $remark
                                          );
                    $addPrItem = $prItemTemp;
                    $result = array_merge($prItem,$addPrItem);
                    $_SESSION['prItem'] = $result;
                }  
                
               /***************************
                * Array for table
                ***************************/
                foreach (array_values($_SESSION['prItem']) as $i => $value) 
                {
                    $itemSeqHidden      = $i + 1;
                    $itemTypeVal        = $value['itemType'];
                    $expNoVal           = $value['expNo'];
                    $rfiNoVal           = $value['rfiNo'];
                    $faCdVal            = $value['faCd'];
                    $itemCdVal          = $value['itemCd'];
                    $itemNameVal        = $value['itemName'];
                    $itemNameRefVal     = $value['itemNameRef'];
                    $supplierCdVal      = $value['supplierCd'];
                    $supplierNameVal    = $value['supplierName'];
                    $umVal              = $value['um'];
                    $priceVal           = $value['price'];
                    $qtyVal             = $value['qty'];
                    $amountVal          = $value['amount'];
                    $deliveryDateVal    = $value['deliveryDate'];
                    $remarkVal          = $value['remark'];
                        
                    if($i == 0)
                    {
                        $prItemTable = "<tr>"
                                            ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                            ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                            ."<td>".$itemCdVal."</td>"
                                            ."<td>".$itemNameVal."</td>"
                                            ."<td style='display: none'>".$itemNameRefVal."</td>"
                                            ."<td>".$deliveryDateVal."</td>"
                                            ."<td style='display: none'>".$itemTypeVal."</td>"
                                            ."<td>".$expNoVal."</td>"
                                            ."<td>".$rfiNoVal."</td>"
                                            ."<td>".$faCdVal."</td>"
                                            ."<td>".$umVal."</td>"
                                            ."<td style='text-align: right'>".$qtyVal."</td>"
                                            ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                            ."<td style='display: none'>".$supplierCdVal."</td>"
                                            ."<td>".$supplierNameVal."</td>"
                                            ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                            ."<td>".$remarkVal."</td>"
                                            ."</tr>";
                    }
                    else
                    {
                        $prItemTable .= "<tr>"
                                            ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                            ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                            ."<td>".$itemCdVal."</td>"
                                            ."<td>".$itemNameVal."</td>"
                                            ."<td style='display: none'>".$itemNameRefVal."</td>"
                                            ."<td>".$deliveryDateVal."</td>"
                                            ."<td style='display: none'>".$itemTypeVal."</td>"
                                            ."<td>".$expNoVal."</td>"
                                            ."<td>".$rfiNoVal."</td>"
                                            ."<td>".$faCdVal."</td>"
                                            ."<td>".$umVal."</td>"
                                            ."<td style='text-align: right'>".$qtyVal."</td>"
                                            ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                            ."<td style='display: none'>".$supplierCdVal."</td>"
                                            ."<td>".$supplierNameVal."</td>"
                                            ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                            ."<td>".$remarkVal."</td>"
                                            ."</tr>";
                    }
                }
                
                $msg = "Success_Add"."||".$prItemTable;
            }
        }
        else if($actionForm == 'EDIT')
        {
            // If change item name
            if($itemName != $itemNameRefHidden)
            {
                foreach (array_values($_SESSION['prItem']) as $x => $value) 
                {
                   /**
                    * Check duplicate item name in array
                    */
                    $itemNameCheck = $value['itemName'];
                    if($itemName == $itemNameCheck)
                    {
                        $validateItemName++;
                    }
                }
            }
            
            if($itemType == "" || $itemCd == "" || $itemName == "" || $um == "" || $deliveryDate == "" )
            {
                $msg = "Mandatory_1";
            }
            else if($qty <= 0 || $price <= 0 || $amount <= 0)
            {
                $msg = "Mandatory_2";
            }
            else if($itemType == "1" && $expNo == "")
            {
                $msg = "Mandatory_4";
            }
            else if($itemType == "2" && $rfiNo == "")
            {
                $msg = "Mandatory_5";
            }
//            else if($itemType == "2" && $rfiNo != "" && !preg_match($faCodeFmt, $faCd))
//            {
//                $msg = "Mandatory_6";
//            }
            else if($itemType == "2" && $sKdper == "D" && $rfiNo != '' && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $rfiNo))
            {           
                $msg = 'Mandatory_7';
            }
            else if($itemType == "2" && $sKdper == "H" && $rfiNo != '' && !preg_match("/^[0-9]{3}-[0-9]{2}$/", $rfiNo))
            {           
                $msg = 'Mandatory_7';
            }
            else if (strpos($remark,$the_word ) !== false || strpos($itemName,$the_word ) !== false) 
            {
                $msg = 'Mandatory_8';
            }
            else if($validateItemName > 0)
            {
                $msg = 'Duplicate';
            }
            else
            {
                /***************************
                 * Edit attachment
                 ***************************/
                if(count($_SESSION['prAttachment']) > 0)
                {
                    foreach (array_values($_SESSION['prAttachment']) as $j => $valueItem) 
                    {
                        $fileSeqHidden      = $j + 1;
                        $itemNameFileVal    = $valueItem['itemNameFile'];
                        $itemCdFileVal      = $valueItem['itemCdFile'];
                        $fileNameVal        = $valueItem['fileName'];
                        $fileTypeVal        = $valueItem['fileType'];
                        $fileSizeVal        = $valueItem['fileSize'];
                       
                        if($itemNameRefHidden == $itemNameFileVal)
                        {
                            $itemCdFileVal = $itemCd;
                            $itemNameFileVal = $itemName;
                        }
                     
                        $_SESSION['prAttachment'][$j]['fileSeqHidden']  = $fileSeqHidden;
                        $_SESSION['prAttachment'][$j]['itemCdFile']     = $itemCdFileVal;
                        $_SESSION['prAttachment'][$j]['itemNameFile']   = $itemNameFileVal;
                        $_SESSION['prAttachment'][$j]['fileName']       = $fileNameVal;
                        $_SESSION['prAttachment'][$j]['fileType']       = $fileTypeVal;
                        $_SESSION['prAttachment'][$j]['fileSize']       = $fileSizeVal;
                        
                        if($j == 0)
                        {
                            $prAttachmentTable = "<tr>"
                                                    ."<td style='text-align: right;'>".$fileSeqHidden."</td>"
                                                    ."<td><input type='radio' name='radioItem' value=".$fileSeqHidden."></td>"
                                                    ."<td>".$itemCdFileVal."</td>"
                                                    ."<td>".$itemNameFileVal."</td>"
                                                    ."<td>".$fileNameVal."</td>"
                                                    ."<td>".$fileTypeVal."</td>"
                                                    ."<td>".$fileSizeVal."</td>"
                                                    ."</tr>";
                        }
                        else
                        {
                            $prAttachmentTable .= "<tr>"
                                                    ."<td style='text-align: right;'>".$fileSeqHidden."</td>"
                                                    ."<td><input type='radio' name='radioItem' value=".$fileSeqHidden."></td>"
                                                    ."<td>".$itemCdFileVal."</td>"
                                                    ."<td>".$itemNameFileVal."</td>"
                                                    ."<td>".$fileNameVal."</td>"
                                                    ."<td>".$fileTypeVal."</td>"
                                                    ."<td>".$fileSizeVal."</td>"
                                                    ."</tr>";
                        }
                    }
                }
                
                /***************************
                 * Edit in array
                 ***************************/
                $indexItem          = $itemSeqHidden - 1;
              
                $prItem[$indexItem]['itemType']     = $itemType;
                $prItem[$indexItem]['expNo']        = $expNo;
                $prItem[$indexItem]['rfiNo']        = $rfiNo;
                $prItem[$indexItem]['faCd']         = $faCd; 
                $prItem[$indexItem]['itemCd']       = $itemCd;
                $prItem[$indexItem]['itemName']     = $itemName;
                $prItem[$indexItem]['itemNameRef']  = $itemName;
                $prItem[$indexItem]['supplierCd']   = $supplierCd;
                $prItem[$indexItem]['supplierName'] = $supplierName;
                $prItem[$indexItem]['um']           = $um;
                $prItem[$indexItem]['price']        = $price;
                $prItem[$indexItem]['qty']          = $qty;
                $prItem[$indexItem]['amount']       = $amount;
                $prItem[$indexItem]['deliveryDate'] = $deliveryDate;
                $prItem[$indexItem]['remark']       = $remark;
                        
                $_SESSION['prItem'] = array_values($prItem);
               
                foreach (array_values($_SESSION['prItem']) as $i => $value) 
                {
                    $itemSeqHidden      = $i + 1;
                    $itemTypeVal        = $value['itemType'];
                    $expNoVal           = $value['expNo'];
                    $rfiNoVal           = $value['rfiNo'];
                    $faCdVal            = $value['faCd'];
                    $itemCdVal          = $value['itemCd'];
                    $itemNameVal        = $value['itemName'];
                    $itemNameRefVal     = $value['itemNameRef'];
                    $supplierCdVal      = $value['supplierCd'];
                    $supplierNameVal    = $value['supplierName'];
                    $umVal              = $value['um'];
                    $priceVal           = $value['price'];
                    $qtyVal             = $value['qty'];
                    $amountVal          = $value['amount'];
                    $deliveryDateVal    = $value['deliveryDate'];
                    $remarkVal          = $value['remark'];
                   
                    $_SESSION['prItem'][$i]['itemSeqHidden']    = $itemSeqHidden;
                    $_SESSION['prItem'][$i]['itemType']         = $itemTypeVal;
                    $_SESSION['prItem'][$i]['expNo']            = $expNoVal;
                    $_SESSION['prItem'][$i]['rfiNo']            = $rfiNoVal;
                    $_SESSION['prItem'][$i]['faCd']             = $faCdVal; 
                    $_SESSION['prItem'][$i]['itemCd']           = $itemCdVal;
                    $_SESSION['prItem'][$i]['itemName']         = $itemNameVal;
                    $_SESSION['prItem'][$i]['itemNameRef']      = $itemNameRefVal;
                    $_SESSION['prItem'][$i]['supplierCd']       = $supplierCdVal;
                    $_SESSION['prItem'][$i]['supplierName']     = $supplierNameVal;
                    $_SESSION['prItem'][$i]['um']               = $umVal;
                    $_SESSION['prItem'][$i]['price']            = $priceVal; 
                    $_SESSION['prItem'][$i]['qty']              = $qtyVal; 
                    $_SESSION['prItem'][$i]['amount']           = $amountVal; 
                    $_SESSION['prItem'][$i]['deliveryDate']     = $deliveryDateVal; 
                    $_SESSION['prItem'][$i]['remark']           = $remarkVal;
                    if($i == 0)
                    {
                        $prItemTable = "<tr>"
                                            ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                            ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                            ."<td>".$itemCdVal."</td>"
                                            ."<td>".$itemNameVal."</td>"
                                            ."<td style='display: none'>".$itemNameRefVal."</td>"
                                            ."<td>".$deliveryDateVal."</td>"
                                            ."<td style='display: none'>".$itemTypeVal."</td>"
                                            ."<td>".$expNoVal."</td>"
                                            ."<td>".$rfiNoVal."</td>"
                                            ."<td>".$faCdVal."</td>"
                                            ."<td>".$umVal."</td>"
                                            ."<td style='text-align: right'>".$qtyVal."</td>"
                                            ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                            ."<td style='display: none'>".$supplierCdVal."</td>"
                                            ."<td>".$supplierNameVal."</td>"
                                            ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                            ."<td>".$remarkVal."</td>"
                                            ."</tr>";
                    }
                    else
                    {
                        $prItemTable .= "<tr>"
                                            ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                            ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                            ."<td>".$itemCdVal."</td>"
                                            ."<td>".$itemNameVal."</td>"
                                            ."<td style='display: none'>".$itemNameRefVal."</td>"
                                            ."<td>".$deliveryDateVal."</td>"
                                            ."<td style='display: none'>".$itemTypeVal."</td>"
                                            ."<td>".$expNoVal."</td>"
                                            ."<td>".$rfiNoVal."</td>"
                                            ."<td>".$faCdVal."</td>"
                                            ."<td>".$umVal."</td>"
                                            ."<td style='text-align: right'>".$qtyVal."</td>"
                                            ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                            ."<td style='display: none'>".$supplierCdVal."</td>"
                                            ."<td>".$supplierNameVal."</td>"
                                            ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                            ."<td>".$remarkVal."</td>"
                                            ."</tr>";
                    }
                }
                $msg = "Success_Edit"."||".$prItemTable."||".$prAttachmentTable;
            }
        }
        else if($actionForm == "DEL")
        {
            $itemSeq        = trim($_GET['itemSeqPrm']);
            $prItem         = array();
            $indexItem      = $itemSeq;
            
            /***************************
             * Delete in array
             ***************************/
            $prItem      = ($_SESSION['prItem']);
            unset($prItem[$indexItem]);
            $_SESSION['prItem'] = array_values($prItem);
            
            foreach (array_values($_SESSION['prItem']) as $i => $value) 
            {
                $itemSeqHidden      = $i + 1;
                $itemTypeVal        = $value['itemType'];
                $expNoVal           = $value['expNo'];
                $rfiNoVal           = $value['rfiNo'];
                $faCdVal            = $value['faCd'];
                $itemCdVal          = $value['itemCd'];
                $itemNameVal        = $value['itemName'];
                $itemNameRefVal     = $value['itemNameRef'];
                $supplierCdVal      = $value['supplierCd'];
                $supplierNameVal    = $value['supplierName'];
                $umVal              = $value['um'];
                $priceVal           = $value['price'];
                $qtyVal             = $value['qty'];
                $amountVal          = $value['amount'];
                $deliveryDateVal    = $value['deliveryDate'];
                $remarkVal          = $value['remark'];
                   
                $_SESSION['prItem'][$i]['itemSeqHidden']    = $itemSeqHidden;
                $_SESSION['prItem'][$i]['itemType']         = $itemTypeVal;
                $_SESSION['prItem'][$i]['expNo']            = $expNoVal;
                $_SESSION['prItem'][$i]['rfiNo']            = $rfiNoVal;
                $_SESSION['prItem'][$i]['faCd']             = $faCdVal; 
                $_SESSION['prItem'][$i]['itemCd']           = $itemCdVal;
                $_SESSION['prItem'][$i]['itemName']         = $itemNameVal;
                $_SESSION['prItem'][$i]['itemNameRef']      = $itemNameRefVal;
                $_SESSION['prItem'][$i]['supplierCd']       = $supplierCdVal;
                $_SESSION['prItem'][$i]['supplierName']     = $supplierNameVal;
                $_SESSION['prItem'][$i]['um']               = $umVal;
                $_SESSION['prItem'][$i]['price']            = $priceVal; 
                $_SESSION['prItem'][$i]['qty']              = $qtyVal; 
                $_SESSION['prItem'][$i]['amount']           = $amountVal; 
                $_SESSION['prItem'][$i]['deliveryDate']     = $deliveryDateVal; 
                $_SESSION['prItem'][$i]['remark']           = $remarkVal;
                    
                if($i == 0)
                {
                    $prItemTable = "<tr>"
                                        ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                        ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                        ."<td>".$itemCdVal."</td>"
                                        ."<td>".$itemNameVal."</td>"
                                        ."<td style='display: none'>".$itemNameRefVal."</td>"
                                        ."<td>".$deliveryDateVal."</td>"
                                        ."<td style='display: none'>".$itemTypeVal."</td>"
                                        ."<td>".$expNoVal."</td>"
                                        ."<td>".$rfiNoVal."</td>"
                                        ."<td>".$faCdVal."</td>"
                                        ."<td>".$umVal."</td>"
                                        ."<td style='text-align: right'>".$qtyVal."</td>"
                                        ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                        ."<td style='display: none'>".$supplierCdVal."</td>"
                                        ."<td>".$supplierNameVal."</td>"
                                        ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                        ."<td>".$remarkVal."</td>"
                                        ."</tr>";
                }
                else
                {
                    $prItemTable .= "<tr>"
                                        ."<td style='text-align: right;'>".$itemSeqHidden."</td>"
                                        ."<td><input type='radio' name='radioItem' value=".$itemSeqHidden."></td>"
                                        ."<td>".$itemCdVal."</td>"
                                        ."<td>".$itemNameVal."</td>"
                                        ."<td style='display: none'>".$itemNameRefVal."</td>"
                                        ."<td>".$deliveryDateVal."</td>"
                                        ."<td style='display: none'>".$itemTypeVal."</td>"
                                        ."<td>".$expNoVal."</td>"
                                        ."<td>".$rfiNoVal."</td>"
                                        ."<td>".$faCdVal."</td>"
                                        ."<td>".$umVal."</td>"
                                        ."<td style='text-align: right'>".$qtyVal."</td>"
                                        ."<td style='text-align: right'>".number_format($priceVal)."</td>"
                                        ."<td style='display: none'>".$supplierCdVal."</td>"
                                        ."<td>".$supplierNameVal."</td>"
                                        ."<td style='text-align: right' class='amount'>".number_format($amountVal)."</td>"
                                        ."<td>".$remarkVal."</td>"
                                        ."</tr>";
                }
            }
            
            if(count($_SESSION['prAttachment']) > 0)
            {
                $actionForm             = trim($_GET['actionFormPrm']);
                $itemName               = trim($_GET['itemNamePrm']);
                $prNoPrm                = $_GET['prNoPrm'];
                
                $currentMonth           = date(Ymd);
                $uploadDir              = $_SERVER['DOCUMENT_ROOT']."/EPS/db/ATTACHMENT/";
                $uploadDirTemp          = $uploadDir."TEMPORARY/";
                $dirByDateTemp          = $uploadDirTemp.$currentMonth."/";
                $dirByUserTemp          = $dirByDateTemp.$sUserId."/";   
                $dirByPrTemp            = $uploadDirTemp.$prNoPrm."/";

                $prAttachment      = ($_SESSION['prAttachment']);
                foreach (array_values($_SESSION['prAttachment']) as $j => $valueDel) 
                {
                    $itemNameFileVal    = $valueDel['itemNameFile'];
                    $itemCdFileVal      = $valueDel['itemCdFile'];
                    $fileNameVal        = $valueDel['fileName'];
                    $fileTypeVal        = $valueDel['fileType'];
                    $fileSizeVal        = $valueDel['fileSize'];
                    $fileSeqVal         = $valueDel['fileSeqHidden'];
                    
                    $fileSeqDel         = $fileSeqVal - 1;
                    
                    if($itemNameFileVal == $itemName)
                    {
                        if($actionForm == "CREATE" || $actionForm == "REPLICATE")
                        {
                            $file = $dirByUserTemp.$fileNameVal;
                        }
                        if($actionForm == "EDIT" || $actionForm == "APPROVAL")
                        {
                            $dirByPrTemp    = $uploadDirTemp.$prNoPrm."/";
                            $file           = $dirByPrTemp.$fileNameVal;
                        }
                       /***************************
                        * DELETE FILE
                        ***************************/
                        unlink($file);
                        
                       /***************************
                        * Delete in array attachment
                        ***************************/
                        unset($prAttachment[$fileSeqDel]);
                    }
                }
                
                $_SESSION['prAttachment'] = array_values($prAttachment);
                
                if(count($_SESSION['prAttachment']) > 0)
                {
                    foreach (array_values($_SESSION['prAttachment']) as $k => $valueItem) 
                    {
                        $fileSeqHidden      = $k + 1;
                        $itemNameFileVal    = $valueItem['itemNameFile'];
                        $itemCdFileVal      = $valueItem['itemCdFile'];
                        $fileNameVal        = $valueItem['fileName'];
                        $fileTypeVal        = $valueItem['fileType'];
                        $fileSizeVal        = $valueItem['fileSize'];

                        $_SESSION['prAttachment'][$k]['fileSeqHidden']  = $fileSeqHidden;
                        $_SESSION['prAttachment'][$k]['itemCdFile']     = $itemCdFileVal;
                        $_SESSION['prAttachment'][$k]['itemNameFile']   = $itemNameFileVal;
                        $_SESSION['prAttachment'][$k]['fileName']       = $fileNameVal;
                        $_SESSION['prAttachment'][$k]['fileType']       = $fileTypeVal;
                        $_SESSION['prAttachment'][$k]['fileSize']       = $fileSizeVal;

                        if($k == 0)
                        {
                            $prAttachmentTable = "<tr>"
                                                    ."<td style='text-align: right;'>".$fileSeqHidden."</td>"
                                                    ."<td><input type='radio' name='radioItem' value=".$fileSeqHidden."></td>"
                                                    ."<td>".$itemCdFileVal."</td>"
                                                    ."<td>".$itemNameFileVal."</td>"
                                                    ."<td>".$fileNameVal."</td>"
                                                    ."<td>".$fileTypeVal."</td>"
                                                    ."<td>".$fileSizeVal."</td>"
                                                    ."</tr>";
                        }
                        else
                        {
                            $prAttachmentTable .= "<tr>"
                                                    ."<td style='text-align: right;'>".$fileSeqHidden."</td>"
                                                    ."<td><input type='radio' name='radioItem' value=".$fileSeqHidden."></td>"
                                                    ."<td>".$itemCdFileVal."</td>"
                                                    ."<td>".$itemNameFileVal."</td>"
                                                    ."<td>".$fileNameVal."</td>"
                                                    ."<td>".$fileTypeVal."</td>"
                                                    ."<td>".$fileSizeVal."</td>"
                                                    ."</tr>";
                        }
                    }
                }
                
            }
            $msg = "Success_Delete"."||".$prItemTable."||".$prAttachmentTable;
        }
        else
        {
            $msg = "Mandatory_3";
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
