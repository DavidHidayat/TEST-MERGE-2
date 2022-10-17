<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
ini_set('memory_limit', '128M');
set_time_limit(1800);

/** Include PHPExcel */
require_once '../LIB/PHPExcel/Classes/PHPExcel.php';
//require_once '../LIB/PHPExcel/Classes/PHPExcel/IOFactory.php';

include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set zoom level
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);

$criteria 			= $_GET['criteria'];
//$companyCdCriteria  = trim($_GET['companyCd']);
$companyCdCriteria  = "T";
$currencyCdCrtieria = trim($_GET['currencyCd']);

$closingMonthArray = array();

$query_select_t_cn_header_groupby_month = "select     
                                            CLOSING_MONTH
                                           from         
                                            EPS_T_CN_HEADER
                                           group by
                                            CLOSING_MONTH
                                           order by 
                                            CLOSING_MONTH";
$sql_select_t_cn_header_groupby_month = $conn->query($query_select_t_cn_header_groupby_month);
while($row_select_t_cn_header_groupby_month = $sql_select_t_cn_header_groupby_month->fetch(PDO::FETCH_ASSOC))
{  
    $closingMonth = $row_select_t_cn_header_groupby_month['CLOSING_MONTH'];
    $closingMonthArray[] = array(
                                'closingMonth' => $closingMonth
                            );
    $addClosingMonthArray = $closingMonthArray;
}
   
/**********************************************************************
 * SUMMARY GS PURCHASE AMOUNT BY SUPPLIER
 **********************************************************************/
if($criteria == 'SummaryAmountBySupplier')
{
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("IS Division")
                                ->setLastModifiedBy("Administrator")
                                ->setTitle("AMOUNT BY SUPPLIER")
                                ->setSubject("AMOUNT BY SUPPLIER")
                                ->setDescription("AMOUNT BY SUPPLIER")
                                ->setKeywords("AMOUNT BY SUPPLIER")
                                ->setCategory("EPS SUMMARY");
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
   
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
   
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO.");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "SUPPLIER CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "SUPPLIER NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "CURRENCY CODE"); 

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');

    $indexArray = 1;
    foreach($addClosingMonthArray as $addClosingMonthArrays)
    {
        $closingMonthVal = $addClosingMonthArrays['closingMonth'];
        if($indexArray == 1)
        {
            $closingMonthCriteria = "sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then TAXABLE_AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        else
        {
            $closingMonthCriteria = $closingMonthCriteria. ", ". " sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then TAXABLE_AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        $indexArray ++;
    }
    
    $numrow         = 2;
    $listNo         = 1;
    $i              = 0;
    $query_select_t_cn_header_groupby_supplier = "select 
                                                    EPS_T_CN_HEADER.SUPPLIER_CD
                                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                                    ,EPS_T_CN_HEADER.CURRENCY_CD
                                                    ,".$closingMonthCriteria."
                                                from
                                                    EPS_T_CN_HEADER
                                                where
                                                    EPS_T_CN_HEADER.COMPANY_CD = '$companyCdCriteria'
                                                group by
                                                    EPS_T_CN_HEADER.SUPPLIER_CD
                                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                                    ,EPS_T_CN_HEADER.CURRENCY_CD ";
    $sql_select_t_cn_header_groupby_supplier = $conn->query($query_select_t_cn_header_groupby_supplier);
    while($row_select_t_cn_header_groupby_supplier = $sql_select_t_cn_header_groupby_supplier->fetch(PDO::FETCH_ASSOC)){  
        $supplierCd     = $row_select_t_cn_header_groupby_supplier['SUPPLIER_CD'];
        $supplierName   = $row_select_t_cn_header_groupby_supplier['SUPPLIER_NAME'];
        $currencyCd     = $row_select_t_cn_header_groupby_supplier['CURRENCY_CD'];
                                                
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$listNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$supplierCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$supplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$currencyCd);
        
        $columnID = 'E';
        $totalTaxableAmount = 0;
        foreach($addClosingMonthArray as $addClosingMonthArrays)
        {
            $columnName     = $addClosingMonthArrays['closingMonth'];
            $taxableAmount    = $row_select_t_cn_header_groupby_supplier[$columnName];
            if ($taxableAmount == '')
            {
                $taxableAmount = 0;
            }
            $selectMonth = substr($columnName,4);
            $monthName = date('M', mktime(0, 0, 0, $selectMonth, 10)); // March
            $selectYear = substr($columnName,0,4);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper($monthName)."-".$selectYear);
            $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow, $taxableAmount);
            
            $totalTaxableAmount = $totalTaxableAmount + $taxableAmount;
            $columnID++;
        }
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               
        $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper("ACCUMULATION"));
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow,$totalTaxableAmount);
		
		$colIndex = PHPExcel_Cell::columnIndexFromString($columnID);
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex); 
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter."1", strtoupper("AVG/MONTH"));
        
        $countMonth = count($addClosingMonthArray);
        $avgMonth = $totalTaxableAmount/$countMonth;
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter.$numrow,$avgMonth);
        
        $i++;
        $numrow++;
        $listNo++; 
    }
    $setWorksheetName = "EPS_PURCHASE_BY_SUPPLIER_".$companyCdCriteria;
}

