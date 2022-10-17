<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';
include("EXCEL_FILE/ExcelWriter/excelwriter.inc.php");
	
$plantCd	= $_GET['plantCdVal'] ;	
$fileName	= "Laporan_Outstanding_PO";
if($plantCd == 0)
{
	$excel=new ExcelWriter("EXCEL_FILE/Laporan_Outstanding_PO.xls");
	$fileName = "Laporan_Outstanding_PO";
	$addSubject = "Sunter";
}
else if($plantCd == 1)
{
	$excel=new ExcelWriter("EXCEL_FILE/Laporan_Outstanding_PO_BS.xls");
	$fileName = "Laporan_Outstanding_PO_BS";
	$addSubject = "Bekasi";
}
else 
{
	if($plantCd == 5)
	{
		$excel=new ExcelWriter("EXCEL_FILE/Laporan_Outstanding_PO_JF.xls");
		$fileName = "Laporan_Outstanding_PO_JF";
		$addSubject = "3rd Plant";
	}
}
if($excel==false)	
echo $excel->error;
							
$headerColumn = array("No","NO.ORDER","T.TRANS","DUE DATE","KODE","NAMA BARANG","JUMLAH","UM","REQUESTER","NAMA SEKSI","OUTFLAG","PLANT");
$excel->writeLine($headerColumn);

$npkProcArray 		= array();
$whereItemSearch  	= array();
/**
 * Search EPS_M_PR_PROC_APPROVER
 */
$query_select_m_pr_proc_app = "select
								distinct NPK
							   from
								EPS_M_PR_PROC_APPROVER
							   where
								PLANT_CD = '$plantCd'";
$sql_select_m_pr_proc_app = $conn->query($query_select_m_pr_proc_app);
while($row_select_m_pr_proc_app = $sql_select_m_pr_proc_app->fetch(PDO::FETCH_ASSOC)){	
	$npkProc = $row_select_m_pr_proc_app['NPK'];
	$npkProcArray[] = array(
                        'npkProc' => $npkProc
                      );
    $addNpkProcArray = $npkProcArray;
}	
$indexArray = 1;
foreach($addNpkProcArray as $addNpkProcArrays)
{
    $npkProcVal = $addNpkProcArrays['npkProc'];
	if($indexArray == 1)
	{
		$npkProcCriteria = "'".$npkProcVal."'";
	}
	else
	{
		$npkProcCriteria = $npkProcCriteria.",'".$npkProcVal."'";
	}
	$indexArray ++;
}	
$whereItemSearch[] = "(EPS_T_TRANSFER.CREATE_BY IN ($npkProcCriteria))";
	
$itemNo         = 0;               
$outFlag        = '';
$query = "select 
            EPS_T_TRANSFER.TRANSFER_ID
            ,EPS_T_TRANSFER.PR_NO
            ,substring(EPS_T_PR_HEADER.ISSUED_DATE,7,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PR_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
            ,EPS_T_TRANSFER.NEW_CHARGED_BU
            ,EPS_T_TRANSFER.ITEM_NAME
            ,EPS_T_TRANSFER.NEW_SUPPLIER_CD
            ,EPS_T_TRANSFER.NEW_SUPPLIER_NAME
            ,substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,7,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,5,2)+'/'+substring(EPS_T_TRANSFER.NEW_DELIVERY_DATE,1,4) as NEW_DELIVERY_DATE
            ,EPS_T_TRANSFER.NEW_ITEM_CD
            ,EPS_T_TRANSFER.NEW_ITEM_NAME
            ,EPS_T_TRANSFER.NEW_QTY
            ,EPS_T_TRANSFER.NEW_UNIT_CD
            ,EPS_T_TRANSFER.NEW_ITEM_PRICE
            ,EPS_M_BUNIT.PLANT_ALIAS
            ,EPS_M_BUNIT.BU_NAME as CHARGED_BU_NAME
            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
            ,datediff(day, PROC_ACCEPT_DATE, GETDATE()) as COUNT_DATE_DIFF
            ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE as PROC_ACCEPT_DATE_2
          from
            EPS_T_TRANSFER
          inner join
            EPS_T_PR_HEADER 
          on 
            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
          left join
            EPS_M_BUNIT
          on
            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
          left join
            EPS_M_EMPLOYEE
          on
            EPS_T_PR_HEADER.REQUESTER =  EPS_M_EMPLOYEE.NPK
          where
            EPS_T_TRANSFER.ITEM_STATUS = '1120' ";
			
	
if(count($whereItemSearch)) {
	$query  .= " and " . implode(' and ', $whereItemSearch);
}
$query .= " order by 
            PROC_ACCEPT_DATE_2 ";
