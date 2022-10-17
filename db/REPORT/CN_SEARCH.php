<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
set_time_limit(1800);
ini_set('memory_limit', '256M');

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
			     ->setTitle("Download CN Search")
			     ->setSubject("CN List")
		   	     ->setDescription("CN List by criteria")
			     ->setKeywords("EPS")
		      	 ->setCategory("CN");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
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

$objPHPExcel->getActiveSheet()->setCellValue('A1', "ADR NO.");  
$objPHPExcel->getActiveSheet()->setCellValue('B1', "SUPPLIER CODE");  	
$objPHPExcel->getActiveSheet()->setCellValue('C1', "SUPPLIER NAME");  
$objPHPExcel->getActiveSheet()->setCellValue('D1', "CREDIT NOTE NO"); 
$objPHPExcel->getActiveSheet()->setCellValue('E1', "PO NO");  
$objPHPExcel->getActiveSheet()->setCellValue('F1', "CHARGED BU");  
$objPHPExcel->getActiveSheet()->setCellValue('G1', "LOC");  
$objPHPExcel->getActiveSheet()->setCellValue('H1', "ITEM NAME"); 
$objPHPExcel->getActiveSheet()->setCellValue('I1', "RECV DATE"); 
$objPHPExcel->getActiveSheet()->setCellValue('J1', "QTY"); 
$objPHPExcel->getActiveSheet()->setCellValue('K1', "UM"); 
$objPHPExcel->getActiveSheet()->setCellValue('L1', "ITEM PRICE");  
$objPHPExcel->getActiveSheet()->setCellValue('M1', "AMOUNT");  
$objPHPExcel->getActiveSheet()->setCellValue('N1', "OBJ.ACCOUNT"); 
$objPHPExcel->getActiveSheet()->setCellValue('O1', "VAT");  
$objPHPExcel->getActiveSheet()->setCellValue('P1', "CURRENCY");  
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "PR NO");  

// FREEZEPANE            
$objPHPExcel->getActiveSheet()->freezePane('A2');

