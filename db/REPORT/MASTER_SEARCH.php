<?php session_start(); 
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
set_time_limit(1800);
ini_set('memory_limit', '256M');

/** Include PHPExcel */
require_once '../LIB/PHPExcel/Classes/PHPExcel.php';
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";

$criteria       = $_GET['criteria'];

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set zoom level
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);

// Set document properties
$objPHPExcel->getProperties()->setCreator("IS Division")
			     ->setLastModifiedBy("Administrator")
			     ->setTitle("Download Master Search")
			     ->setSubject($criteria." List")
		   	     ->setDescription($criteria." List by criteria")
			     ->setKeywords("EPS")
		      	     ->setCategory(strtoupper($criteria));
if($criteria == "Item")
{
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 

    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setSize(12)->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("G1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "ITEM CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "ITEM NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "ITEM GROUP CODE"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "ACTIVE FLAG"); 
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('G1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    $whereItemMaster    = array();           
    $itemCdCriteria     = trim($_GET['itemCd']);
    $itemNameCriteria   = trim($_GET['itemName']);
    $itemGroupCdCriteria= trim(urldecode($_GET['itemGroupCd']));
    $activeFlagCriteria = trim($_GET['activeFlag']);
    
    if($itemCdCriteria){
        $whereItemMaster[] = "EPS_M_ITEM.ITEM_CD = '".$itemCdCriteria."'";
    }
    if($itemNameCriteria){
        $whereItemMaster[] = "EPS_M_ITEM.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
    }
    if($itemGroupCdCriteria){
        $whereItemMaster[] = "EPS_M_ITEM.ITEM_GROUP_CD = '".$itemGroupCdCriteria."'";
    }
    if($activeFlagCriteria)
    {
        $whereItemMaster[] = "EPS_M_ITEM.ACTIVE_FLAG = '".$activeFlagCriteria."'";
    }

    $query_select_m_item = "select
                                EPS_M_ITEM.ITEM_CD
                                ,EPS_M_ITEM.ITEM_NAME
                                ,EPS_M_ITEM.ITEM_GROUP_CD
                                ,EPS_M_ITEM.ACTIVE_FLAG
                                ,EPS_M_ITEM.UPDATE_BY
                                ,CONVERT(VARCHAR(24), UPDATE_DATE, 103) as UPDATE_DATE
                                ,CONVERT(VARCHAR(24), UPDATE_DATE, 108) as UPDATE_TIME
                                ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                            from
                                EPS_M_ITEM
                            left join
                                EPS_M_EMPLOYEE
                            on
                                EPS_M_ITEM.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
    if(count($whereItemMaster)) {
        $query_select_m_item .= "where " . implode(' and ', $whereItemMaster);
    }
    $query_select_m_item .= "order by
                                ITEM_CD ";
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_item = $conn->query($query_select_m_item);
    while($row_select_m_item = $sql_select_m_item->fetch(PDO::FETCH_ASSOC))
    {
        $itemCd     = $row_select_m_item['ITEM_CD'];
        $itemName   = $row_select_m_item['ITEM_NAME'];
        $itemGroupCd= $row_select_m_item['ITEM_GROUP_CD'];
        $activeFlag = $row_select_m_item['ACTIVE_FLAG'];
        $updateDate = $row_select_m_item['UPDATE_DATE'];
        $updateTime = $row_select_m_item['UPDATE_TIME'];
        $updateBy   = $row_select_m_item['UPDATE_BY_NAME'];
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$itemCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$itemName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$itemGroupCd);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$activeFlag);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}

if($criteria == 'ItemGroup')
{
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12)->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "ITEM GROUP CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "ITEM GROUP NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    $whereItemGroupMaster   = array();    
    $itemGroupCdCriteria    = trim($_GET['itemGroupCd']);     
    $itemGroupNameCriteria  = trim($_GET['itemGroupName']);
                 
    if($itemGroupCdCriteria)
    {
        $whereItemGroupMaster[] = "EPS_M_ITEM_GROUP.ITEM_GROUP_CD = '".$itemGroupCdCriteria."'";
    }
    if($itemGroupNameCriteria)
    {
        $whereItemGroupMaster[] = "EPS_M_ITEM_GROUP.ITEM_GROUP_NAME like '%".$itemGroupNameCriteria."%'";
    }

    $query_select_m_item_group = "select 
                                    EPS_M_ITEM_GROUP.ITEM_GROUP_CD
                                    ,EPS_M_ITEM_GROUP.ITEM_GROUP_NAME
                                    ,EPS_M_ITEM_GROUP.UPDATE_BY
                                    ,CONVERT(VARCHAR(24), UPDATE_DATE, 103) as UPDATE_DATE
                                    ,CONVERT(VARCHAR(24), UPDATE_DATE, 108) as UPDATE_TIME
                                    ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                from
                                    EPS_M_ITEM_GROUP
                                left join
                                    EPS_M_EMPLOYEE
                                on
                                    EPS_M_ITEM_GROUP.UPDATE_BY = EPS_M_EMPLOYEE.NPK  ";
    if(count($whereItemGroupMaster)) {
        $query_select_m_item_group .= "where " . implode(' and ', $whereItemGroupMaster);
    }
    $query_select_m_item_group .= "order by 
                                        ITEM_GROUP_CD";
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_item_group = $conn->query($query_select_m_item_group);
    while($row_select_m_item_group = $sql_select_m_item_group->fetch(PDO::FETCH_ASSOC))
    {
        $itemGroupCd     = $row_select_m_item_group['ITEM_GROUP_CD'];
        $itemGroupName   = $row_select_m_item_group['ITEM_GROUP_NAME'];
        $updateDate     = $row_select_m_item_group['UPDATE_DATE'];
        $updateTime     = $row_select_m_item_group['UPDATE_TIME'];
        $updateBy       = $row_select_m_item_group['UPDATE_BY_NAME'];
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$itemGroupCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$itemGroupName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}

if($criteria == "ItemPrice")
{
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

    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "ITEM CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "ITEM NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "ITEM GROUP CODE"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "U M");   
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "PRICE");  
    $objPHPExcel->getActiveSheet()->setCellValue('G1', "SUPPLIER CODE");  
    $objPHPExcel->getActiveSheet()->setCellValue('H1', "SUPPLIER NAME");  	
    $objPHPExcel->getActiveSheet()->setCellValue('I1', "CURRENCY");  
    $objPHPExcel->getActiveSheet()->setCellValue('J1', "EFFECTIVE DATE FROM"); 
    $objPHPExcel->getActiveSheet()->setCellValue('K1', "LEAD TIME"); 
    $objPHPExcel->getActiveSheet()->setCellValue('L1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('M1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    
    $itemCdCriteria             = trim($_GET['itemCd']);
    $itemNameCriteria           = trim($_GET['itemName']);
    $itemGroupCdCriteria        = trim(urldecode($_GET['itemGroupCd']));
    $effectiveDateFromCriteria  = trim($_GET['effectiveDateFrom']);
    $supplierCdCriteria         = trim($_GET['supplierCd']);
    
    $whereItemPriceMaster = array();  
    $whereItemPriceMaster[] = "EPS_M_ITEM.ACTIVE_FLAG = 'A'";
    if($itemCdCriteria){
        $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_CD = '".$itemCdCriteria."'";
    }
    if($itemNameCriteria){
        $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
    }
    if($itemGroupCdCriteria){
        $whereItemPriceMaster[] = "EPS_M_ITEM.ITEM_GROUP_CD = '".$itemGroupCdCriteria."'";
    }
    if($effectiveDateFromCriteria){
        $whereItemPriceMaster[] = "EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM = '".encodeDate($effectiveDateFromCriteria)."'";
    }
    if($supplierCdCriteria){
        $whereItemPriceMaster[] = "EPS_M_ITEM_PRICE.SUPPLIER_CD = '".$supplierCdCriteria."'";
    }
    
    $query_select_m_item_price = "select
                                    EPS_M_ITEM_PRICE.ITEM_CD
                                    ,EPS_M_ITEM.ITEM_NAME
                                    ,EPS_M_ITEM.ITEM_GROUP_CD
                                    ,EPS_M_ITEM_PRICE.UNIT_CD
                                    ,EPS_M_ITEM_PRICE.ITEM_PRICE
                                    ,EPS_M_ITEM_PRICE.CURRENCY_CD
                                    ,EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM as EFFECTIVE_DATE
                                    ,substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,7,2)+'/'+substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,5,2)+'/'+substring(EPS_M_ITEM_PRICE.EFFECTIVE_DATE_FROM,1,4) as EFFECTIVE_DATE_FROM
                                    ,EPS_M_ITEM_PRICE.LEAD_TIME
                                    ,EPS_M_ITEM_PRICE.SUPPLIER_CD
                                    ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                    ,convert(VARCHAR(24),EPS_M_ITEM_PRICE.CREATE_DATE, 120) as CREATE_DATE
                                    ,EPS_M_ITEM_PRICE.CREATE_BY
                                    ,convert(VARCHAR(24), EPS_M_ITEM_PRICE.UPDATE_DATE, 120) as UPDATE_DATE
                                    ,EPS_M_ITEM_PRICE.UPDATE_BY
                                    ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                  from 
                                    EPS_M_ITEM_PRICE
                                  inner join
                                    EPS_M_ITEM
                                  on
                                    EPS_M_ITEM_PRICE.ITEM_CD = EPS_M_ITEM.ITEM_CD
                                  left join
                                    EPS_M_SUPPLIER
                                  on
                                    EPS_M_ITEM_PRICE.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
                                  left join
                                    EPS_M_EMPLOYEE
                                  on
                                    EPS_M_ITEM_PRICE.UPDATE_BY = EPS_M_EMPLOYEE.NPK  ";
    if(count($whereItemPriceMaster)) {
        $query_select_m_item_price .= "where " . implode(' and ', $whereItemPriceMaster);
    }
    $query_select_m_item_price .= "order by
                                    EPS_M_ITEM_PRICE.ITEM_CD
                                    ,EFFECTIVE_DATE ";
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_item_price = $conn->query($query_select_m_item_price);
    while($row_select_m_item_price = $sql_select_m_item_price->fetch(PDO::FETCH_ASSOC))
    {
        $itemCd         = $row_select_m_item_price['ITEM_CD'];
        $itemName       = $row_select_m_item_price['ITEM_NAME'];
        $itemGroupCd    = $row_select_m_item_price['ITEM_GROUP_CD'];
        $unitCd         = $row_select_m_item_price['UNIT_CD'];
        $itemPrice      = $row_select_m_item_price['ITEM_PRICE'];
        $currencyCd     = $row_select_m_item_price['CURRENCY_CD'];
        $effectiveDateFrom= $row_select_m_item_price['EFFECTIVE_DATE_FROM'];
        $leadTime       = $row_select_m_item_price['LEAD_TIME'];
        $supplierCd     = $row_select_m_item_price['SUPPLIER_CD'];
        $supplierName   = $row_select_m_item_price['SUPPLIER_NAME'];
        $updateDate     = $row_select_m_item_price['UPDATE_DATE'];
        $updateTime     = $row_select_m_item_price['UPDATE_TIME'];
        $updateBy       = $row_select_m_item_price['UPDATE_BY_NAME'];
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$itemCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$itemName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$itemGroupCd);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$unitCd);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$itemPrice);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$supplierCd);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$supplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$currencyCd);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$effectiveDateFrom);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$leadTime);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}

