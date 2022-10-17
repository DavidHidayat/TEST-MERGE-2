<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB');
set_time_limit(1800);
ini_set('memory_limit', '512M'); 

/** Include PHPExcel */
require_once '../LIB/PHPExcel/Classes/PHPExcel.php';

include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set zoom level
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);

// Set document properties
$objPHPExcel->getProperties()->setCreator("IS Division")
			     ->setLastModifiedBy("Administrator")
			     ->setTitle("Download PO Search")
			     ->setSubject("PO List")
		   	     ->setDescription("PO List by criteria")
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
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);

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
$objPHPExcel->getActiveSheet()->getStyle('T1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('U1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);         

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
$objPHPExcel->getActiveSheet()->getStyle('T1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");  
$objPHPExcel->getActiveSheet()->getStyle('U1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");      

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
$objPHPExcel->getActiveSheet()->getStyle('T1')->getFont()->setSize(12)->setBold(true);   
$objPHPExcel->getActiveSheet()->getStyle('U1')->getFont()->setSize(12)->setBold(true);  

$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
$objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("G1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
$objPHPExcel->getActiveSheet()->getStyle("H1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->getStyle("I1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("J1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->getStyle("K1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
$objPHPExcel->getActiveSheet()->getStyle("L1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("M1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("N1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("O1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
$objPHPExcel->getActiveSheet()->getStyle("P1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("Q1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("R1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("S1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("T1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("U1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
$objPHPExcel->getActiveSheet()->setCellValue('B1', "PO NO");  	
$objPHPExcel->getActiveSheet()->setCellValue('C1', "PO STATUS");  
$objPHPExcel->getActiveSheet()->setCellValue('D1', "PO SEND"); 
$objPHPExcel->getActiveSheet()->setCellValue('E1', "SUPPLIER");  
$objPHPExcel->getActiveSheet()->setCellValue('F1', "DUE DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('G1', "PR NO");  
$objPHPExcel->getActiveSheet()->setCellValue('H1', "PR ACCEPTED"); 
$objPHPExcel->getActiveSheet()->setCellValue('I1', "REQUESTER"); 
$objPHPExcel->getActiveSheet()->setCellValue('J1', "ITEM CODE"); 
$objPHPExcel->getActiveSheet()->setCellValue('K1', "ITEM NAME"); 
$objPHPExcel->getActiveSheet()->setCellValue('L1', "EXP/RFI"); 
$objPHPExcel->getActiveSheet()->setCellValue('M1', "UM");  
$objPHPExcel->getActiveSheet()->setCellValue('N1', "CURRENCY");  
$objPHPExcel->getActiveSheet()->setCellValue('O1', "ITEM PRICE"); 
$objPHPExcel->getActiveSheet()->setCellValue('P1', "AMOUNT");  
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "ORDER QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('R1', "OPEN QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('S1', "DELIVERY STATUS");  
$objPHPExcel->getActiveSheet()->setCellValue('T1', "CLOSED ITEM DATE");   
$objPHPExcel->getActiveSheet()->setCellValue('U1', "CN NO"); 

// FREEZEPANE            
$objPHPExcel->getActiveSheet()->freezePane('A2');

$poNoCriteria       	= trim($_GET['poNo']);
$poDateCriteria     	= trim($_GET['poDate']);
$poDateEndCriteria  	= trim($_GET['poDateEnd']);
$deliveryDateCriteria	= trim($_GET['deliveryDate']);
$requesterCriteria  	= trim($_GET['requester']);
$prNoCriteria       	= trim($_GET['prNo']);
$supplierCdCriteria 	= trim($_GET['supplierCd']);
$supplierNameCriteria   = trim($_GET['supplierName']);
$deliveryPlantCriteria  = trim($_GET['deliveryPlant']);
$poIssuedByCriteria     = trim($_GET['poIssuedBy']);
$poApproverCriteria     = trim($_GET['poApprover']);
$itemTypeCriteria   	= trim($_GET['itemType']); 
$expNoCriteria      	= trim($_GET['expNo']); 
$invNoCriteria      	= trim($_GET['invNo']);
$rfiNoCriteria      	= trim($_GET['rfiNo']); 
$itemStatusCriteria 	= trim($_GET['itemStatus']);
$prChargedCriteria 	= trim($_GET['prCharged']);
$poStatusCriteria   	= trim($_GET['poSts']);
$sentPoDateCriteria   	= trim($_GET['sentPoDate']);
$roStatusCriteria   	= trim($_GET['roSts']);
$itemNameCriteria   	= trim($_GET['itemName']);
$currencyCdCriteria   	= trim($_GET['currencyCd']);
$closedPoMonthCriteria  = trim($_GET['closedPoMonth']);
$cnNoCriteria           = trim($_GET['cnNo']);

$wherePoSelect = array();
if($poNoCriteria){
    $wherePoSelect[] = "EPS_T_PO_DETAIL.PO_NO = '".$poNoCriteria."'";
}
if($poDateCriteria && !$poDateEndCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE = '".encodeDate($poDateCriteria)."'";
}
if(!$poDateCriteria && $poDateEndCriteria ){
    $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE = '".encodeDate($poDateEndCriteria)."'";
}
if($poDateCriteria && $poDateEndCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.ISSUED_DATE >= '".encodeDate($poDateCriteria)."'
                        and EPS_T_PO_HEADER.ISSUED_DATE <= '".encodeDate($poDateEndCriteria)."'";
}
if($deliveryDateCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.DELIVERY_DATE = '".encodeDate($deliveryDateCriteria)."'";
}
if($requesterCriteria){
    $wherePoSelect[] = "EPS_M_EMPLOYEE.NAMA1 like '".$requesterCriteria."%'";
}
if($prNoCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.PR_NO LIKE '%".$prNoCriteria."%'";
}
if($supplierCdCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.SUPPLIER_CD = '".$supplierCdCriteria."'";
}
if($supplierNameCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.SUPPLIER_NAME = '".$supplierNameCriteria."'";
}
if($deliveryPlantCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.DELIVERY_PLANT = '".$deliveryPlantCriteria."'";
}
if($poIssuedByCriteria){
    $wherePoSelect[] = "EPS_M_EMPLOYEE_3.NAMA1 like '".$poIssuedByCriteria."%'";
}
if($poApproverCriteria){
    $wherePoSelect[] = "EPS_M_EMPLOYEE_2.NAMA1 like '".$poApproverCriteria."%'";
}
if($itemTypeCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ITEM_TYPE_CD = '".$itemTypeCriteria."'";
}
if($expNoCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ACCOUNT_NO = '".$expNoCriteria."'";
}
if($invNoCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_ACCOUNT_NO = '".$invNoCriteria."'";
}
if($rfiNoCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_RFI_NO = '".$rfiNoCriteria."'";
}
if($itemStatusCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.ITEM_STATUS = '".$itemStatusCriteria."'";
}
if($prChargedCriteria){
    $wherePoSelect[] = "EPS_T_TRANSFER.NEW_CHARGED_BU = '".$prChargedCriteria."'";
}
if($poStatusCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.PO_STATUS = '".$poStatusCriteria."'";
}
if($sentPoDateCriteria){
    $wherePoSelect[] = "convert (VARCHAR(10), EPS_T_PO_HEADER.SEND_PO_DATE, 103) =  '$sentPoDateCriteria'";
}
if($roStatusCriteria){
    $wherePoSelect[] = "EPS_T_PO_DETAIL.RO_STATUS = '".$roStatusCriteria."'";
}
if($itemNameCriteria){
    $wherePoSelect[] = "EPS_T_PO_DETAIL.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
}
if($currencyCdCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.CURRENCY_CD = '".$currencyCdCriteria."'";
}
if($closedPoMonthCriteria){
    $wherePoSelect[] = "EPS_T_PO_HEADER.CLOSED_PO_MONTH = '".$closedPoMonthCriteria."'";
}
if($cnNoCriteria){
    $wherePoSelect[] = "EPS_T_CN_DETAIL.CN_NO = '".$cnNoCriteria."'";
}

$query_t_ro_select = "
                        select
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID
                            ,EPS_T_PO_DETAIL.PO_NO
                            ,EPS_T_PO_HEADER.SUPPLIER_NAME
                            ,EPS_T_PO_HEADER.CURRENCY_CD
                            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
                            ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                            ,EPS_T_PO_DETAIL.ITEM_CD
                            ,EPS_T_PO_DETAIL.ITEM_NAME
                            ,EPS_T_PO_DETAIL.QTY
                            ,EPS_T_PO_DETAIL.UNIT_CD
                            ,EPS_T_PO_DETAIL.ITEM_PRICE
                            ,EPS_T_PO_DETAIL.AMOUNT
                            ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                            ,EPS_T_PO_DETAIL.ACCOUNT_NO
                            ,EPS_T_PO_DETAIL.RFI_NO
                            ,convert(VARCHAR(24), EPS_T_PO_DETAIL.UPDATE_DATE, 103) as CLOSED_ITEM_DATE
                            ,isnull(
                                (select 
                                    sum(TRANSACTION_QTY)
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
                                (select 
                                    sum(TRANSACTION_QTY)
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
                                (select 
                                    sum(TRANSACTION_QTY)
                                 from 
                                    EPS_T_RO_DETAIL
                                 where   
                                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                              ,0
                              )
                            as TOTAL_OPENED_QTY
                            ,EPS_T_PO_DETAIL.RO_STATUS
                            ,EPS_M_APP_STATUS.APP_STATUS_NAME as RO_STATUS_NAME
                            ,EPS_T_TRANSFER.PR_NO
                            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
                            ,EPS_T_PO_HEADER.PO_STATUS
                            ,EPS_M_APP_STATUS_2.APP_STATUS_NAME as PO_STATUS_NAME
                            ,(select count(*)
                                from          
                                    EPS_T_TRANSFER_SUPPLIER
                                where      
                                    EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER_SUPPLIER.TRANSFER_ID) 
                            as TOTAL_SUPPLIER
                            ,convert(VARCHAR(24), EPS_T_PR_HEADER.PROC_ACCEPT_DATE, 103) as PROC_ACCEPT_DATE
                            ,EPS_T_CN_DETAIL.CN_NO
                        from
                            EPS_T_PO_DETAIL 
                        left join
                            EPS_T_PO_HEADER
                        on
                            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                        left join
                            EPS_M_APP_STATUS 
                        on 
                            EPS_M_APP_STATUS.APP_STATUS_CD = EPS_T_PO_DETAIL.RO_STATUS
                        left join
                            EPS_M_APP_STATUS EPS_M_APP_STATUS_2
                        on 
                            EPS_M_APP_STATUS_2.APP_STATUS_CD = EPS_T_PO_HEADER.PO_STATUS
                        left join
                            EPS_T_TRANSFER
                        on
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                        left join
                            EPS_M_EMPLOYEE 
                        on 
                            EPS_T_TRANSFER.REQUESTER = EPS_M_EMPLOYEE.NPK
                        left join 
                            EPS_T_PR_HEADER
                        on
                            EPS_T_TRANSFER.PR_NO = EPS_T_PR_HEADER.PR_NO
                        left join
                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2 
                        on 
                            EPS_T_PO_HEADER.APPROVER = EPS_M_EMPLOYEE_2.NPK 
                        left join
                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_3 
                        on 
                            EPS_T_PO_HEADER.ISSUED_BY = EPS_M_EMPLOYEE_3.NPK
                        left join
                            EPS_T_CN_DETAIL
                        on 
                            EPS_T_PO_DETAIL.PO_NO = EPS_T_CN_DETAIL.PO_NO
                            and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_CN_DETAIL.REF_TRANSFER_ID ";
if(count($wherePoSelect)) {
    $query_t_ro_select .= "where " . implode(' and ', $wherePoSelect);
}
$query_t_ro_select .= " order by 
                            EPS_T_PO_HEADER.PO_NO
                            ,EPS_T_PO_DETAIL.REF_TRANSFER_ID ";
$numrow = 2;
$poListNo = 1;
$sql_t_ro_select = $conn->query($query_t_ro_select);
while($row_t_ro_select = $sql_t_ro_select->fetch(PDO::FETCH_ASSOC))
{
    $refTransferId  = $row_t_ro_select['REF_TRANSFER_ID'];
    $poNo           = $row_t_ro_select['PO_NO'];
    $supplierName   = $row_t_ro_select['SUPPLIER_NAME'];
    $currencyCd     = $row_t_ro_select['CURRENCY_CD'];
    $deliveryDate   = $row_t_ro_select['DELIVERY_DATE'];
    $itemCd         = $row_t_ro_select['ITEM_CD'];
    $itemName       = $row_t_ro_select['ITEM_NAME'];
    $itemPrice      = $row_t_ro_select['ITEM_PRICE'];
    $amount         = $row_t_ro_select['AMOUNT'];
    $qty            = $row_t_ro_select['QTY'];
    $totalReceivedQty= $row_t_ro_select['TOTAL_RECEIVED_QTY'];
    $totalCanceledQty= $row_t_ro_select['TOTAL_CANCELED_QTY'];
    $totalOpenedQty = $row_t_ro_select['TOTAL_OPENED_QTY'];
    $unitCd         = $row_t_ro_select['UNIT_CD'];
    $roStatus       = $row_t_ro_select['RO_STATUS'];
    $roStatusName   = $row_t_ro_select['RO_STATUS_NAME'];
    $prNo           = $row_t_ro_select['PR_NO'];
    $requesterName  = $row_t_ro_select['REQUESTER_NAME'];
    $poStatusName   = $row_t_ro_select['PO_STATUS_NAME'];
    $itemTypeCd     = $row_t_ro_select['ITEM_TYPE_CD'];
    $accountNo      = $row_t_ro_select['ACCOUNT_NO'];
    $rfiNo          = $row_t_ro_select['RFI_NO'];
    if($roStatus == '1320')
    {
        $closedItemDate = $row_t_ro_select['CLOSED_ITEM_DATE'];
    }
    else
    {
        $closedItemDate = '';
    }
    $totalSupplier  = $row_t_ro_select['TOTAL_SUPPLIER'];
    $poSendDate     = $row_t_ro_select['SEND_PO_DATE'];
    $procAcceptDate = $row_t_ro_select['PROC_ACCEPT_DATE'];
    $cnNo           = $row_t_ro_select['CN_NO'];
                                        
    if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4' || $itemTypeCd == '5')
    {
        $objectAccount = $accountNo;
    }
    if($itemTypeCd == '2')
    {
        $objectAccount = $rfiNo;
    }
    if(strlen($objectAccount) == 1)
    {
        $objectAccount = '0'.$accountNo;
    }
            
    $totalOpenQty   = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
    
    $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$poListNo);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$poNo);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$poStatusName);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$poSendDate);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$supplierName);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$deliveryDate);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$prNo);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$procAcceptDate);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$requesterName);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$itemCd);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$itemName);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$objectAccount);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$unitCd);
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$numrow,$currencyCd);
    $objPHPExcel->getActiveSheet()->getStyle('O'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$numrow,$itemPrice);
    $objPHPExcel->getActiveSheet()->getStyle('P'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$numrow,$amount);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$numrow,$qty);
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$numrow,$totalOpenQty);
    $objPHPExcel->getActiveSheet()->setCellValue('S'.$numrow,$roStatusName);
    $objPHPExcel->getActiveSheet()->setCellValue('T'.$numrow,$closedItemDate);
    $objPHPExcel->getActiveSheet()->setCellValue('U'.$numrow,$cnNo);
    $numrow++;
    $poListNo++; 
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('EPS_PO_Search');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EPS_PO_Search.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objPHPExcel->save('php://output');
?>
