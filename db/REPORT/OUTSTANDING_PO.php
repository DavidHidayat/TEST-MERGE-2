<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
set_time_limit(1800);
ini_set('memory_limit', '512M'); 

/** Include PHPExcel */
require_once '../LIB/PHPExcel/Classes/PHPExcel.php';

include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/COM_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';

$sUserId    = $_SESSION['sUserId'];
$sKdPlant   = $_SESSION['sKDPL'];
$sRoleId    = $_SESSION['sRoleId'];
$action     = $_GET['action'];
$plantCd    = $_GET['plantCdVal'] ;	

$mailFrom       = $_SESSION['sinet'];
$mailFromName   = $_SESSION['snotes'];

if($action == "ManualSentOutstandingPo")
{
    $mailTo       = "muh.iqbal@taci.toyota-industries.com";
    
    $mailSubject  = "[EPS] Outstanding PO Running Manually";
    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
    $mailMessage .= "Dear EPS Administrator,";
    $mailMessage .= "<br><br>Please check EPS server because user already running outstanding PO report manually.";
    $mailMessage .= "<br>Thank you";
    $mailMessage .= "</font>";
    
    manualReportMail($mailTo,$mailFrom,$mailFromName,$mailSubject,$mailMessage);
}

if($action == 'SentOutstandingPo' || $action == 'ManualSentOutstandingPo')
{
    // original denso
    if($plantCd == 0)
    {
        $fileName               = "EPS_Outstanding_PO_JK";
        $addSubject             = "Sunter";
        $deliveryPlantCriteria  = "'JK','SI','HD'";
    }
    else if($plantCd == 7)
    {
        $fileName               = "EPS_Outstanding_PO_TACI";
        $addSubject             = "Bekasi";
        $deliveryPlantCriteria  = "'GT'";
    }
    else 
    {
        if($plantCd == 5)
        {
            $fileName               = "EPS_Outstanding_PO_JF";
            $addSubject             = "Fajar Plant";
            $deliveryPlantCriteria  = "'JF'";
        }
    }
    
//        $fileName               = "EPS_Outstanding_PO_TACI";
//        $addSubject             = "TACI";
//        $deliveryPlantCriteria  = "'TACI PLANT'";

   
    
    
    $npkProcArray 		= array();

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
}
	
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set zoom level
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);

// Set document properties
$objPHPExcel->getProperties()->setCreator("IS Division")
			     ->setLastModifiedBy("Administrator")
			     ->setTitle("Download Outstanding PO")
			     ->setSubject("Oustanding PO")
		   	     ->setDescription("Outstanding PO by criteria")
			     ->setKeywords("EPS")
		      	 ->setCategory("PO");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