if($criteria == "Supplier")
{
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

    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "SUPPLIER CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "SUPPLIER NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "CURRENCY CODE"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "VAT");   
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "NPWP");  
    $objPHPExcel->getActiveSheet()->setCellValue('G1', "CONTACT");  	
    $objPHPExcel->getActiveSheet()->setCellValue('H1', "PHONE");  
    $objPHPExcel->getActiveSheet()->setCellValue('I1', "ADDRESS"); 
    $objPHPExcel->getActiveSheet()->setCellValue('J1', "EMAIL"); 
    $objPHPExcel->getActiveSheet()->setCellValue('K1', "OUTSTANDING FLAG"); 
    $objPHPExcel->getActiveSheet()->setCellValue('L1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('M1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    $whereSupplierMaster    = array();           
    $supplierCdCriteria     = trim($_GET['supplierCd']);
    $supplierNameCriteria   = trim($_GET['supplierName']);
    $currencyCdCriteria     = trim($_GET['currencyCd']);
    $vatCriteria            = trim($_GET['vat']);
    $outCriteria            = trim($_GET['out']);
   
    if($supplierCdCriteria){
        $whereSupplierMaster[] = "EPS_M_SUPPLIER.SUPPLIER_CD = '".$supplierCdCriteria."'";
    }
    if($supplierNameCriteria){
        $whereSupplierMaster[] = "EPS_M_SUPPLIER.SUPPLIER_NAME like '%".$supplierNameCriteria."%'";
    }
    if($currencyCdCriteria){
        $whereSupplierMaster[] = "EPS_M_SUPPLIER.CURRENCY_CD = '".$currencyCdCriteria."'";
    }            
    if($vatCriteria){
        $whereSupplierMaster[] = "EPS_M_SUPPLIER.VAT = '".$vatCriteria."'";
    }           
    if($outCriteria)
    {
        $whereSupplierMaster[] = "EPS_M_SUPPLIER.OUTSTANDING_FLAG = '".$outCriteria."'";
    }

    $query_select_m_supplier = "select
                                    EPS_M_SUPPLIER.SUPPLIER_CD
                                    ,EPS_M_SUPPLIER.SUPPLIER_NAME
                                    ,EPS_M_SUPPLIER.CURRENCY_CD
                                    ,EPS_M_SUPPLIER.VAT
                                    ,EPS_M_SUPPLIER.NPWP
                                    ,EPS_M_SUPPLIER.CONTACT
                                    ,EPS_M_SUPPLIER.EMAIL
                                    ,EPS_M_SUPPLIER.PHONE
                                    ,EPS_M_SUPPLIER.ADDRESS
                                    ,EPS_M_SUPPLIER.OUTSTANDING_FLAG
                                    ,EPS_M_SUPPLIER.UPDATE_BY
                                    ,CONVERT(VARCHAR(24), UPDATE_DATE, 103) as UPDATE_DATE
                                    ,CONVERT(VARCHAR(24), UPDATE_DATE, 108) as UPDATE_TIME
                                    ,EPS_M_EMPLOYEE.NAMA1 as UPDATE_BY_NAME
                                from
                                    EPS_M_SUPPLIER
                                left join
                                    EPS_M_EMPLOYEE
                                on
                                    EPS_M_SUPPLIER.UPDATE_BY = EPS_M_EMPLOYEE.NPK ";
    if(count($whereSupplierMaster)) {
        $query_select_m_supplier .= "where " . implode(' and ', $whereSupplierMaster);
    }
    $query_select_m_supplier .= " order by
                                    SUPPLIER_CD ";
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_supplier = $conn->query($query_select_m_supplier);
    while($row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC))
    {
        $supplierCd     = $row_select_m_supplier['SUPPLIER_CD'];
        $supplierName   = $row_select_m_supplier['SUPPLIER_NAME'];
        $currencyCd     = $row_select_m_supplier['CURRENCY_CD'];
        $vat            = $row_select_m_supplier['VAT'];
        $npwp           = $row_select_m_supplier['NPWP'];
        $contact        = $row_select_m_supplier['CONTACT'];
        $email          = $row_select_m_supplier['EMAIL'];
        $phone          = $row_select_m_supplier['PHONE'];
        $address        = $row_select_m_supplier['ADDRESS'];
        $outstandingFlag= $row_select_m_supplier['OUTSTANDING_FLAG'];
        $updateDate     = $row_select_m_supplier['UPDATE_DATE'];
        $updateTime     = $row_select_m_supplier['UPDATE_TIME'];
        $updateBy       = $row_select_m_supplier['UPDATE_BY_NAME'];
                                        
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$supplierCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$supplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$currencyCd);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$vat);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$npwp);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$contact);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$phone);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$address);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$email);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$outstandingFlag);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}
if($criteria == "PrProcInCharge")
{
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
  
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
   
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(12)->setBold(true);   
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setSize(12)->setBold(true);
   
    $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
    $objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("G1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("H1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
   
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "PLANT");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "BU CODE");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "BU NAME"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "NPK");   
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('G1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('H1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    $whereProcAppMaster     = array();           
    $plantAliasCriteria     = trim($_GET['plantAlias']);
    $buCdCriteria          = trim($_GET['buCd']);
    $buNameCriteria         = trim($_GET['buName']);
    $npkInChargeCriteria    = trim($_GET['npkInCharge']);
   
    if($plantAliasCriteria)
    {
        $whereProcAppMaster[] = "EPS_M_PR_PROC_APPROVER.PLANT_ALIAS = '".$plantAliasCriteria."'";
    }
    if($buCdCriteria)
    {
        $whereProcAppMaster[] = "EPS_M_PR_PROC_APPROVER.BU_CD = '".$buCdCriteria."'";
    }
    if($buNameCriteria)
    {
        $whereProcAppMaster[] = "EPS_M_TBUNIT.NMBU1 like '%".$buNameCriteria."%'";
    }
    if($npkInChargeCriteria)
    {
        $whereProcAppMaster[] = "ltrim(EPS_M_PR_PROC_APPROVER.NPK) = '".trim($npkInChargeCriteria)."'";
    }

    $query_select_m_proc_app = "select
                                    EPS_M_PR_PROC_APPROVER.PLANT_CD
                                    ,EPS_M_PLANT.PLANT_NAME
                                    ,EPS_M_PR_PROC_APPROVER.BU_CD
                                    ,EPS_M_TBUNIT.NMBU1 as BU_NAME
                                    ,EPS_M_PR_PROC_APPROVER.NPK
                                    ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                    ,EPS_M_PR_PROC_APPROVER.PROC_APP_ID
                                    ,CONVERT(VARCHAR(24), EPS_M_PR_PROC_APPROVER.UPDATE_DATE, 103) as UPDATE_DATE
                                    ,CONVERT(VARCHAR(24), EPS_M_PR_PROC_APPROVER.UPDATE_DATE, 108) as UPDATE_TIME
                                from
                                    EPS_M_PR_PROC_APPROVER
                                left join
                                    EPS_M_EMPLOYEE
                                on
                                    EPS_M_PR_PROC_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_M_PLANT
                                on
                                    EPS_M_PR_PROC_APPROVER.PLANT_CD = EPS_M_PLANT.PLANT_CD
                                left join
                                    EPS_M_TBUNIT
                                on
                                    EPS_M_PR_PROC_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU ";
    if(count($whereProcAppMaster)) {
        $query_select_m_proc_app .= "where " . implode(' and ', $whereProcAppMaster);
    }
    $query_select_m_proc_app .= "order by
                                    PROC_APP_ID ";
    
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_proc_app = $conn->query($query_select_m_proc_app);
    while($row_select_m_proc_app = $sql_select_m_proc_app->fetch(PDO::FETCH_ASSOC))
    {
        $plantCd        = $row_select_m_proc_app['PLANT_CD'];
        $plantName      = $row_select_m_proc_app['PLANT_NAME'];
        $buCd           = $row_select_m_proc_app['BU_CD'];
        $buName         = $row_select_m_proc_app['BU_NAME'];
        $npk            = $row_select_m_proc_app['NPK'];
        $approverName   = $row_select_m_proc_app['APPROVER_NAME'];
        $updateDate     = $row_select_m_proc_app['UPDATE_DATE'];
        $updateTime     = $row_select_m_proc_app['UPDATE_TIME'];
        $updateBy       = $row_select_m_proc_app['UPDATE_BY_NAME'];
                                        
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$plantName);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$buCd);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$buName);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$npk);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$approverName);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}
if($criteria == "PrApprover")
{
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
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "BU CODE");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C1', "BU NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "APPROVER NO"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E1', "NPK");   
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "NAME");   
    $objPHPExcel->getActiveSheet()->setCellValue('G1', "LIMIT AMOUNT");  	
    $objPHPExcel->getActiveSheet()->setCellValue('H1', "CURRENCY");  
    $objPHPExcel->getActiveSheet()->setCellValue('I1', "UPDATE DATE");   
    $objPHPExcel->getActiveSheet()->setCellValue('J1', "UPDATE BY");  

    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A2');
    $wherePrApproverMaster     = array();     
    $buCdCriteria        = trim($_GET['buCd']); 
    $buNameCriteria      = trim($_GET['buName']);
    $approverNoCriteria  = trim($_GET['approverNo']);
    $npkCriteria         = trim($_GET['npk']);  
    $approverNameCriteria= trim($_GET['approverName']);  
    $currencyCdCriteria  = trim($_GET['currencyCd']);  
   
    if($buCdCriteria)
    {
        $wherePrApproverMaster[] = "EPS_M_PR_APPROVER.BU_CD = '".$buCdCriteria."'";
    }
    if($buNameCriteria)
    {
        $wherePrApproverMaster[] = "EPS_M_TBUNIT.NMBU1 like '%".$buNameCriteria."%'";
    }              
    if($approverNoCriteria)
    {
        $wherePrApproverMaster[] = "EPS_M_PR_APPROVER.APPROVER_NO = '".$approverNoCriteria."'";
    }
    if($npkCriteria)
    {
        $wherePrApproverMaster[] = "ltrim(EPS_M_PR_APPROVER.NPK) = '".trim($npkCriteria)."'";
    }
    if($approverNameCriteria)
    {
        $wherePrApproverMaster[] = "EPS_M_EMPLOYEE.NAMA1 like '%".$approverNameCriteria."%'";
    } 
    if($currencyCdCriteria)
    {
        $wherePrApproverMaster[] = "EPS_M_LIMIT.CURRENCY_CD = '".$currencyCdCriteria."'";
    }

    $query_select_m_pr_approver = "select
                                        EPS_M_PR_APPROVER.BU_CD
                                        ,EPS_M_TBUNIT.NMBU1 as BU_NAME
                                        ,EPS_M_PR_APPROVER.APPROVER_NO
                                        ,EPS_M_PR_APPROVER.NPK
                                        ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                        ,EPS_M_PR_APPROVER.APPROVER_LEVEL
                                        ,EPS_M_LIMIT.LIMIT_AMOUNT
                                        ,EPS_M_LIMIT.CURRENCY_CD
                                        ,CONVERT(VARCHAR(24), EPS_M_PR_APPROVER.UPDATE_DATE, 103) as UPDATE_DATE
                                        ,CONVERT(VARCHAR(24), EPS_M_PR_APPROVER.UPDATE_DATE, 108) as UPDATE_TIME
                                   from
                                        EPS_M_PR_APPROVER
                                   left join
                                        EPS_M_EMPLOYEE
                                   on
                                        EPS_M_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                   left join
                                        EPS_M_TBUNIT
                                   on
                                        EPS_M_PR_APPROVER.BU_CD = EPS_M_TBUNIT.KDBU 
                                   left join
                                        EPS_M_LIMIT 
                                   on 
                                        EPS_M_PR_APPROVER.APPROVER_LEVEL = EPS_M_LIMIT.LEVEL_ID ";
    if(count($wherePrApproverMaster)) {
        $query_select_m_pr_approver .= "where " . implode(' and ', $wherePrApproverMaster);
    }
    $query_select_m_pr_approver .= "order by
                                        EPS_M_PR_APPROVER.BU_CD asc
                                        ,EPS_M_LIMIT.CURRENCY_CD asc
                                        ,EPS_M_PR_APPROVER.APPROVER_NO ";
    
    $numrow = 2;
    $itemNo = 1;
    $sql_select_m_pr_approver= $conn->query($query_select_m_pr_approver);
    while($row_select_m_pr_approver = $sql_select_m_pr_approver->fetch(PDO::FETCH_ASSOC))
    {
        $buCd           = $row_select_m_pr_approver['BU_CD'];
        $buName         = $row_select_m_pr_approver['BU_NAME'];
        $approverNo     = $row_select_m_pr_approver['APPROVER_NO'];
        $npk            = $row_select_m_pr_approver['NPK'];
        $approverName   = $row_select_m_pr_approver['APPROVER_NAME'];
        $approverLevel  = $row_select_m_pr_approver['APPROVER_LEVEL'];
        $limitAmount    = $row_select_m_pr_approver['LIMIT_AMOUNT'];
        $currencyCd     = $row_select_m_pr_approver['CURRENCY_CD'];
        $updateDate     = $row_select_m_pr_approver['UPDATE_DATE'];
        $updateTime     = $row_select_m_pr_approver['UPDATE_TIME'];
        $updateBy       = $row_select_m_pr_approver['UPDATE_BY_NAME'];
                                        
        if(trim($updateBy) == '')
        {
            $updateBy = "Administrator";
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$buCd);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$buName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$approverNo);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$npk);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$approverName);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$limitAmount);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$currencyCd);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$updateDate." ".$updateTime);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,substr($updateBy, 0, strpos($updateBy, ' ')));
        
        $numrow++;
        $itemNo++; 
    }
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
}

