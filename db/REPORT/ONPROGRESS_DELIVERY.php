<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
set_time_limit(1800);
ini_set('memory_limit', '512M'); 

$sKdPlant   = $_SESSION['sKDPL'];
$sRoleId    = $_SESSION['sRoleId'];

if($sKdPlant == 0)
{
    $plantCdAlias  = "JK";
}
else if($sKdPlant == 1)
{
    $plantCdAlias  = "BS";
}
else 
{
    if($sKdPlant == 5)
    {
        $plantCdAlias  = "JF";
    }
}
/**
 * Search EPS_M_PR_PROC_APPROVER
 */
$query_select_m_pr_proc_app = "select
                                distinct NPK
                               from
                                EPS_M_PR_PROC_APPROVER
                               where
                                PLANT_CD = '$sKdPlant'";
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
            
/** Include PHPExcel */
require_once '../LIB/PHPExcel/Classes/PHPExcel.php';
//require_once '../LIB/PHPExcel/Classes/PHPExcel/IOFactory.php';

include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set zoom level
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);

// Set document properties
$objPHPExcel->getProperties()->setCreator("IS Division")
			     ->setLastModifiedBy("Administrator")
			     ->setTitle("Download Delay Delivery")
			     ->setSubject("Delay Delivery")
		   	     ->setDescription("Delay Delivery by criteria")
			     ->setKeywords("EPS")
		      	     ->setCategory("RO");

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
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

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
$objPHPExcel->getActiveSheet()->getStyle('N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('O1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('P1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('Q1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('R1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('S1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    

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
$objPHPExcel->getActiveSheet()->getStyle('N1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('O1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('P1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('Q1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle('R1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
$objPHPExcel->getActiveSheet()->getStyle('S1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");

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
$objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('O1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('Q1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('R1')->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('S1')->getFont()->setSize(12)->setBold(true);

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
$objPHPExcel->getActiveSheet()->getStyle('N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('S1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
$objPHPExcel->getActiveSheet()->setCellValue('B1', "NO.ORDER");  	
$objPHPExcel->getActiveSheet()->setCellValue('C1', "REQUESTER");  
$objPHPExcel->getActiveSheet()->setCellValue('D1', "PROC ACCEPTED DATE"); 
$objPHPExcel->getActiveSheet()->setCellValue('E1', "PO NO");  
$objPHPExcel->getActiveSheet()->setCellValue('F1', "PO DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('G1', "SENT PO DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('H1', "SUPPLIER");  
$objPHPExcel->getActiveSheet()->setCellValue('I1', "DUE DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('J1', "OUTFLAG"); 
$objPHPExcel->getActiveSheet()->setCellValue('K1', "DESCRIPTION");  
$objPHPExcel->getActiveSheet()->setCellValue('L1', "OPEN QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('M1', "ORDER QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('N1', "CUR");  
$objPHPExcel->getActiveSheet()->setCellValue('O1', "PRICE");  
$objPHPExcel->getActiveSheet()->setCellValue('P1', "SECT"); 
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "SECT NAME");  
$objPHPExcel->getActiveSheet()->setCellValue('R1', "E/I");  
$objPHPExcel->getActiveSheet()->setCellValue('S1', "CIP");  

// FREEZEPANE            
$objPHPExcel->getActiveSheet()->freezePane('A2');

$numrow     = 2;
$itemNo     = 1;     
$outFlag    = '';
$query_select_t_po_detail = "select     
            EPS_T_TRANSFER.PR_NO
            ,EPS_T_TRANSFER.REQUESTER
            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
            ,EPS_T_PO_DETAIL.PO_NO
            ,EPS_T_PO_HEADER.SUPPLIER_NAME
            ,substring(EPS_T_PO_HEADER.ISSUED_DATE, 7, 2)
             + '/' + substring(EPS_T_PO_HEADER.ISSUED_DATE, 5, 2) 
             + '/' + substring(EPS_T_PO_HEADER.ISSUED_DATE, 1, 4) as ISSUED_DATE
            ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2)
             + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) 
             + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE
            ,EPS_T_PO_DETAIL.ITEM_CD
            ,EPS_T_PO_DETAIL.ITEM_NAME
            ,EPS_T_PO_DETAIL.QTY
            ,EPS_T_PO_DETAIL.UNIT_CD
            ,EPS_T_PO_HEADER.CURRENCY_CD
            ,EPS_T_PO_DETAIL.ITEM_PRICE
            ,isnull(
             (select sum(TRANSACTION_QTY)
             from 
                EPS_T_RO_DETAIL
             where   
                EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A')
             ,0
             )
            as TOTAL_RECEIVED_QTY
            ,isnull(
             (select sum(TRANSACTION_QTY)
             from 
                EPS_T_RO_DETAIL
             where   
                EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'C')
             ,0
             )
            as TOTAL_CANCELED_QTY
            ,isnull(
                (select sum(TRANSACTION_QTY)
                from 
                    EPS_T_RO_DETAIL
                where   
                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                ,0
                )
            as TOTAL_OPENED_QTY
            ,EPS_T_TRANSFER.NEW_CHARGED_BU
            ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
            ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
            ,EPS_T_TRANSFER.NEW_RFI_NO
            ,EPS_M_BUNIT.BU_NAME
            ,datediff
                (day, EPS_T_PO_HEADER.DELIVERY_DATE, convert(char(10), GETDATE(), 112)) 
             as COUNT_DATE_DIFF
          from         
            EPS_T_PO_DETAIL 
          left join
            EPS_T_PO_HEADER 
          on 
            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
          left join
            EPS_T_TRANSFER
          on
            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
          left join
            EPS_M_BUNIT
          on
            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
          left join
            EPS_M_EMPLOYEE
          on
            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
          left join
            EPS_T_PR_HEADER 
          on 
            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
          where     
            EPS_T_PO_HEADER.PO_STATUS = '1250'
            and EPS_T_PO_DETAIL.RO_STATUS != '1320'
            and (DATEDIFF(day, EPS_T_PO_HEADER.DELIVERY_DATE,CONVERT(char(10), GETDATE(), 112)) <= 0) ";
if($sRoleId == 'ROLE_02' || $sRoleId == 'ROLE_04' || $sRoleId == 'ROLE_05' || $sRoleId == 'ROLE_09' || $sRoleId == 'ROLE_11' )
{
    $query_select_t_po_detail .= "and EPS_T_PO_HEADER.ISSUED_BY IN ($npkProcCriteria)";
    $query_select_t_po_detail .= "and case 
                                        when(substring(EPS_T_TRANSFER.PR_NO, 1, 1)) = 'H' 
                                            then (substring(EPS_T_TRANSFER.PR_NO, 1, 5)) 
                                        else (substring(EPS_T_TRANSFER.PR_NO, 1, 4)) 
                                      end
                                      in (select     
                                        BU_CD
                                      from
                                        EPS_M_PR_PROC_APPROVER
                                      where      
                                        PLANT_ALIAS = '$plantCdAlias')";
}  
$query_select_t_po_detail .= "order by 
                                convert(DATETIME, DELIVERY_DATE, 103)
                                ,EPS_T_PO_HEADER.SUPPLIER_NAME ";
$sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC))
{
    $prNo           = $row_select_t_po_detail['PR_NO'];
    $requesterName  = $row_select_t_po_detail['REQUESTER_NAME'];
    $procAcceptDate = $row_select_t_po_detail['PROC_ACCEPT_DATE'];
    $poNo           = $row_select_t_po_detail['PO_NO'];
    $supplierName   = $row_select_t_po_detail['SUPPLIER_NAME'];
    $issuedDate     = $row_select_t_po_detail['ISSUED_DATE'];
    $sentPoDate     = $row_select_t_po_detail['SEND_PO_DATE'];
    $deliveryDate   = $row_select_t_po_detail['DELIVERY_DATE'];
    $itemCd         = $row_select_t_po_detail['ITEM_CD'];
    $itemName       = $row_select_t_po_detail['ITEM_NAME'];
    $qty            = $row_select_t_po_detail['QTY'];
    $unitCd         = $row_select_t_po_detail['UNIT_CD'];
    $currencyCd     = $row_select_t_po_detail['CURRENCY_CD'];
    $itemPrice      = $row_select_t_po_detail['ITEM_PRICE'];
    $totalReceivedQty = $row_select_t_po_detail['TOTAL_RECEIVED_QTY'];
    $totalCanceledQty = $row_select_t_po_detail['TOTAL_CANCELED_QTY'];
    $totalOpenedQty = $row_select_t_po_detail['TOTAL_OPENED_QTY'];
    $newChargedBu   = $row_select_t_po_detail['NEW_CHARGED_BU'];
    $newItemTypeCd  = $row_select_t_po_detail['NEW_ITEM_TYPE_CD'];
    $newAccountNo   = $row_select_t_po_detail['NEW_ACCOUNT_NO'];
    $newRfiNo       = $row_select_t_po_detail['NEW_RFI_NO'];
    $buName         = $row_select_t_po_detail['BU_NAME'];
    $countDateDiff  = $row_select_t_po_detail['COUNT_DATE_DIFF'];
    $totalOpenQty   = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
                         
    
    if($newItemTypeCd == '1' || $newItemTypeCd == '3' || $newItemTypeCd == '4' || $newItemTypeCd == '5'){
        $newItemTypeCd = 'E';
        $newCip = $newAccountNo;
    }else{
        $newItemTypeCd = 'I';
        $newCip = $newRfiNo;
    }
    
    if($countDateDiff < 1)
    {
        $outFlag = '';
    }
    else if($countDateDiff < 8){
        $outFlag = '*';
    }
    else if($countDateDiff < 15){
        $outFlag = '**';
    } 
    else if($countDateDiff < 22){
        $outFlag = '***';
    }
    else{
        $outFlag = '****';
    }
    /*if($countDateDiff <= 0)
    {
        $outFlag = '';
    }
    else if($countDateDiff >= 1 && $countDateDiff < 8){
        $outFlag = '*';
    }
    else if($countDateDiff >= 8 && $countDateDiff < 15){
        $outFlag = '**';
    } 
    else if($countDateDiff >= 15 && $countDateDiff < 22){
        $outFlag = '***';
    }
    else{
        $outFlag = '****';
    }
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
    else if($countDateDiff >= 22 && $countDateDiff < 29)
    {
        $outFlag = '***';
    }
    else
    {
	$outFlag = '**** ';
    }*/
    
    $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$prNo);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$requesterName);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$procAcceptDate);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$poNo);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$issuedDate);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$sentPoDate);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$supplierName);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$deliveryDate);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$outFlag);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,stripslashes($itemName));
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$totalOpenQty);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$qty);
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$numrow,$currencyCd);
    $objPHPExcel->getActiveSheet()->getStyle('O'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$numrow,$itemPrice);
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$numrow,$newChargedBu);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$numrow,$buName);
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$numrow,$newItemTypeCd);
    $objPHPExcel->getActiveSheet()->setCellValue('S'.$numrow,$newCip);
    $numrow++;
    $itemNo++;
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('EPS_OnProgress_Delivery');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EPS_OnProgress_Delivery.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objPHPExcel->save('php://output');
?>