if($criteria == 'SummaryAmountByItemTypeFy')
{
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("IS Division")
                                ->setLastModifiedBy("Administrator")
                                ->setTitle("AMOUNT BY SUPPLIER")
                                ->setSubject("AMOUNT BY SUPPLIER")
                                ->setDescription("AMOUNT BY SUPPLIER")
                                ->setKeywords("AMOUNT BY SUPPLIER")
                                ->setCategory("EPS SUMMARY");
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
   
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
   
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO.");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "SUPPLIER CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "SUPPLIER NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "CURRENCY CODE"); 

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    
    $itemType = trim($_GET['itemType']);
    $periodYear = trim($_GET['periodYear']);
    
    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');

    $query_select_m_range_fy = "select
                                    START_FY
                                    ,END_FY
                                from
                                    EPS_M_RANGE_FY
                                where
                                    FY = '$periodYear'";
    $sql_select_m_range_fy = $conn->query($query_select_m_range_fy);
    $row_select_m_range_fy = $sql_select_m_range_fy->fetch(PDO::FETCH_ASSOC);
    if($row_select_m_range_fy)
    {
        $startFy = $row_select_m_range_fy['START_FY'];
        $endFy = $row_select_m_range_fy['END_FY'];
    }
    
    $closingMonthArray  = array();
    $query_select_t_cn_header_groupby_month = "select     
                                                CLOSING_MONTH
                                              from         
                                                EPS_T_CN_HEADER
                                              where     
                                                (CLOSING_MONTH BETWEEN '$startFy' and '$endFy')
                                              group by
                                                CLOSING_MONTH
                                              order by 
                                                CLOSING_MONTH";
    $sql_select_t_cn_header_groupby_month = $conn->query($query_select_t_cn_header_groupby_month);
    while($row_select_t_cn_header_groupby_month = $sql_select_t_cn_header_groupby_month->fetch(PDO::FETCH_ASSOC))
    {  
        $closingMonth = $row_select_t_cn_header_groupby_month['CLOSING_MONTH'];
        $closingMonthArray[] = array(
                                    'closingMonth' => $closingMonth
                                );
        $addClosingMonthArray = $closingMonthArray;
    }

    $indexArray = 1;
    foreach($addClosingMonthArray as $addClosingMonthArrays)
    {
        $closingMonthVal = $addClosingMonthArrays['closingMonth'];
        if($indexArray == 1)
        {
            $closingMonthCriteria = "sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        else
        {
            $closingMonthCriteria = $closingMonthCriteria. ", ". " sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        $indexArray ++;
    }
    
    $numrow         = 2;
    $listNo         = 1;
    $i              = 0;
    $query_select_t_cn_header_groupby_supplier = "select 
                                                    EPS_T_CN_HEADER.SUPPLIER_CD
                                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                                    ,EPS_T_CN_HEADER.CURRENCY_CD
                                                    ,".$closingMonthCriteria."
                                                from
                                                    EPS_T_CN_HEADER
                                                left join
                                                    EPS_T_CN_DETAIL 
                                                on
                                                    EPS_T_CN_HEADER.CN_HDR_ID = EPS_T_CN_DETAIL.CN_TRANSFER_ID
                                                where
                                                    EPS_T_CN_HEADER.COMPANY_CD = '$companyCdCriteria'
                                                    and (EPS_T_CN_HEADER.CLOSING_MONTH between '$startFy' and '$endFy') ";
    if($itemType == 'E')
    {
        $query_select_t_cn_header_groupby_supplier .= "and EPS_T_CN_DETAIL.ITEM_TYPE_CD != '2'";
    }
    if($itemType == 'I')
    {
        $query_select_t_cn_header_groupby_supplier .= "and EPS_T_CN_DETAIL.ITEM_TYPE_CD = '2'";
    }
    $query_select_t_cn_header_groupby_supplier .=" group by
                                                    EPS_T_CN_HEADER.SUPPLIER_CD
                                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                                    ,EPS_T_CN_HEADER.CURRENCY_CD ";
    //echo $query_select_t_cn_header_groupby_supplier;
    $sql_select_t_cn_header_groupby_supplier = $conn->query($query_select_t_cn_header_groupby_supplier);
    while($row_select_t_cn_header_groupby_supplier = $sql_select_t_cn_header_groupby_supplier->fetch(PDO::FETCH_ASSOC)){  
        $supplierCd     = $row_select_t_cn_header_groupby_supplier['SUPPLIER_CD'];
        $supplierName   = $row_select_t_cn_header_groupby_supplier['SUPPLIER_NAME'];
        $currencyCd     = $row_select_t_cn_header_groupby_supplier['CURRENCY_CD'];
                                                
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$listNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$supplierCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$supplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$currencyCd);
        
        $columnID = 'E';
        $totalTaxableAmount = 0;
        foreach($addClosingMonthArray as $addClosingMonthArrays)
        {
            $columnName     = $addClosingMonthArrays['closingMonth'];
            $taxableAmount    = $row_select_t_cn_header_groupby_supplier[$columnName];
            if ($taxableAmount == '')
            {
                $taxableAmount = 0;
            }
            $selectMonth = substr($columnName,4);
            $monthName = date('M', mktime(0, 0, 0, $selectMonth, 10)); // March
            $selectYear = substr($columnName,0,4);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper($monthName)."-".$selectYear);
            $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow, $taxableAmount);
            
            $totalTaxableAmount = $totalTaxableAmount + $taxableAmount;
            $columnID++;
        }
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               
        $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper("ACCUMULATION"));
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow,$totalTaxableAmount);
		
		$colIndex = PHPExcel_Cell::columnIndexFromString($columnID);
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex); 
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter."1", strtoupper("AVG/MONTH"));
        
        $countMonth = count($addClosingMonthArray);
        $avgMonth = $totalTaxableAmount/$countMonth;
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter.$numrow,$avgMonth);
        
        $i++;
        $numrow++;
        $listNo++; 
    }
    
    $setWorksheetName = "EPS_PURCHASE_BY_".$itemType."_".$companyCdCriteria."_FY_".$periodYear;
    
}

