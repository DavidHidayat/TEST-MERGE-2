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
			     ->setTitle("Download PR Search")
			     ->setSubject("PR List")
		   	     ->setDescription("PR List by criteria")
			     ->setKeywords("EPS")
		      	 ->setCategory("PR");

$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
$objPHPExcel->getActiveSheet()->mergeCells('B1:M1');
$objPHPExcel->getActiveSheet()->mergeCells('N1:N2');
$objPHPExcel->getActiveSheet()->mergeCells('O1:AB1');
 
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
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('N1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('O1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('C2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('D2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('E2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('F2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('G2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('H2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('I2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('J2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('K2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('L2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);        
$objPHPExcel->getActiveSheet()->getStyle('M2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('O2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('P2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('Q2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('R2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('S2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('T2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('U2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('V2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('W2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('X2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('Y2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('Z2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('AA2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);     
$objPHPExcel->getActiveSheet()->getStyle('AB2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AC2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AD2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AE2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AC1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AD1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AE1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AF1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AG1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AH1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AI1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
$objPHPExcel->getActiveSheet()->getStyle('AJ1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);    
               
$objPHPExcel->getActiveSheet()->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("B1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("N1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("O1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("D2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("F2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("G2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("H2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("I2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
$objPHPExcel->getActiveSheet()->getStyle("J2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("K2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("L2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("M2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("O2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("P2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("Q2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("R2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("S2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("T2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("U2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("V2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000"); 
$objPHPExcel->getActiveSheet()->getStyle("W2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("X2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("Y2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("Z2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AA2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AB2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AC1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AD1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AE1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AC2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AD2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AE2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AF2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AG2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AH2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AF1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AG1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AH1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AI1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AI2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AJ1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");
$objPHPExcel->getActiveSheet()->getStyle("AJ2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("FFC000");

$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("N1")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("O1")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("D2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("E2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("F2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("G2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("H2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("I2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("J2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("K2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("L2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("M2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("O2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("P2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("Q2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("R2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("S2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("T2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("U2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("V2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("W2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("X2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("Y2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("Z2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AA2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AB2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AC2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AD2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AE2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AF2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AG2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AH2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AI2")->getFont()->setSize(12)->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("AJ2")->getFont()->setSize(12)->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("N1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("O1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("I2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("J2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("K2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("L2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("N2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("O2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("P2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("Q2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("R2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("S2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("T2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("U2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("V2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("W2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("X2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("Y2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("Z2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AA2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AB2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AC2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AD2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AE2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AF2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AG2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AH2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AI2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("AJ2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
$objPHPExcel->getActiveSheet()->setCellValue('B1', "PURCHASE REQIUISITION (PR)");  	
$objPHPExcel->getActiveSheet()->setCellValue('N1', "ITEM STATUS");  
$objPHPExcel->getActiveSheet()->setCellValue('O1', "PURCHASE ORDER (PO)"); 
$objPHPExcel->getActiveSheet()->setCellValue('B2', "PR NO");  
$objPHPExcel->getActiveSheet()->setCellValue('C2', "PR STATUS");  
$objPHPExcel->getActiveSheet()->setCellValue('D2', "REQUESTER");  
$objPHPExcel->getActiveSheet()->setCellValue('E2', "CHARGED BU");  
$objPHPExcel->getActiveSheet()->setCellValue('F2', "ITEM CODE");  
$objPHPExcel->getActiveSheet()->setCellValue('G2', "ITEM NAME"); 
$objPHPExcel->getActiveSheet()->setCellValue('H2', "DUE DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('I2', "PRICE");  
$objPHPExcel->getActiveSheet()->setCellValue('J2', "QTY"); 
$objPHPExcel->getActiveSheet()->setCellValue('K2', "UM"); 
$objPHPExcel->getActiveSheet()->setCellValue('L2', "AMOUNT");  
$objPHPExcel->getActiveSheet()->setCellValue('M2', "SUPPLIER");  
$objPHPExcel->getActiveSheet()->setCellValue('O2', "PO NO");  
$objPHPExcel->getActiveSheet()->setCellValue('P2', "ITEM CODE");  
$objPHPExcel->getActiveSheet()->setCellValue('Q2', "ITEM NAME");  
$objPHPExcel->getActiveSheet()->setCellValue('R2', "DUE DATE");  
$objPHPExcel->getActiveSheet()->setCellValue('S2', "ITEM TYPE"); 
$objPHPExcel->getActiveSheet()->setCellValue('T2', "EXP/RFI/INV");  
$objPHPExcel->getActiveSheet()->setCellValue('U2', "ORDER QTY");  
$objPHPExcel->getActiveSheet()->setCellValue('V2', "RECEIVED QTY"); 
$objPHPExcel->getActiveSheet()->setCellValue('W2', "UM");  
$objPHPExcel->getActiveSheet()->setCellValue('X2', "CUR");  
$objPHPExcel->getActiveSheet()->setCellValue('Y2', "PRICE"); 
$objPHPExcel->getActiveSheet()->setCellValue('Z2', "AMOUNT");  
$objPHPExcel->getActiveSheet()->setCellValue('AA2', "SUPPLIER");  
$objPHPExcel->getActiveSheet()->setCellValue('AB2', "PO DATE CLOSED"); 
$objPHPExcel->getActiveSheet()->setCellValue('AC2', "ITEM TYPE"); 
$objPHPExcel->getActiveSheet()->setCellValue('AD2', "RFI NUMBER"); 
$objPHPExcel->getActiveSheet()->setCellValue('AE2', "ACCOUNT NO"); 
$objPHPExcel->getActiveSheet()->setCellValue('AF2', "PURPOSE"); 
$objPHPExcel->getActiveSheet()->setCellValue('AG2', "REMARK 1"); 
$objPHPExcel->getActiveSheet()->setCellValue('AH2', "REMARK 2"); 
$objPHPExcel->getActiveSheet()->setCellValue('AI2', "PO STATUS"); 
$objPHPExcel->getActiveSheet()->setCellValue('AJ2', "ISSUED DATE"); 

// FREEZEPANE            
$objPHPExcel->getActiveSheet()->freezePane('A3');

$prNoCriteria           = trim($_GET['prNo']);
$prDateCriteria         = trim($_GET['prDate']);
$prDateEndCriteria      = trim($_GET['prDateEnd']);
$requesterNameCriteria  = trim($_GET['requester']);
$deliveryDateCriteria   = trim($_GET['deliveryDate']);  
$prChargedCriteria      = trim($_GET['prCharged']); 
$supplierCdCriteria     = trim($_GET['supplierCd']); 
$supplierNameCriteria   = trim($_GET['supplierName']); 
$itemNameCriteria       = trim($_GET['itemName']); 
$itemStatusCriteria     = trim($_GET['itemStatus']); 
$itemTypeCriteria       = trim($_GET['itemType']); 
$expNoCriteria          = trim($_GET['expNo']); 
$invNoCriteria          = trim($_GET['invNo']);
$rfiNoCriteria          = trim($_GET['rfiNo']); 
$prStatusCriteria       = trim($_GET['prStatus']); 
$roStatusCriteria       = trim($_GET['roSts']); 
$itemCodeCriteria       = trim($_GET['itemCode']); 

$wherePrSearch  = array();

if($prNoCriteria || $prDateCriteria || $prDateEndCriteria
    || $deliveryDateCriteria || $supplierNameCriteria
    || $approverNameCriteria || $prStatusCriteria
    || $requesterNameCriteria || $prChargedCriteria
    || $itemTypeCriteria || $expNoCriteria || $rfiNoCriteria || $invNoCriteria
    || $poNoCriteria || $itemNameCriteria || $itemStatusCriteria || $roStatusCriteria
    || $supplierCdCriteria || $itemCodeCriteria)
{
	if($prNoCriteria){
        $wherePrSearch[] = "EPS_T_PR_HEADER.PR_NO LIKE '".$prNoCriteria."%'";
    }
    if($prDateCriteria && !$prDateEndCriteria){
        $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateCriteria)."'";
    }
    if(!$prDateCriteria && $prDateEndCriteria ){
        $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE = '".encodeDate($prDateEndCriteria)."'";
    }
    if($prDateCriteria && $prDateEndCriteria){
        $wherePrSearch[] = "EPS_T_PR_HEADER.ISSUED_DATE >= '".encodeDate($prDateCriteria)."'
                            and EPS_T_PR_HEADER.ISSUED_DATE <= '".encodeDate($prDateEndCriteria)."'";
    }
    if($deliveryDateCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.DELIVERY_DATE = '".encodeDate($deliveryDateCriteria)."'";
    }
    if($supplierNameCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.SUPPLIER_NAME LIKE '".$supplierNameCriteria."%'";
    }
    if($approverNameCriteria){
        $wherePrSearch[] = "EPS_M_EMPLOYEE_3.NAMA1 LIKE '".$approverNameCriteria."%'";
    }
    if($prStatusCriteria){
        $wherePrSearch[] = "EPS_T_PR_HEADER.PR_STATUS = '".$prStatusCriteria."'";
    }
    if($requesterNameCriteria){
        $wherePrSearch[] = "EPS_M_EMPLOYEE_2.NAMA1 LIKE '".$requesterNameCriteria."%'";
    }
    if($prChargedCriteria){
        $wherePrSearch[] = "EPS_T_PR_HEADER.CHARGED_BU_CD = '".$prChargedCriteria."'";
    }
    if($itemTypeCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_TYPE_CD = '".$itemTypeCriteria."'";
    }
    if($expNoCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ACCOUNT_NO = '".$expNoCriteria."'";
    }
    if($rfiNoCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.RFI_NO = '".$rfiNoCriteria."'";
    }
    if($invNoCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ACCOUNT_NO = '".$invNoCriteria."'";
    }
    if($poNoCriteria){
        $wherePrSearch[] = "EPS_T_PO_DETAIL.PO_NO = '".$poNoCriteria."'";
    }
    if($itemNameCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_NAME LIKE '%".$itemNameCriteria."%'";
    }
    if($itemStatusCriteria){
        $wherePrSearch[] = "EPS_T_TRANSFER.ITEM_STATUS = '".$itemStatusCriteria."'";
    }
    if($roStatusCriteria){
        $wherePrSearch[] = "EPS_T_PO_DETAIL.RO_STATUS = '".$roStatusCriteria."'";
    }
    if($supplierCdCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.SUPPLIER_CD = '".$supplierCdCriteria."'";
    }
    if($itemCodeCriteria){
        $wherePrSearch[] = "EPS_T_PR_DETAIL.ITEM_CD = '".$itemCodeCriteria."'";
    }

    $query_select_pr = "select     
                            EPS_T_PR_DETAIL.PR_NO
                            ,substring(EPS_T_PR_HEADER.ISSUED_DATE, 5, 2) + '/' + substring(EPS_T_PR_HEADER.ISSUED_DATE, 7, 2) + '/' + substring(EPS_T_PR_HEADER.ISSUED_DATE, 1, 4) as ISSUED_DATE 
                            ,EPS_M_EMPLOYEE_2.NAMA1 as REQUESTER_NAME
                            ,EPS_T_PR_DETAIL.ITEM_CD
                            ,EPS_T_PR_DETAIL.ITEM_NAME
                            ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
                            ,EPS_T_PR_DETAIL.QTY
                            ,EPS_T_PR_DETAIL.ITEM_PRICE
                            ,EPS_T_PR_DETAIL.AMOUNT
                            ,EPS_T_PR_DETAIL.CURRENCY_CD
                            ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
                            ,EPS_T_PR_DETAIL.ACCOUNT_NO
                            ,EPS_T_PR_DETAIL.RFI_NO
                            ,EPS_T_PR_DETAIL.UNIT_CD as PR_UNIT_CD
                            ,EPS_T_PR_DETAIL.SUPPLIER_CD
                            ,EPS_T_PR_DETAIL.SUPPLIER_NAME
                            ,EPS_T_PR_DETAIL.REMARK
			    ,EPS_T_PR_DETAIL.REMARK_2
                            ,EPS_T_PR_HEADER.PR_STATUS as PR_STATUS
                            ,EPS_M_APP_STATUS.APP_STATUS_NAME as PR_STATUS_NAME
                            ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                            ,EPS_T_TRANSFER.ITEM_STATUS
                            ,EPS_M_APP_STATUS_1.APP_STATUS_NAME as ITEM_STATUS_NAME
                            ,EPS_T_PR_HEADER.REQ_BU_CD 
                            ,EPS_T_PR_HEADER.CHARGED_BU_CD
                            ,EPS_M_ITEM_TYPE.ITEM_TYPE_ALIAS
                            ,case 
                                when 
                                    CHARINDEX('.', EPS_T_PR_DETAIL.ITEM_NAME) - 1 > 0 
                                then 
                                case 
                                when 
                                    ISNUMERIC(SUBSTRING(EPS_T_PR_DETAIL.ITEM_NAME, 1, CHARINDEX('.',EPS_T_PR_DETAIL.ITEM_NAME) - 1)) = 1 
                                then 
                                    SUBSTRING(EPS_T_PR_DETAIL.ITEM_NAME, 1, CHARINDEX('.', EPS_T_PR_DETAIL.ITEM_NAME) - 1) 
                                else 
                                    999 
                                end 
                            else 
                                999 
                            end 
                            as INDEX_ITEM_NAME
                            ,EPS_T_TRANSFER.TRANSFER_ID
                            ,EPS_T_PO_DETAIL.PO_NO
                            ,EPS_T_PO_DETAIL.ITEM_CD as PO_ITEM_CD
                            ,EPS_T_PO_DETAIL.ITEM_NAME as PO_ITEM_NAME
                            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2) + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as PO_DELIVERY_DATE
                            ,EPS_T_PO_DETAIL.ITEM_TYPE_CD as PO_ITEM_TYPE_CD
                            ,EPS_T_PO_DETAIL.ACCOUNT_NO as PO_ACCOUNT_NO
                            ,EPS_T_PO_DETAIL.RFI_NO as PO_RFI_NO
                            ,EPS_T_PO_DETAIL.UNIT_CD as PO_UNIT_CD
                            ,EPS_T_PO_DETAIL.QTY as PO_QTY
                            ,EPS_T_PO_DETAIL.ITEM_PRICE as PO_ITEM_PRICE
                            ,EPS_T_PO_DETAIL.AMOUNT as PO_AMOUNT
                            ,EPS_T_PO_HEADER.CURRENCY_CD as PO_CURRENCY_CD
                            ,EPS_T_PO_HEADER.SUPPLIER_NAME as PO_SUPPLIER_NAME
                            ,CONVERT(varchar, EPS_T_PO_HEADER.CLOSED_PO_DATE, 101) AS CLOSED_PO_DATE
                            ,EPS_T_PR_HEADER.PURPOSE
                            ,EPS_M_APP_STATUS_2.APP_STATUS_NAME AS STATUS_PO
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
                        from         
                            EPS_T_PR_DETAIL 
                        inner join
                            EPS_T_PR_HEADER 
                        on 
                            EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO 
                        left join
                            EPS_M_EMPLOYEE 
                        on 
                            EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE.NPK
                        left join
                            EPS_M_APP_STATUS 
                        on 
                            EPS_T_PR_HEADER.PR_STATUS = EPS_M_APP_STATUS.APP_STATUS_CD
                        inner join
                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_2
                        on 
                            EPS_T_PR_HEADER.REQUESTER = EPS_M_EMPLOYEE_2.NPK
                        left join
                            EPS_T_TRANSFER 
                        on 
                            EPS_T_PR_DETAIL.PR_NO = EPS_T_TRANSFER.PR_NO 
                            and EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_TRANSFER.ITEM_NAME 
                        left join
                            EPS_M_APP_STATUS EPS_M_APP_STATUS_1 
                        on 
                            EPS_T_TRANSFER.ITEM_STATUS = EPS_M_APP_STATUS_1.APP_STATUS_CD
                        left join
                            EPS_M_ITEM_TYPE
                        on
                            EPS_T_PR_DETAIL.ITEM_TYPE_CD = EPS_M_ITEM_TYPE.ITEM_TYPE_CD
                        left join
                            EPS_M_EMPLOYEE EPS_M_EMPLOYEE_3
                        on 
                            EPS_T_PR_HEADER.APPROVER = EPS_M_EMPLOYEE_3.NPK
                        left join
                            EPS_T_PO_DETAIL 
                        on 
                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID 
                        left join
                            EPS_T_PO_HEADER 
                        on  
                            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
                        left join 
                            EPS_M_APP_STATUS EPS_M_APP_STATUS_2
			on EPS_T_PO_HEADER.PO_STATUS=EPS_M_APP_STATUS_2.APP_STATUS_CD    
";
    if(count($wherePrSearch)) {
        $query_select_pr .= "where " . implode(' and ', $wherePrSearch);
    }
    $query_select_pr .= " order by 
                            EPS_T_PR_HEADER.ISSUED_DATE desc
                            ,EPS_T_PR_HEADER.PR_NO desc
                            ,INDEX_ITEM_NAME ";
    $sql_select_pr = $conn->query($query_select_pr);
    $numrow = 3;
    $itemNo = 1;
    while($row_select_pr = $sql_select_pr->fetch(PDO::FETCH_ASSOC))
    {
        $prNo           = $row_select_pr['PR_NO'];
        $prStatusName   = $row_select_pr['PR_STATUS_NAME'];
        $itemCd         = $row_select_pr['ITEM_CD'];
        $itemName       = $row_select_pr['ITEM_NAME'];
        $qty            = $row_select_pr['QTY'];
        $deliveryDate   = $row_select_pr['DELIVERY_DATE'];
        $supplierName   = $row_select_pr['SUPPLIER_NAME'];
        $prCharged      = $row_select_pr['CHARGED_BU_CD'];
        $itemTypeCd     = $row_select_pr['ITEM_TYPE_CD'];
        $itemTypeAlias  = $row_select_pr['ITEM_TYPE_ALIAS'];
        $accountNo      = $row_select_pr['ACCOUNT_NO'];
        $rfiNo          = $row_select_pr['RFI_NO'];
        $prUnitCd       = $row_select_pr['PR_UNIT_CD'];
        $itemStatusName = $row_select_pr['ITEM_STATUS_NAME'];
        $requesterName  = $row_select_pr['REQUESTER_NAME'];
        $itemPrice      = $row_select_pr['ITEM_PRICE'];
        $amount         = $row_select_pr['AMOUNT'];
        $remark         = $row_select_pr['REMARK'];
        $remark2         = $row_select_pr['REMARK_2'];
        $poNo           = $row_select_pr['PO_NO'];
        $poItemCd       = $row_select_pr['PO_ITEM_CD'];
        $poItemName     = $row_select_pr['PO_ITEM_NAME'];
        $poDeliveryDate = $row_select_pr['PO_DELIVERY_DATE'];
        $poItemTypeCd   = $row_select_pr['PO_ITEM_TYPE_CD'];
        $poAccountNo    = $row_select_pr['PO_ACCOUNT_NO'];
        $poRfiNo        = $row_select_pr['PO_RFI_NO'];
        $poUnitCd       = $row_select_pr['PO_UNIT_CD'];
        $poQty          = $row_select_pr['PO_QTY'];
        $poItemPrice    = $row_select_pr['PO_ITEM_PRICE'];
        $poAmount       = $row_select_pr['PO_AMOUNT'];
        $poCurrencyCd   = $row_select_pr['PO_CURRENCY_CD'];
        $poSupplierName = $row_select_pr['PO_SUPPLIER_NAME'];
        $closedPoDate   = $row_select_pr['CLOSED_PO_DATE'];
        $totalReceivedQty   = $row_select_pr['TOTAL_RECEIVED_QTY'];
        $totalCanceledQty   = $row_select_pr['TOTAL_CANCELED_QTY'];
        $totalOpenedQty     = $row_select_pr['TOTAL_OPENED_QTY'];
        $purpose     = $row_select_pr['PURPOSE'];
        $poStatus     = $row_select_pr['STATUS_PO'];
        $issuedDate    = $row_select_pr['ISSUED_DATE'];
                                            
        $totalReceiveQty   = $totalReceivedQty - ($totalCanceledQty + $totalOpenedQty);
        
        if(trim($poDeliveryDate) == "" || strlen(trim($poDeliveryDate)) == 2)
        {
            $poDeliveryDate = "";
        }
        
        $accountVal = '';

        if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
        {
            if(strlen($accountNo) == 1)
            {
                $accountVal = '0'.$accountNo;
            }
            else
            {
                $accountVal = $accountNo; 
            }
        }
        if($itemTypeCd == '2')
        {
            $accountVal = $rfiNo;
        }

        if($poItemTypeCd == '1' || $poItemTypeCd == '3' || $poItemTypeCd == '4')
        {
            $poItemTypeCd = 'EXP';
            if(strlen($poAccountNo) == 1)
            {
                $poAccountNo = '0'.$poAccountNo;
            }
            else
            {
                $poAccountNo = $poAccountNo; 
            }
        }
        if($poItemTypeCd == '2')
        {
            $poItemTypeCd = 'RFI';
            $poAccountNo = $poRfiNo;
        }

        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$prNo);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$prStatusName);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$requesterName);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$prCharged);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$itemCd);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,iconv("UTF-8", "ISO-8859-1//TRANSLIT", $itemName), PHP_EOL);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$deliveryDate);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$itemPrice);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$qty);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$prUnitCd);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$amount);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$supplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$numrow,$itemStatusName);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$numrow,$poNo);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$numrow,$poItemCd);
        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$numrow,$poItemName);
        $objPHPExcel->getActiveSheet()->setCellValue('R'.$numrow,$poDeliveryDate);
        $objPHPExcel->getActiveSheet()->setCellValue('S'.$numrow,$poItemTypeCd);
        $objPHPExcel->getActiveSheet()->setCellValue('T'.$numrow,$poAccountNo);
        $objPHPExcel->getActiveSheet()->setCellValue('U'.$numrow,$poQty);
        $objPHPExcel->getActiveSheet()->setCellValue('V'.$numrow,$totalReceiveQty);
        $objPHPExcel->getActiveSheet()->setCellValue('W'.$numrow,$poUnitCd);
        $objPHPExcel->getActiveSheet()->setCellValue('X'.$numrow,$poCurrencyCd);
        $objPHPExcel->getActiveSheet()->getStyle('Y'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('Y'.$numrow,$poItemPrice);
        $objPHPExcel->getActiveSheet()->getStyle('Z'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->setCellValue('Z'.$numrow,$poAmount);
        $objPHPExcel->getActiveSheet()->setCellValue('AA'.$numrow,$poSupplierName);
        $objPHPExcel->getActiveSheet()->setCellValue('AB'.$numrow,$closedPoDate);
        $objPHPExcel->getActiveSheet()->setCellValue('AC'.$numrow,$itemTypeAlias);
        $objPHPExcel->getActiveSheet()->setCellValue('AD'.$numrow,$rfiNo);
        $objPHPExcel->getActiveSheet()->setCellValue('AE'.$numrow,$accountNo);
        $objPHPExcel->getActiveSheet()->setCellValue('AF'.$numrow,$purpose);
        $objPHPExcel->getActiveSheet()->setCellValue('AG'.$numrow,$remark);
        $objPHPExcel->getActiveSheet()->setCellValue('AH'.$numrow,$remark2);
        $objPHPExcel->getActiveSheet()->setCellValue('AI'.$numrow,$poStatus);
        $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$numrow,$issuedDate);
        $numrow++;
        $itemNo++;
    }
}       
    
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('EPS_PR_Search');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
// Save Excel 2007 file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EPS_PR_Search.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objPHPExcel->save('php://output');
?>