$sql = $conn->query($query);
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $transferId     = $row['TRANSFER_ID'];
    $prNo           = $row['PR_NO'];
    $procAcceptDate = $row['PROC_ACCEPT_DATE'];
    $newPrCharged   = $row['NEW_CHARGED_BU'];
    $itemName       = $row['ITEM_NAME'];
    $newSupplierCd  = $row['NEW_SUPPLIER_CD'];
    $newSupplierName= $row['NEW_SUPPLIER_NAME'];
    $newDeliveryDate= $row['NEW_DELIVERY_DATE'];
    $newItemCd      = $row['NEW_ITEM_CD'];
    $newItemName    = $row['NEW_ITEM_NAME'];
    $newQty         = $row['NEW_QTY'];
    $newUnitCd      = $row['NEW_UNIT_CD'];
    $newItemPrice   = $row['NEW_ITEM_PRICE'];
    $plantAlias     = $row['PLANT_ALIAS'];
    $chargedBuName  = $row['CHARGED_BU_NAME'];
    $requesterName  = $row['REQUESTER_NAME'];
    $countDateDiff  = $row['COUNT_DATE_DIFF'];
    $itemNo++;
    
    if($countDateDiff < 8){
	$outFlag = '';
    }
    else if($countDateDiff >= 8 && $countDateDiff < 15){
        $outFlag = '*';
    } 
    else if($countDateDiff >= 15 && $countDateDiff < 22){
        $outFlag = '**';
    }
    else{
	$outFlag = '***';
    }
    
    $excel->writeRow();
    $excel->writeCol($itemNo);
    $excel->writeCol($prNo);
    $excel->writeCol($procAcceptDate);
    $excel->writeCol($newDeliveryDate);
    $excel->writeCol($newItemCd);
    $excel->writeCol($newItemName);
    $excel->writeCol($newQty);
    $excel->writeCol($newUnitCd);
    $excel->writeCol($requesterName);
    $excel->writeCol($chargedBuName);
    $excel->writeCol($outFlag);
    $excel->writeCol($plantAlias);
}
	
$excel->close();
echo "data is write into myXls.xls Successfully.";

/** 
 * SELECT EPS_M_SUPPLIER 
 */           
$query_m_supplier = "select 
                        EMAIL
                     from
                        EPS_M_SUPPLIER
                     where
                        SUPPLIER_CD = '$supplierCd'";
$sql_m_supplier = $conn ->query($query_m_supplier);
$row_m_supplier = $sql_m_supplier->fetch(PDO::FETCH_ASSOC);
$supplierMail = $row_m_supplier['EMAIL'];
                
/**
 * SELECT MAIL PROC.IN CHARGE
 */
$procInChargeMailArray = array();
$query_select_m_dscid = "select
                            INETML
                         from
                            EPS_M_DSCID
                         where 
                            INOPOK in ($npkProcCriteria)";
$sql_select_m_dscid = $conn ->query($query_select_m_dscid);
while($row_select_m_dscid = $sql_select_m_dscid->fetch(PDO::FETCH_ASSOC)){	
    $procInChargeMail = trim($row_select_m_dscid['INETML']);
    $procInChargeMailArray[] = array(
        'procInChargeMail' => $procInChargeMail
    );
    $addProcInChargeMailArray = $procInChargeMailArray;
}
$indexProcInCharge = 1;
foreach($addProcInChargeMailArray as $addProcInChargeMailArrays)
{
    $procInChargeMailVal = $addProcInChargeMailArrays['procInChargeMail'];
    if($indexProcInCharge == 1)
    {
        $procInChargeMailCriteria = $procInChargeMailVal;
    }
    else
    {
        $procInChargeMailCriteria = $procInChargeMailCriteria.",".$procInChargeMailVal;
    }
    $indexProcInCharge ++;
}
//echo $procInChargeMailCriteria;

/**********************************************************************
 * SEND MAIL
 **********************************************************************/
$mailFrom     = "muh.iqbal@taci.toyota-industries.com";
$mailFromName = "EPS ADMINISTRATOR/DNIA";  

$mailCc 	  = $procInChargeMailCriteria;
$mailTo       = "karyoto@taci.toyota-industries.com";

$mailSubject  = "** [TRIAL] List Permintaan Penawaran - ".$addSubject;
$mailMessage  = "<font face='Trebuchet MS' size='-1'>";
$mailMessage .= "Yth. Bapak/Ibu Supplier Denso Indoensia Group";
$mailMessage .= "<br><br>Laporan ini terbentuk dan terkirim secara otomatis oleh sistem yang dibuat oleh DENSO INDONESIA GROUP.";
$mailMessage .= "<br>Silahkan melakukan konfirmasi kepada PIC Procurement yang bersangkutan jika terdapat informasi yang kurang lengkap.";
$mailMessage .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
$mailMessage .= "<br><br>Hormat kami,";
$mailMessage .= "<br><br>Procurement Dept. | General Supplies";
$mailMessage .= "<br>Denso Indonesia Group";
$mailMessage .= "<br><br>Sunter Plant: 021-651 2279 Ext. 213 / 214";
$mailMessage .= "<br>Bekasi Plant: 021-898 0303 Ext. 201 / 202";
$mailMessage .= "<br>3rd Plant: 021-2957 7000 Ext. 405 / 406";
$mailMessage .= "<br><br>";
                
//$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
$a = outstandingPoSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $fileName);
?>