/**********************************************************************
 * SUMMARY GS PURCHASE AMOUNT BY SECTION
 **********************************************************************/
if($criteria == 'SummaryAmountBySection')
{
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("IS Division")
                                ->setLastModifiedBy("Administrator")
                                ->setTitle("AMOUNT BY SECTION")
                                ->setSubject("AMOUNT BY SECTION")
                                ->setDescription("AMOUNT BY SECTION")
                                ->setKeywords("AMOUNT BY SECTION")
                                ->setCategory("EPS SUMMARY");
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
   
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO.");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "SECTION CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "SECTION NAME");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');

    $indexArray = 1;
    foreach($addClosingMonthArray as $addClosingMonthArrays)
    {
        $closingMonthVal = $addClosingMonthArrays['closingMonth'];
        if($indexArray == 1)
        {
            $closingMonthCriteria = "sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        else
        {
            $closingMonthCriteria = $closingMonthCriteria. ", ". " sum(case when CLOSING_MONTH = '". $closingMonthVal. "' then AMOUNT end) as '". $closingMonthVal. "'"; 
        }
        $indexArray ++;
    }
    
    $numrow = 2;
    $listNo = 1;
    $i      = 0;
    $query_select_t_cn_header = "select     
                                    CHARGED_BU
                                    ,".$closingMonthCriteria."
                                 from         
                                    EPS_T_CN_DETAIL
                                 left join
                                    EPS_T_CN_HEADER 
                                 on 
                                    EPS_T_CN_DETAIL.CN_NO = EPS_T_CN_HEADER.CN_NO
                                 where
                                    EPS_T_CN_DETAIL.CURRENCY_CD = '$currencyCdCrtieria'
                                 group by
                                    EPS_T_CN_DETAIL.CHARGED_BU
                                 order by 
                                    EPS_T_CN_DETAIL.CHARGED_BU ";
    $sql_select_t_cn_header = $conn->query($query_select_t_cn_header);
    while($row_select_t_cn_header = $sql_select_t_cn_header->fetch(PDO::FETCH_ASSOC)){  
        $chargedBu      = $row_select_t_cn_header['CHARGED_BU'];
             
        if(substr($chargedBu, 0,1) != 'H')
        {
            $newChargedBu = substr($chargedBu, 0,4);
        }
        else
        {
            $newChargedBu = $chargedBu;
        }
        
        $query_select_m_bunit = "select 
                                    BU_NAME
                                from
                                    EPS_M_BUNIT
                                where
                                    BU_CD = '$newChargedBu'";
        $sql_select_m_bunit = $conn->query($query_select_m_bunit);
        $row_select_m_bunit = $sql_select_m_bunit->fetch(PDO::FETCH_ASSOC);
        if($row_select_m_bunit)
        {
            $chargedBuName = $row_select_m_bunit['BU_NAME'];
        }
        
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$listNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$chargedBu);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$chargedBuName);
        
        $columnID = 'D';
        $totalAmount = 0;
        foreach($addClosingMonthArray as $addClosingMonthArrays)
        {
            $columnName         = $addClosingMonthArrays['closingMonth'];
            $totalAmountByMonth = $row_select_t_cn_header[$columnName];
            if ($totalAmountByMonth == '')
            {
                $totalAmountByMonth = 0;
            }
            $selectMonth = substr($columnName,4);
            $monthName = date('M', mktime(0, 0, 0, $selectMonth, 10)); // March
            $selectYear = substr($columnName,0,4);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper($monthName)."-".$selectYear);
            $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow, $totalAmountByMonth);
            
            $totalAmount = $totalAmount + $totalAmountByMonth;
            $columnID++;
        }
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
               
        $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", strtoupper("ACCUMULATION"));
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow,$totalAmount);
        
		$colIndex = PHPExcel_Cell::columnIndexFromString($columnID);
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex); 
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter."1", strtoupper("AVG/MONTH"));
        
        $countMonth = count($addClosingMonthArray);
        $avgMonth = $totalAmount/$countMonth;
        $objPHPExcel->getActiveSheet()->getStyle($columnLetter.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue($columnLetter.$numrow,$avgMonth);
		
        $i++;
        $numrow++;
        $listNo++; 
    }
    $setWorksheetName = "EPS_PURCHASE_BY_SECTION_".$currencyCdCrtieria;
}