if($criteria == "UserID")
{
    $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
    $objPHPExcel->getActiveSheet()->mergeCells('B1:E1');
    $objPHPExcel->getActiveSheet()->mergeCells('F1:G1');
    $objPHPExcel->getActiveSheet()->mergeCells('H1:I1');
    $objPHPExcel->getActiveSheet()->mergeCells('J1:J2');
    
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
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
    
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setSize(12)->setBold(true);
    
    $objPHPExcel->getActiveSheet()->getStyle("A1:J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO"); 
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "EMPLOYEE"); 
    $objPHPExcel->getActiveSheet()->setCellValue('B2', "NPK");  	
    $objPHPExcel->getActiveSheet()->setCellValue('C2', "NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('D2', "BU CODE"); 
    $objPHPExcel->getActiveSheet()->setCellValue('E2', "COMPANY");   
    $objPHPExcel->getActiveSheet()->setCellValue('F1', "USER"); 
    $objPHPExcel->getActiveSheet()->setCellValue('F2', "USERID");   
    $objPHPExcel->getActiveSheet()->setCellValue('G2', "BU USER");  	
    $objPHPExcel->getActiveSheet()->setCellValue('H1', "MAIL");  	
    $objPHPExcel->getActiveSheet()->setCellValue('H2', "MAIL NAME");  
    $objPHPExcel->getActiveSheet()->setCellValue('I2', "EMAIL");    
    $objPHPExcel->getActiveSheet()->setCellValue('J1', "LAST UPDATE");    
    
    // FREEZEPANE            
    $objPHPExcel->getActiveSheet()->freezePane('A3');
    
    $npkCriteria        = stripslashes(strtoupper(trim($_GET['npk'])));
    $npkCriteria        = str_replace("'", "''", $npkCriteria);
    $empNameCriteria    = stripslashes(strtoupper(trim($_GET['empName'])));
    $empNameCriteria    = str_replace("'", "''", $empNameCriteria);
    $buCdCriteria       = stripslashes(strtoupper(trim($_GET['buCd'])));
    $buCdCriteria       = str_replace("'", "''", $buCdCriteria);
    $userIdCriteria     = stripslashes(strtoupper(trim($_GET['userId'])));
    $userIdCriteria     = str_replace("'", "''", $userIdCriteria);
    $buUserCriteria     = stripslashes(strtoupper(trim($_GET['buUser'])));
    $buUserCriteria     = str_replace("'", "''", $buUserCriteria);
    $mailNameCriteria   = stripslashes(strtoupper(trim($_GET['mailName'])));
    $mailNameCriteria   = str_replace("'", "''", $mailNameCriteria);
    $emailCriteria      = stripslashes(strtoupper(trim($_GET['email'])));
    $emailCriteria      = str_replace("'", "''", $emailCriteria);
            
    $whereUserMaster = array();   
    $whereUserMaster[] = "EPS_M_EMPLOYEE.AKTIF = 'A'";
    $whereUserMaster[] = "EPS_M_USER.ACTIVE_FLAG = 'A'";
            
    if($npkCriteria)
    {
        $whereUserMaster[] = "ltrim(EPS_M_USER.NPK) = ltrim('".$npkCriteria."')";
    }
    if($empNameCriteria)
    {
        $whereUserMaster[] = "EPS_M_EMPLOYEE.NAMA1 like '%".$empNameCriteria."%'";
    }
    if($buCdCriteria)
    {
        $whereUserMaster[] = "EPS_M_EMPLOYEE.LKDP = '".$buCdCriteria."'";
    }
    if($userIdCriteria)
    {
        $whereUserMaster[] = "EPS_M_USER.USERID = '".$userIdCriteria."'";
    }
    if($buUserCriteria)
    {
        $whereUserMaster[] = "EPS_M_USER.BU_CD = '".$buUserCriteria."'";
    }
    if($mailNameCriteria)
    {
        $whereUserMaster[] = "EPS_M_DSCID.INMAIL like '%".$mailNameCriteria."%'";
    }
    if($emailCriteria)
    {
        $whereUserMaster[] = "EPS_M_DSCID.INETML like '%".$emailCriteria."%'";
    }
    
    $query_select_m_dscid = "select 
                                EPS_M_EMPLOYEE.NPK
                                ,EPS_M_EMPLOYEE.NAMA1
                                ,EPS_M_EMPLOYEE.LKDP
                                ,EPS_M_USER.USERID
                                ,EPS_M_USER.BU_CD
                                ,EPS_M_DSCID.INMAIL
                                ,EPS_M_DSCID.INETML
                                ,EPS_M_COMPANY.COMPANY_NAME_ALIAS
                                ,CONVERT(VARCHAR(24), EPS_M_USER.LAST_UPDATE, 120) as LAST_UPDATE
                             from
                                EPS_M_EMPLOYEE
                             inner join
                                EPS_M_DSCID
                             on
                                EPS_M_DSCID.INOPOK = EPS_M_EMPLOYEE.NPK
                             inner join
                                EPS_M_USER
                             on
                                EPS_M_EMPLOYEE.NPK = EPS_M_USER.NPK
                             inner join
                                EPS_M_COMPANY
                             on
                                EPS_M_EMPLOYEE.PERSH = EPS_M_COMPANY.COMPANY_CD ";
    if(count($whereUserMaster)) {
        $query_select_m_dscid .= "where " . implode(' and ', $whereUserMaster);
    }
    $query_select_m_dscid .= "order by 
                                EPS_M_USER.USERID asc ";
    
    $numrow = 3;
    $itemNo = 1;
    $sql_select_m_dscid= $conn->query($query_select_m_dscid);
    while($row_select_m_dscid = $sql_select_m_dscid->fetch(PDO::FETCH_ASSOC))
    {
        $npk                = $row_select_m_dscid['NPK'];
        $nama               = $row_select_m_dscid['NAMA1'];
        $lkdp               = $row_select_m_dscid['LKDP'];
        $companyNameAlias   = $row_select_m_dscid['COMPANY_NAME_ALIAS'];
        $userId             = $row_select_m_dscid['USERID'];
        $buCd               = $row_select_m_dscid['BU_CD'];
        $inmail             = $row_select_m_dscid['INMAIL'];
        $inetml             = $row_select_m_dscid['INETML'];
        $lastUpdate         = $row_select_m_dscid['LAST_UPDATE'];
                                        
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$npk);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$nama);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$lkdp);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$companyNameAlias);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$userId);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$buCd);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$inmail);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$inetml);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$lastUpdate);
        
        $numrow++;
        $itemNo++; 
                                                
    }
    
    $setWorksheetName = "EPS_".strtoupper($criteria)."_MASTER";
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