$objPHPExcel->getActiveSheet()->getStyle('E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('F1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('G1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('H1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('I1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('J1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('K1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('L1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('M1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);   

$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");  
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");  
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");

$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);  
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setSize(12)->setBold(true);   
$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setSize(12)->setBold(true);  
$objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->setSize(12)->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
$objPHPExcel->getActiveSheet()->setCellValue('B1', "NO.ORDER");  	
$objPHPExcel->getActiveSheet()->setCellValue('C1', "T.TRANS");  
$objPHPExcel->getActiveSheet()->setCellValue('D1', "DUE DATE"); 
$objPHPExcel->getActiveSheet()->setCellValue('E1', "CODE");  
$objPHPExcel->getActiveSheet()->setCellValue('F1', "ITEM NAME");  
$objPHPExcel->getActiveSheet()->setCellValue('G1', "QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('H1', "UM");  
$objPHPExcel->getActiveSheet()->setCellValue('I1', "REQUESTER");  
$objPHPExcel->getActiveSheet()->setCellValue('J1', "CHARGED BU NAME"); 
$objPHPExcel->getActiveSheet()->setCellValue('K1', "OUTFLAG");  
$objPHPExcel->getActiveSheet()->setCellValue('L1', "PLANT");  
$objPHPExcel->getActiveSheet()->setCellValue('M1', "IN CHARGE");  

// FREEZEPANE            
$objPHPExcel->getActiveSheet()->freezePane('A2');

$numrow     = 2;
$itemNo     = 1;               
$outFlag    = '';
$query_select_t_transfer = "select 
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
                                ,(select count(*)
                                    from         
                                EPS_T_PR_ATTACHMENT
                                    where      
                                EPS_T_TRANSFER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
                                and EPS_T_TRANSFER.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) 
                                as ATTACHMENT_ITEM_COUNT
                                ,(select count(*)
                                    from          
                                EPS_T_TRANSFER_SUPPLIER
                                    where      
                                EPS_T_TRANSFER.TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                                as TOTAL_SUPPLIER
                                ,datediff(day, PROC_ACCEPT_DATE, GETDATE()) as COUNT_DATE_DIFF
                                ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE as PROC_ACCEPT_DATE_2
                                ,EPS_T_TRANSFER.CREATE_BY
                                ,EPS_M_EMPLOYEE_2.NAMA1 as CREATE_BY_NAME
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
                            left join
                                EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                            on
                                EPS_T_TRANSFER.CREATE_BY =  EPS_M_EMPLOYEE_2.NPK
                            where
                                EPS_T_TRANSFER.ITEM_STATUS = '1120' OR EPS_T_TRANSFER.ITEM_STATUS = '1160'  ";
if($action == 'SentOutstandingPo')
{
    $query_select_t_transfer .= "and (EPS_T_TRANSFER.CREATE_BY in ($npkProcCriteria))";
    $query_select_t_transfer .= "and (EPS_M_BUNIT.PLANT_ALIAS in ($deliveryPlantCriteria))";
}
else
{
    if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_07' || $sRoleId == 'ROLE_10' || $sRoleId == 'ROLE_11' )
    {
        $query_select_t_transfer .= "and EPS_T_TRANSFER.CREATE_BY = '".$sUserId."'";
    }
}
$query_select_t_transfer .= "order by 
                                PROC_ACCEPT_DATE_2
                                ,EPS_T_TRANSFER.ITEM_NAME OPTION (MAXDOP 1) ";
$sql_select_t_transfer = $conn->query($query_select_t_transfer);
while($row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC))
{
    $transferId     = $row_select_t_transfer['TRANSFER_ID'];
    $prNo           = $row_select_t_transfer['PR_NO'];
    $procAcceptDate = $row_select_t_transfer['PROC_ACCEPT_DATE'];
    $newPrCharged   = $row_select_t_transfer['NEW_CHARGED_BU'];
    $itemName       = $row_select_t_transfer['ITEM_NAME'];
    $newSupplierCd  = $row_select_t_transfer['NEW_SUPPLIER_CD'];
    $newSupplierName= $row_select_t_transfer['NEW_SUPPLIER_NAME'];
    $newDeliveryDate= $row_select_t_transfer['NEW_DELIVERY_DATE'];
    $newItemCd      = $row_select_t_transfer['NEW_ITEM_CD'];
    $newItemName    = $row_select_t_transfer['NEW_ITEM_NAME'];
    $newQty         = $row_select_t_transfer['NEW_QTY'];
    $newUnitCd      = $row_select_t_transfer['NEW_UNIT_CD'];
    $newItemPrice   = $row_select_t_transfer['NEW_ITEM_PRICE'];
    $attachmentItemCount= $row_select_t_transfer['ATTACHMENT_ITEM_COUNT'];
    $totalSupplier  = $row_select_t_transfer['TOTAL_SUPPLIER'];
    $plantAlias     = $row_select_t_transfer['PLANT_ALIAS'];
    $chargedBuName  = $row_select_t_transfer['CHARGED_BU_NAME'];
    $requesterName  = $row_select_t_transfer['REQUESTER_NAME'];
    $countDateDiff  = $row_select_t_transfer['COUNT_DATE_DIFF'];
    $createByName   = $row_select_t_transfer['CREATE_BY_NAME'];
    
    if($countDateDiff < 8)
    {
	$outFlag = '';
    }
    else if($countDateDiff >= 8 && $countDateDiff < 15)
    {
        $outFlag = '*';
    } 
    else if($countDateDiff >= 15 && $countDateDiff < 22)
    {
        $outFlag = '**';
    }
    else
    {
	$outFlag = '***';
    }
                                            
    $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$prNo);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$procAcceptDate);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$newDeliveryDate);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$newItemCd);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$newItemName);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$newQty);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$newUnitCd);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$requesterName);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$chargedBuName);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$outFlag);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$plantAlias);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$createByName);
    $numrow++;
    $itemNo++;
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('EPS_Outstanding_PO');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EPS_Outstanding_PO.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
if($action == 'SentOutstandingPo' || $action == 'ManualSentOutstandingPo')
{
    $objPHPExcel->save('OUTSTANDING_PO/'.$fileName.'.xlsx');
	
	/**
     * SELECT EPS_M_SUPPLIER
     **/
    $supplierMailCriteria = array();
    $x = 0;
    $query_select_m_supplier = "select
                                    EMAIL
                                from
                                    EPS_M_SUPPLIER
                                where
                                    OUTSTANDING_FLAG = 'Y'";
    $sql_select_m_supplier = $conn ->query($query_select_m_supplier);
    while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC)){	
        $supplierEmail = $row_select_m_supplier['EMAIL'];
        if($x == 0)
        {
            $supplierMailCriteria = $supplierEmail;
        }
        else
        {
            $supplierMailCriteria = $supplierMailCriteria.",".$supplierEmail;
        }
        $x++;
    }
			
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

    /**********************************************************************
     * SEND MAIL
     **********************************************************************/
    $mailFrom       = "IT.TACI@taci.toyota-industries.com";
    $mailFromName   = "EPS ADMINISTRATOR/TACI";  

	$mailTo       = $supplierMailCriteria;
	//$mailTo       = "muh.iqbal@taci.toyota-industries.com";
    $mailCc       = "ahmadjafar@taci.toyota-industries.com, idapian@taci.toyota-industries.com,maintenance.sparepart2@taci.toyota-industries.com, maintenance.sparepart1@taci.toyota-industries.com".",".$procInChargeMailCriteria;
    $mailBcc      = $supplierMailCriteria;
    //$mailBcc      = "muh.iqbal@taci.toyota-industries.com";
    
    $mailSubject  = "** [EPS] LIST PERMINTAAN PENAWARAN - ".$addSubject;
    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
    $mailMessage .= "Yth. Bapak/Ibu Supplier PT.TD AUTOMOTIVE COMPRESSOR INDONESIA";
    $mailMessage .= "<br><br>Laporan ini terbentuk dan terkirim secara otomatis dari sistem yang dibuat oleh PT. TD AUTOMOTIVE COMPRESSOR INDONESIA.";
    $mailMessage .= "<br>Silahkan melakukan konfirmasi kepada PIC Procurement yang bersangkutan jika terdapat informasi yang kurang lengkap.";
    $mailMessage .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
    $mailMessage .= "<br><br>Hormat kami,";
    $mailMessage .= "<br><br>Procurement Dept. | General Supplies";
    $mailMessage .= "<br>PT. TD AUTOMOTIVE COMPRESSOR INDONESIA";
    $mailMessage .= "<br><br>(+62 21) 28517699 ext. 301 / 310";
    $mailMessage .= "<br><br>";

    //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
    $a = outstandingPoSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $mailBcc, $fileName);
}
else
{
    $objPHPExcel->save('php://output');
}
?>