/**********************************************************************
 * SUMMARY BUYER PURCHASE BY ITEM
 **********************************************************************/
if($criteria == 'SummaryBuyerByItem')
{
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("IS Division")
                                ->setLastModifiedBy("Administrator")
                                ->setTitle("BUYER PURCHASE BY ITEM")
                                ->setSubject("BUYER PURCHASE BY ITEM")
                                ->setDescription("BUYER PURCHASE BY ITEM")
                                ->setKeywords("BUYER PURCHASE BY ITEM")
                                ->setCategory("EPS SUMMARY");
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
   
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO.");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "BUYER"); 
    
    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    
    $indexArray = 1;
    foreach($addClosingMonthArray as $addClosingMonthArrays)
    {
        $closingMonthVal = $addClosingMonthArrays['closingMonth'];
        if($indexArray == 1)
        {
            $closingMonthCriteria = "count(case when LEFT(CONVERT(varchar, EPS_T_TRANSFER.CREATE_DATE, 112), 6) = '". $closingMonthVal. "' then  EPS_T_TRANSFER.CREATE_BY end) as '". $closingMonthVal. "'"; 
        }
        else
        {
            $closingMonthCriteria = $closingMonthCriteria. ", ". " count(case when  LEFT(CONVERT(varchar, EPS_T_TRANSFER.CREATE_DATE, 112), 6) = '". $closingMonthVal. "' then  EPS_T_TRANSFER.CREATE_BY end) as '". $closingMonthVal. "'"; 
        }
        $indexArray ++;
    }
    
    $indexArray_2 = 1;
    foreach($addClosingMonthArray as $addClosingMonthArrays_2)
    {
        $closingMonthVal = $addClosingMonthArrays_2['closingMonth'];
        if($indexArray_2 == 1)
        {
            $closingMonthCriteria_2 = "count(case when LEFT(CONVERT(varchar, EPS_T_PO_DETAIL.CREATE_DATE, 112), 6) = '". $closingMonthVal. "' then  EPS_T_PO_DETAIL.CREATE_DATE end) as '". $closingMonthVal. "'"; 
        }
        else
        {
            $closingMonthCriteria_2 = $closingMonthCriteria_2. ", ". " count(case when  LEFT(CONVERT(varchar, EPS_T_PO_DETAIL.CREATE_DATE, 112), 6) = '". $closingMonthVal. "' then  EPS_T_PO_DETAIL.CREATE_DATE end) as '". $closingMonthVal. "'"; 
        }
        $indexArray_2 ++;
    }
    
    $numrow = 2;
    $listNo = 1;
    $i      = 0;
    $query_select_t_transfer = "select     
                                    EPS_T_TRANSFER.CREATE_BY
                                    ,EPS_M_EMPLOYEE.NAMA1 AS CREATE_BY_NAME
                                    ,".$closingMonthCriteria."
                                 from         
                                    EPS_T_TRANSFER
                                 left join
                                    EPS_M_EMPLOYEE
                                 on 
                                    EPS_M_EMPLOYEE.NPK = EPS_T_TRANSFER.CREATE_BY
                                 group by
                                    EPS_T_TRANSFER.CREATE_BY
                                    ,EPS_M_EMPLOYEE.NAMA1
                                 order by 
                                    EPS_T_TRANSFER.CREATE_BY ";
    $sql_select_t_transfer = $conn->query($query_select_t_transfer);
    while($row_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC)){  
        $createByName     = $row_t_transfer['CREATE_BY_NAME'];
    
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$listNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$createByName);
        
        $columnID = 'C';
        $totalAmount = 0;
        foreach($addClosingMonthArray as $addClosingMonthArrays)
        {
            $columnName         = $addClosingMonthArrays['closingMonth'];
            $totalByBuyer       = $row_t_transfer[$columnName];
            if ($totalByBuyer == '')
            {
                $totalByBuyer = 0;
            }
            $selectMonth = substr($columnName,4);
            $monthName = date('M', mktime(0, 0, 0, $selectMonth, 10)); // March
            $selectYear = substr($columnName,0,4);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", "PR-".strtoupper($monthName)."-".$selectYear);
            $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow, $totalByBuyer);
            
            $columnID++;
            $columnID++;
        }
        
        $i++;
        $numrow++;
        $listNo++; 
    }
    
    $numrow = 2;
    $listNo = 1;
    $i      = 0;
    $query_select_t_po_detail = "select 
                                    EPS_T_PO_HEADER.ISSUED_BY
                                    ,".$closingMonthCriteria_2."
                                 from         
                                    EPS_T_PO_HEADER 
                                 left join
                                    EPS_T_PO_DETAIL 
                                 on 
                                    EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                 where     
                                    PO_STATUS IN ('1210', '1220', '1230', '1250', '1280', '1330')
                                 group by
                                    EPS_T_PO_HEADER.ISSUED_BY
                                 order by 
                                    EPS_T_PO_HEADER.ISSUED_BY ";
    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){  
        $createByName     = $row_select_t_po_detail['CREATE_BY_NAME'];
    
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
        $columnID = 'D';
        $totalAmount = 0;
        foreach($addClosingMonthArray as $addClosingMonthArrays_2)
        {
            $columnName         = $addClosingMonthArrays_2['closingMonth'];
            $totalByBuyer       = $row_select_t_po_detail[$columnName];
            if ($totalByBuyer == '')
            {
                $totalByBuyer = 0;
            }
            $selectMonth = substr($columnName,4);
            $monthName = date('M', mktime(0, 0, 0, $selectMonth, 10)); // March
            $selectYear = substr($columnName,0,4);
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($columnID."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $objPHPExcel->getActiveSheet()->setCellValue($columnID."1", "PO-".strtoupper($monthName)."-".$selectYear);
            $objPHPExcel->getActiveSheet()->setCellValue($columnID.$numrow, $totalByBuyer);
            
            $columnID++;
            $columnID++;
        }
        
        $i++;
        $numrow++;
        $listNo++; 
    }
    $setWorksheetName = "EPS_SUMMARY_BUYER_BY_ITEM"; 
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($setWorksheetName);
    
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$setWorksheetName.'.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$objPHPExcel->save('php://output');
//$objPHPExcel->save('pr-search.xlsx');
?>