$numrow = 2;
$poListNo = 1;
$poTotalAmount  = 0;
                                        $query_select_t_po = "select distinct
                                                                EPS_T_PO_HEADER.PO_NO
                                                                ,EPS_T_PO_HEADER.COMPANY_CD
                                                                ,EPS_T_PO_HEADER.PO_STATUS
                                                                ,EPS_T_PO_HEADER.SUPPLIER_CD
                                                                ,EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                ,EPS_T_PO_HEADER.APPROVER
                                                                ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2) 
                                                                + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) 
                                                                + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                                                                ,EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                ,EPS_T_PO_DETAIL.ITEM_CD
                                                                ,EPS_T_PO_DETAIL.ITEM_NAME
                                                                ,EPS_T_PO_DETAIL.QTY
                                                                ,EPS_T_PO_DETAIL.ITEM_PRICE
                                                                ,EPS_T_PO_DETAIL.AMOUNT
                                                                ,EPS_T_PO_HEADER.CURRENCY_CD
                                                                ,EPS_T_PO_DETAIL.UNIT_CD
                                                                ,EPS_T_PO_DETAIL.RO_STATUS
                                                                ,EPS_T_PO_DETAIL.ITEM_TYPE_CD
                                                                ,EPS_T_PO_DETAIL.ACCOUNT_NO
                                                                ,EPS_T_PO_DETAIL.RFI_NO
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
                                                                ,EPS_T_TRANSFER.PR_NO
                                                                ,EPS_M_ACCOUNT.ACCOUNT_CD
                                                                ,EPS_T_TRANSFER.NEW_CHARGED_BU
                                                                ,EPS_T_PO_HEADER.DELIVERY_PLANT
                                                                ,convert(VARCHAR(24), EPS_T_PO_HEADER.CLOSED_PO_DATE, 103) as CLOSED_PO_DATE
                                                                ,EPS_M_ACCOUNT.ITEM_TYPE_CD as ITEM_TYPE_CD_ACC
                                                            from
                                                                EPS_T_PO_DETAIL
                                                            left join
                                                                EPS_T_PO_HEADER
                                                            on 
                                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO
                                                            left join
                                                                EPS_T_RO_DETAIL
                                                            on 
                                                                EPS_T_PO_DETAIL.PO_NO = EPS_T_RO_DETAIL.PO_NO
                                                                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_RO_DETAIL.REF_TRANSFER_ID
                                                            left join
                                                                EPS_T_TRANSFER
                                                            on
                                                                EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
                                                            left join
                                                                EPS_M_ACCOUNT
                                                            on
                                                                EPS_T_PO_DETAIL.ACCOUNT_NO = EPS_M_ACCOUNT.ACCOUNT_NO
                                                            where
                                                                EPS_T_PO_HEADER.PO_STATUS = '1280'
                                                            order by
                                                                EPS_T_PO_HEADER.SUPPLIER_NAME
                                                                ,EPS_T_PO_HEADER.COMPANY_CD
                                                                ,EPS_T_PO_HEADER.PO_NO ";
                                        $sql_select_t_po = $conn->query($query_select_t_po);
                                        while($row_select_t_po = $sql_select_t_po->fetch(PDO::FETCH_ASSOC)){
                                            $poNo           = $row_select_t_po['PO_NO'];
                                            $companyCd      = $row_select_t_po['COMPANY_CD'];
                                            $refTransferId  = $row_select_t_po['REF_TRANSFER_ID'];
                                            $poStatus       = $row_select_t_po['PO_STATUS'];
                                            $supplierCd     = $row_select_t_po['SUPPLIER_CD'];
                                            $supplierName   = $row_select_t_po['SUPPLIER_NAME'];
                                            $deliveryDate   = $row_select_t_po['DELIVERY_DATE'];
                                            $approver       = $row_select_t_po['APPROVER'];
                                            $currencyCd     = $row_select_t_po['CURRENCY_CD'];
                                            $itemCd         = $row_select_t_po['ITEM_CD'];
                                            $itemName       = $row_select_t_po['ITEM_NAME'];
                                            $qty            = $row_select_t_po['QTY'];
                                            $itemPrice      = $row_select_t_po['ITEM_PRICE'];
                                            $amount         = $row_select_t_po['AMOUNT'];
                                            $unitCd         = $row_select_t_po['UNIT_CD'];
                                            $roStatus       = $row_select_t_po['RO_STATUS'];
                                            $totalReceivedQty= $row_select_t_po['TOTAL_RECEIVED_QTY'];
                                            $totalCanceledQty= $row_select_t_po['TOTAL_CANCELED_QTY'];
                                            $totalOpenedQty = $row_select_t_po['TOTAL_OPENED_QTY'];
                                            //$transactionQty = $row_select_t_po['TRANSACTION_QTY'];
                                            //$transactionFlag= $row_select_t_po['TRANSACTION_FLAG'];
                                            //$createDate     = $row_select_t_po['CREATE_DATE'];
                                            $prNo           = $row_select_t_po['PR_NO'];
                                            $itemTypeCd     = $row_select_t_po['ITEM_TYPE_CD'];
                                            $accountNo      = $row_select_t_po['ACCOUNT_NO'];
                                            $accountCd      = $row_select_t_po['ACCOUNT_CD'];
                                            $rfiNo          = $row_select_t_po['RFI_NO'];
                                            $chargedBu      = $row_select_t_po['NEW_CHARGED_BU'];
                                            $deliveryPlant  = $row_select_t_po['DELIVERY_PLANT'];
                                            $closedPoDate   = $row_select_t_po['CLOSED_PO_DATE'];
                                            $totalActualReceivedQty = $totalReceivedQty - $totalCanceledQty - $totalOpenedQty;
                                            $itemNo++;
                                            $poTotalAmount  = $poTotalAmount + $amount;
                                            
                                            
                                            if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
                                            {
                                                $objectAccount = $accountCd;
                                            }
                                            if($itemTypeCd == '2')
                                            {
                                                $objectAccount = $rfiNo;
                                            }
                                            
                                            if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
                                                    && $deliveryPlant =='JK')
                                            {
                                                $chargedBu = trim($chargedBu).'S';
                                            }
                                            if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
                                                    && $deliveryPlant == 'GT')
                                            {
                                               //$chargedBu = trim($chargedBu).'C';
                                                                        // Update 1 Mar 2018 . TACI no need modify charged BU
                                                                        $chargedBu = trim($chargedBu);
                                            }
                                            if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
                                                    && $deliveryPlant =='JF')
                                            {
                                                $chargedBu = trim($chargedBu).'F';
                                            }
                                            if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
                                                    && $deliveryPlant =='SI')
                                            {
                                                $chargedBu = trim($chargedBu).'S';
                                            }
                                            if($itemTypeCd == '3' && $deliveryPlant =='JK')
                                            {
                                                $chargedBu = '1000S';
                                            }
    if($itemTypeCd == '3' && $deliveryPlant == 'GT')
    {
        $chargedBu = 'T1000';
    }
    if($itemTypeCd == '3' && $deliveryPlant =='JF')
    {
        $chargedBu = '1000F';
    }
    if($itemTypeCd == '4' && $deliveryPlant =='SI')
    {
        $chargedBu = '1001S';
    }

    $split = explode('.', $totalActualReceivedQty);
    if($split[1] == 0)
    {
        $totalActualReceivedQty = number_format($totalActualReceivedQty);
    }
    else
    {
        $totalActualReceivedQty = $totalActualReceivedQty;
    }
    
    $split_item_price = explode('.', $itemPrice);
    if($split_item_price[1] == 0)
    {
        $itemPrice = number_format($itemPrice);
    }
    else
    {
        $itemPrice = number_format($itemPrice,2);
    }
         
    $split_amount = explode('.', $amount);
    if($split_amount[1] == 0)
    {
        $amount = number_format($amount);
    }
    else
    {
        $amount = number_format($amount,2);
    }
    $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,'');
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$supplierCd);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$supplierName);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,'');
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$poNo);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$chargedBu);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$deliveryPlant);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$itemName);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$closedPoDate);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$totalActualReceivedQty);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$unitCd);
    $objPHPExcel->getActiveSheet()->getStyle('L'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$itemPrice);
    $objPHPExcel->getActiveSheet()->getStyle('M'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$amount);
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$numrow,$objectAccount);
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$numrow,'');
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$numrow,$currencyCd);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$numrow,$prNo);
    $numrow++;
    $poListNo++; 
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('EPS_CN_Search');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EPS_CN_Search.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objPHPExcel->save('php://output');
//$objPHPExcel->save('pr-search.xlsx');
?>
