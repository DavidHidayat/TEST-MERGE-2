<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/CN_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';
/** Include FPDF */
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/pdf/fpdf16/fpdf.php';
       
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 

date_default_timezone_set('Asia/Jakarta');
$currentDateFile    = date('n/d/y');
$currentTimeFile    = date('G:i');
$currentCnMonth    = $_GET['cnDatePrm'];
$monthNum           = substr($currentCnMonth,4);
$currentMonthAlias  = strtoupper(date('M', mktime(0, 0, 0, $monthNum, 10))); // March
$currentYearAlias   = substr($currentCnMonth,0,4);

$fileNameArray_D    = array();
$msg = "";

if($currentCnMonth != '')
{

    set_time_limit(1800);
    ini_set('memory_limit', '256M');
    
    /******************************************************************************************
     * - CREATE PDF FILE 
     * - SEND TO SUPPLIER
     ******************************************************************************************/
    
    class PDF_MC_Table extends FPDF
    {

        function Header()
        {
            $cnNo           = $GLOBALS['cnNo'];
            $companyName    = $GLOBALS['companyNameVal'];
            $supplierName   = $GLOBALS['supplierName'];
            $supplierNumber = $GLOBALS['supplierNumber'];
            $supplierCd     = $GLOBALS['supplierCd'];
            $vat            = $GLOBALS['vatVal'];
            $currencyCd     = $GLOBALS['currencyCdVal'];
            $email          = $GLOBALS['emailVal'];
            $currentDateFile= $GLOBALS['currentDateFile'];
            $currentTimeFile= $GLOBALS['currentTimeFile'];
            $currentMonthAlias = $GLOBALS['currentMonthAlias'];
            $currentYearAlias = $GLOBALS['currentYearAlias'];

            $this->HeaderTableCnItem($companyName,$supplierCd,$supplierName,$supplierNumber,$vat,$currencyCd,$email,$cnNo,$currentDateFile,$currentTimeFile,$currentMonthAlias,$currentYearAlias);
        }

        function HeaderTableCnItem($companyName,$supplierCd,$supplierName,$supplierNumber,$vat,$currencyCd,$email,$cnNo,$currentDateFile,$currentTimeFile,$currentMonthAlias,$currentYearAlias)
        {
            $this->SetFillColor(255,255,255);
            $this->SetX(4);
            $this->SetFont('Courier','',7);
            $this->Cell(70,2,$companyName,'',0,'L');
            $this->Cell(20,2,'PCCN111','',0,'L');
            $this->Cell(30,2,$currentMonthAlias.' '.$currentYearAlias,'',0,'L');
            $this->Cell(10,2,'DATE','',0,'L');
            $this->Cell(20,2,$currentDateFile,'',0,'L');
            $this->Cell(10,2,'TIME','',0,'L');
            $this->Cell(20,2,$currentTimeFile,'',0,'L');
            $this->Cell(15,2,'PAGE','',0,'L');
            $this->Cell(20,2,$this->PageNo(),'',1,'L');

            $this->SetX(4);
            $this->Cell(185,8,'C R E D I T  N O T E S ( GENERAL SUPPLIES PROCUREMENT )','',1,'C');

            $this->SetX(4);
            $this->Cell(15,3,'SUPPLIER','',0,'L');
            $this->Cell(105,3,': '.$supplierName,'',0,'L');
            $this->Cell(30,3,'VAT : '.$vat,'',0,'L');
            $this->Cell(50,3,$supplierNumber,'',1,'L');

            $this->SetX(4);
            $this->Cell(15,3,'CODE','',0,'L');
            $this->Cell(105,3,': '.$supplierCd,'',0,'L');
            $this->Cell(30,3,'CUR : '.$currencyCd,'',0,'L');
            $this->Cell(27,3,'CREDIT NOTES NO.','',0,'L');
            $this->Cell(50,3,' : '.$cnNo,'',1,'L');

            $this->SetX(4);
            $this->Cell(15,3,'EMAIL','',0,'L');
            $this->Cell(180,3,': '.$email,'',1,'L');
            $this->Ln(3);

            $this->SetX(4);
            $this->Cell(16,3,'P/O','',0,'C'); 
            $this->Cell(70,3,'ITEM NAME','',0,'C'); 
            $this->Cell(11,3,'BU','',0,'C'); 
            $this->Cell(7,3,'LOC','',0,'C'); 
            $this->Cell(16,3,'RCV. DATE','',0,'C'); 
            $this->Cell(11,3,'E/I','',0,'C'); 
            $this->Cell(13,3,'QTY','',0,'C'); 
            $this->Cell(8,3,'U/M','',0,'L'); 
            $this->Cell(25,3,'PRICE','',0,'C'); 
            $this->Cell(25,3,'TOTAL','',1,'C'); 
            //$this->Ln(3);
        }
        function Footer()
        {
            //Position at 1.5 cm from bottom
            $this->SetY(-15);
            //Arial italic 8
            $this->SetFont('Courier','I',8);
            //Text color in gray
            $this->SetTextColor(128);
            //Page number
            //$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'L');
        }
        var $widths;
        var $aligns;

        function SetWidths($w)
        {
            //Set the array of column widths
            $this->widths=$w;
        }

        function SetAligns($a)
        {
            //Set the array of column alignments
            $this->aligns=$a;
        }

        function Row($data)
        {
            $this->SetFont('Courier','',7);
            //Calculate the height of the row
            $nb=0;
            for($i=0;$i<count($data);$i++)
                $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
                $h=4*$nb;
                //Issue a page break first if needed
                $this->CheckPageBreak($h);
                //Draw the cells of the row
                for($i=0;$i<count($data);$i++)
                {
                    $w=$this->widths[$i];
                    $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    //Save the current position
                    if($i==0)
                    {
                        $x=$this->SetX(4);
                    }
                    $x=$this->GetX();
                    $y=$this->GetY();
                    //Draw the border
                    $this->Rect($x,$y,$w,$h,'D');
                    //Print the text
                    $this->MultiCell($w,4,$data[$i],0,$a);
                    //Put the position to the right of the cell
                    $this->SetXY($x+$w,$y);
                }
                //Go to the next line
                $this->Ln($h);
        }

        function CheckPageBreak($h)
        {
            //If the height h would cause an overflow, add a new page immediately
            if($this->GetY()+$h>$this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
        }

        function NbLines($w,$txt)
        {
            //Computes the number of lines a MultiCell of width w will take
            $cw=&$this->CurrentFont['cw'];
            if($w==0)
                $w=$this->w-$this->rMargin-$this->x;
                $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
                $s=str_replace("\r",'',$txt);
                $nb=strlen($s);
            if($nb>0 and $s[$nb-1]=="\n")
                $nb--;
                $sep=-1;
                $i=0;
                $j=0;
                $l=0;
                $nl=1;
                while($i<$nb)
                {
                    $c=$s[$i];
                    if($c=="\n")
                    {
                        $i++;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                        continue;
                    }
                    if($c==' ')
                        $sep=$i;
                        $l+=$cw[$c];
                    if($l>$wmax)
                    {
                        if($sep==-1)
                        {
                            if($i==$j)
                                $i++;
                        }
                        else
                        $i=$sep+1;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                    }
                    else
                        $i++;
                }
            return $nl;
        }
    }

    $dirCN= $_SERVER['DOCUMENT_ROOT']."/EPS/db/REPORT/CN/PDF/".$currentCnMonth;
    // Create directory by Year Month CN
    if(!is_dir($dirCN)){    
        mkdir($dirCN);
    }

    $query_select_t_cn_transfer = "select     
                                    EPS_T_CN_HEADER.CN_NO
                                    ,EPS_T_CN_HEADER.SUPPLIER_CD
                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                    ,EPS_T_CN_HEADER.COMPANY_CD
                                    ,EPS_M_COMPANY.COMPANY_NAME
                                    ,EPS_M_SUPPLIER.EMAIL
                                    ,EPS_T_CN_HEADER.SUPPLIER_NUMBER
                                    ,EPS_T_CN_HEADER.VAT_CD
                                    ,EPS_T_CN_HEADER.CURRENCY_CD
                                from         
                                    EPS_T_CN_HEADER 
                                left join
                                    EPS_M_SUPPLIER 
                                on 
                                    EPS_T_CN_HEADER.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD 
                                left join
                                    EPS_M_COMPANY 
                                on 
                                    EPS_T_CN_HEADER.COMPANY_CD = EPS_M_COMPANY.COMPANY_CD 
                                where     
                                    EPS_T_CN_HEADER.CLOSING_MONTH = '$currentCnMonth'
                                group by 
                                    EPS_T_CN_HEADER.CN_NO
                                    ,EPS_T_CN_HEADER.SUPPLIER_CD
                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                    ,EPS_T_CN_HEADER.COMPANY_CD
                                    ,EPS_M_COMPANY.COMPANY_NAME
                                    ,EPS_M_SUPPLIER.EMAIL
                                    ,EPS_T_CN_HEADER.SUPPLIER_NUMBER
                                    ,EPS_T_CN_HEADER.VAT_CD
                                    ,EPS_T_CN_HEADER.CURRENCY_CD order by EPS_T_CN_HEADER.SUPPLIER_CD DESC";
    $sql_select_t_cn_transfer = $conn->query($query_select_t_cn_transfer);
    while($row_select_t_cn_transfer = $sql_select_t_cn_transfer->fetch(PDO::FETCH_ASSOC))
    {
        $cnNo               = $row_select_t_cn_transfer['CN_NO'];
        $supplierCd         = $row_select_t_cn_transfer['SUPPLIER_CD'];
        $supplierName       = $row_select_t_cn_transfer['SUPPLIER_NAME'];
        $supplierNumber     = $row_select_t_cn_transfer['SUPPLIER_NUMBER']; 
        $companyCd          = $row_select_t_cn_transfer['COMPANY_CD'];
        $companyNameVal     = $row_select_t_cn_transfer['COMPANY_NAME'];
        $vatVal             = $row_select_t_cn_transfer['VAT_CD'];
        $currencyCdVal      = $row_select_t_cn_transfer['CURRENCY_CD'];
        $emailVal           = $row_select_t_cn_transfer['EMAIL'];
       
        if($vatVal == 'VAT')
        {
            $vatVal = '1';
        }
        else
        {
            $vatVal = '';
        }

        //Create new pdf file
        $pdf=new PDF_MC_Table('P','mm','A4');

        //Add first page
        $pdf->AddPage();
        $pdf->SetFont('Courier','',7);

        $itemPoNo = 1;
        $countOfPo = 0;
        $intY = 28;
        $query_select_t_po = "select     
                                EPS_T_CN_DETAIL.PO_NO
                                ,EPS_T_CN_DETAIL.ITEM_NAME
                                ,EPS_T_CN_DETAIL.CHARGED_BU
                                ,EPS_T_CN_DETAIL.DELIVERY_PLANT
                                ,CONVERT(VARCHAR(24), EPS_T_CN_DETAIL.CLOSED_PO_DATE, 120) as CLOSED_PO_DATE
                                ,EPS_T_CN_DETAIL.ITEM_TYPE_CD
                                ,EPS_T_CN_DETAIL.OBJECT_ACCOUNT
                                ,EPS_M_ACCOUNT.ACCOUNT_CD
                                ,EPS_T_CN_DETAIL.QTY
                                ,EPS_T_CN_DETAIL.UNIT_CD
                                ,EPS_T_CN_DETAIL.ITEM_PRICE
                                ,EPS_T_CN_DETAIL.AMOUNT
                            from
                                EPS_T_CN_DETAIL 
                            left join
                                EPS_M_ACCOUNT 
                            on 
                                EPS_T_CN_DETAIL.OBJECT_ACCOUNT = EPS_M_ACCOUNT.ACCOUNT_NO
                            left join
                                EPS_T_CN_HEADER 
                            on 
                                EPS_T_CN_HEADER.CN_NO = EPS_T_CN_DETAIL.CN_NO
                            where     
                                EPS_T_CN_HEADER.CLOSING_MONTH = '$currentCnMonth'
                                and EPS_T_CN_DETAIL.CN_NO = '$cnNo'
                            order by
                                EPS_T_CN_DETAIL.SUPPLIER_NAME
                                ,EPS_T_CN_DETAIL.COMPANY_CD
                                ,EPS_T_CN_DETAIL.PO_NO ";
        $sql_select_t_po = $conn->query($query_select_t_po);
        while($row_select_t_po = $sql_select_t_po->fetch(PDO::FETCH_ASSOC))
        {
            $poNo           = $row_select_t_po['PO_NO'];
            $itemName       = $row_select_t_po['ITEM_NAME'];
            $qty            = $row_select_t_po['QTY'];
            $itemPrice      = $row_select_t_po['ITEM_PRICE'];
            $amount         = $row_select_t_po['AMOUNT'];
            $unitCd         = $row_select_t_po['UNIT_CD'];
            $itemTypeCd     = $row_select_t_po['ITEM_TYPE_CD'];
            $objectAccount  = $row_select_t_po['OBJECT_ACCOUNT'];
            $accountCd      = $row_select_t_po['ACCOUNT_CD'];
            $chargedBu      = $row_select_t_po['CHARGED_BU'];
            $deliveryPlant  = $row_select_t_po['DELIVERY_PLANT'];
            $closedPoDate   = $row_select_t_po['CLOSED_PO_DATE'];

            date_default_timezone_set('Asia/Jakarta');
            $closedPoDate   = date("d/m/y", strtotime($closedPoDate));
    
            if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4')
            {
                $objectAccount = $accountCd;
            }
            if($itemTypeCd == '2')
            {
                $objectAccount = $objectAccount;
            }
            
            if($initialPoNo == $row_select_t_po['PO_NO'])
            {
                $poNo           = '';
            }
            else
            {
                $countOfPo ++;
                $poNo = $row_select_t_po['PO_NO'];
                $initialPoNo = $row_select_t_po['PO_NO'];
            }    

            $split = explode('.', $qty);
            if($split[1] == 0)
            {
                $qty = number_format($qty);
            }
            
            $amount = number_format($amount,2);
            $itemPrice = number_format($itemPrice,2);
            
            $pdf->SetX(4);
            $pdf->SetDrawColor(255, 255, 255);
            $pdf->SetFont('Courier','',7);
            $pdf->SetWidths(array(
                    16
                    ,70
                    ,11
                    ,7
                    ,16
                    ,11
                    ,13
                    ,8
                    ,25
                    ,25));
            $pdf->SetAligns(array(
                    'L'
                    ,'L'
                    ,'L'
                    ,'C'
                    ,'L'
                    ,'L'
                    ,'R'
                    ,'L'
                    ,'R'
                    ,'R'));
            $pdf->Row(array(
                    $poNo
                    ,$itemName
                    ,$chargedBu
                    ,$deliveryPlant
                    ,$closedPoDate
                    ,$objectAccount
                    ,$qty
                    ,$unitCd
                    ,$itemPrice
                    ,$amount));
        }
        $query_select_t_po_header = "select
                                        TAXABLE_AMOUNT
                                        ,GROSS_AMOUNT
                                    from
                                        EPS_T_CN_HEADER
                                    where
                                        CN_NO = '$cnNo'";
        $sql_select_t_po_header= $conn->query($query_select_t_po_header);
        $row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC);
        $poAmount   = $row_select_t_po_header['TAXABLE_AMOUNT'];
        $tax        = ($poAmount * 0.11);
        $poAmountAfter = $row_select_t_po_header['GROSS_AMOUNT'];
        $poAmount   =  number_format($poAmount,2);
        $tax        =  number_format($tax,2);
        $poAmountAfter = number_format($poAmountAfter,2);
        $pdf->SetX(4);
        $pdf->Cell(152,4,'COUNT OF P/O :'.$countOfPo,'L',0,'L');
        $pdf->Cell(25,4,'TOTAL :','L',0,'R');
        $pdf->Cell(25,4,$poAmount,'L',1,'R');

        if($vatVal == '1')
        {
            $pdf->SetX(4);
            $pdf->Cell(152,4,'','L',0,'L');
            $pdf->Cell(25,4,'TAX (11%) :','L',0,'R');
            $pdf->Cell(25,4,$tax,'L',1,'R');
            $pdf->SetX(4);
            $pdf->Cell(152,4,'','L',0,'L');
            $pdf->Cell(25,4,'AFTER TAX :','L',0,'R');
            $pdf->Cell(25,4,$poAmountAfter,'L',1,'R');
        }

        if($companyCd == 'D')
        {
            $companyNameAlias = "DNIA";
        }
        if($companyCd == 'S')
        {
            $companyNameAlias = "DSIA";
        }
        if($companyCd == 'H')
        {
            $companyNameAlias = "HDI";
        }
        if($companyCd == 'T')
        {
            $companyNameAlias = "TACI";
        }
        $newSupplierName = str_replace  ("/", "-", $supplierName);

        $dirCN_D= $dirCN.'/'.$companyNameAlias;
        // Create directory by Year Month CN
        if(!is_dir($dirCN_D)){    
            mkdir($dirCN_D);
        }
        $dirCN_S= $dirCN.'/'.$companyNameAlias;
        // Create directory by Year Month CN
        if(!is_dir($dirCN_S)){    
            mkdir($dirCN_S);
        }
        $dirCN_H= $dirCN.'/'.$companyNameAlias;
        // Create directory by Year Month CN
        if(!is_dir($dirCN_H)){    
            mkdir($dirCN_H);
        }
        $dirCN_T= $dirCN.'/'.$companyNameAlias;
        // Create directory by Year Month CN
        if(!is_dir($dirCN_T)){    
            mkdir($dirCN_T);
        }

        if($companyCd == 'D')
        {
            $dir = $dirCN_D."/";
        }
        if($companyCd == 'S')
        {
            $dir = $dirCN_S."/";
        }
        if($companyCd == 'H')
        {
            $dir = $dirCN_H."/";
        }
        if($companyCd == 'T')
        {
            $dir = $dirCN_T."/";
        }

        $filename = trim($companyNameVal)." CN ".$cnNo."-".$currentCnMonth." ".$newSupplierName.".pdf";
        $pdf ->Output($dir.$filename);
        //echo "Save PDF in folder"."<br/>";

       /**********************************************************************
        * SEND MAIL
        **********************************************************************/
        $mailFrom       = "bayu.thr@taci.toyota-industries.com";
        $mailFromName   = "EPS ADMINISTRATOR/DNIA";  

        /** 
         * SELECT EPS_M_SUPPLIER 
         */           
        $query_m_supplier = "select 
                                EMAIL
								,CURRENCY_CD
                            from
                                EPS_M_SUPPLIER
                            where
                                SUPPLIER_CD = '$supplierCd'";
        $sql_m_supplier = $conn ->query($query_m_supplier);
        $row_m_supplier = $sql_m_supplier->fetch(PDO::FETCH_ASSOC);
        $supplierMail = $row_m_supplier['EMAIL'];
        $supplierCurrency   = $row_m_supplier['CURRENCY_CD'];
        if($supplierMail == '' || $supplierCurrency != 'IDR')
        {
            $supplierMail = "bayu.thr@taci.toyota-industries.com";
        }    
        $mailCc         = "andry@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com,muh.iqbal@taci.toyota-industries.com, ahmadjafar@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com, tinamartina@taci.toyota-industries.com";

        $mailTo     = $supplierMail;
        $mailSubject  = "** [EPS] CREDIT NOTES ".$currentCnMonth." ".$cnNo."-".$supplierName;
        $mailMessage  = "";
        $newFileName  = trim($companyNameVal)." CN ".$cnNo."-".$currentCnMonth." ".$newSupplierName;
        $fileLocation = $currentCnMonth."/".$companyNameAlias;
        cnSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $newFileName, $fileLocation);  
    }

    /******************************************************************************************
     * - CREATE EXCEL FILE 
     * - SEND TO INTERNAL GS AND ACCOUNTING
     ******************************************************************************************/
 
    $dirCNExcel= $_SERVER['DOCUMENT_ROOT']."/EPS/db/REPORT/CN/EXCEL/".$currentCnMonth;
    // Create directory by Year Month CN
    if(!is_dir($dirCNExcel)){    
        mkdir($dirCNExcel);
    }
	
    $companyCdArray = array('T');
    foreach($companyCdArray as $values)
    {

        $companyCdVal = $values;

        /** Include PHPExcel */
        require_once '../LIB/PHPExcel/Classes/PHPExcel.php';
            // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("IS Division")
                                    ->setLastModifiedBy("Administrator")
                                    ->setTitle("Download Credit Notes")
                                    ->setSubject("Credit Notes")
                                    ->setDescription("Credit Notes by criteria")
                                    ->setKeywords("EPS")
                                    ->setCategory("CN");

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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', "NO");  
        $objPHPExcel->getActiveSheet()->setCellValue('B1', "ADDRESS NO");  	
        $objPHPExcel->getActiveSheet()->setCellValue('C1', "SUPPLIER CODE");  
        $objPHPExcel->getActiveSheet()->setCellValue('D1', "SUPPLIER NAME"); 
        $objPHPExcel->getActiveSheet()->setCellValue('E1', "CREDIT NOTE NO");  
        $objPHPExcel->getActiveSheet()->setCellValue('F1', "P/O");  
        $objPHPExcel->getActiveSheet()->setCellValue('G1', "SEKSI");  
        $objPHPExcel->getActiveSheet()->setCellValue('H1', "LOC");  
        $objPHPExcel->getActiveSheet()->setCellValue('I1', "ITEM NAME");  
        $objPHPExcel->getActiveSheet()->setCellValue('J1', "DATE"); 
        $objPHPExcel->getActiveSheet()->setCellValue('K1', "QTY");  
        $objPHPExcel->getActiveSheet()->setCellValue('L1', "U/M"); 
        $objPHPExcel->getActiveSheet()->setCellValue('M1', "PRICE");   
        $objPHPExcel->getActiveSheet()->setCellValue('N1', "AMOUNT");  
        $objPHPExcel->getActiveSheet()->setCellValue('O1', "OBJ.ACCOUNT");  
        $objPHPExcel->getActiveSheet()->setCellValue('P1', "VAT"); 
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', "CURRENCY");  
        $objPHPExcel->getActiveSheet()->setCellValue('R1', "PR NO");  

        // FREEZEPANE            
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $numrow     = 2;
        $itemNo     = 1; 
        $query_select_t_cn_detail = "select     
                                        EPS_T_CN_HEADER.SUPPLIER_NUMBER
                                        ,EPS_T_CN_HEADER.SUPPLIER_CD
                                        ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                        ,EPS_T_CN_HEADER.CN_NO
                                        ,EPS_T_CN_DETAIL.PO_NO
                                        ,EPS_T_CN_DETAIL.CHARGED_BU
                                        ,EPS_T_CN_DETAIL.DELIVERY_PLANT
                                        ,EPS_T_CN_DETAIL.ITEM_NAME
                                        ,CONVERT(VARCHAR(24), EPS_T_CN_DETAIL.CLOSED_PO_DATE, 103) as CLOSED_PO_DATE
                                        ,EPS_T_CN_DETAIL.QTY
                                        ,EPS_T_CN_DETAIL.UNIT_CD
                                        ,EPS_T_CN_DETAIL.ITEM_PRICE
                                        ,EPS_T_CN_DETAIL.AMOUNT
                                        ,EPS_T_CN_DETAIL.ITEM_TYPE_CD
                                        ,EPS_T_CN_DETAIL.OBJECT_ACCOUNT
                                        ,EPS_M_ACCOUNT.ACCOUNT_CD
                                        ,EPS_T_CN_HEADER.VAT_CD
                                        ,EPS_T_CN_HEADER.CURRENCY_CD
                                        ,EPS_T_CN_DETAIL.PR_NO
                                    from
                                        EPS_T_CN_DETAIL 
                                    left join
                                        EPS_M_ACCOUNT 
                                    on 
                                        EPS_T_CN_DETAIL.OBJECT_ACCOUNT = EPS_M_ACCOUNT.ACCOUNT_NO
                                    left join
                                        EPS_T_CN_HEADER 
                                    on 
                                        EPS_T_CN_HEADER.CN_NO = EPS_T_CN_DETAIL.CN_NO
                                    where     
                                        EPS_T_CN_HEADER.CLOSING_MONTH = '$currentCnMonth'
                                        and EPS_T_CN_HEADER.COMPANY_CD = '$companyCdVal'
                                    order by
                                        EPS_T_CN_DETAIL.SUPPLIER_CD
                                        ,EPS_T_CN_DETAIL.PO_NO ";
        $sql_select_t_cn_detail = $conn->query($query_select_t_cn_detail);
        while($row_select_t_cn_detail = $sql_select_t_cn_detail->fetch(PDO::FETCH_ASSOC))
        {
            $supplierNumber = $row_select_t_cn_detail['SUPPLIER_NUMBER'];
            $supplierCd     = $row_select_t_cn_detail['SUPPLIER_CD'];
            $supplierName   = $row_select_t_cn_detail['SUPPLIER_NAME'];
            $cnNo           = $row_select_t_cn_detail['CN_NO'];
            $poNo           = $row_select_t_cn_detail['PO_NO'];
            $chargedBu      = $row_select_t_cn_detail['CHARGED_BU'];
            $deliveryPlant  = $row_select_t_cn_detail['DELIVERY_PLANT'];
            $itemName       = $row_select_t_cn_detail['ITEM_NAME'];
            $closedPoDate   = $row_select_t_cn_detail['CLOSED_PO_DATE'];
            $qty            = $row_select_t_cn_detail['QTY'];
            $unitCd         = $row_select_t_cn_detail['UNIT_CD'];
            $itemPrice      = $row_select_t_cn_detail['ITEM_PRICE'];
            $amount         = $row_select_t_cn_detail['AMOUNT'];
            $itemTypeCd     = $row_select_t_cn_detail['ITEM_TYPE_CD'];
            $objectAccount  = $row_select_t_cn_detail['OBJECT_ACCOUNT'];
            $accountCd      = $row_select_t_cn_detail['ACCOUNT_CD'];
            $vatCd          = $row_select_t_cn_detail['VAT_CD'];
            $currencyCd     = $row_select_t_cn_detail['CURRENCY_CD'];
            $prNo           = $row_select_t_cn_detail['PR_NO'];

            if($itemTypeCd == '1' || $itemTypeCd == '3' || $itemTypeCd == '4' || $itemTypeCd == '5')
            {
                $objectAccount = $accountCd;
            }

            /*$split = explode('.', $qty);
            if($split[1] == 0)
            {
                $qty = number_format($qty);
            }

            $split_item_price = explode('.', $itemPrice);
            if($split_item_price[1] == 0)
            {
                $itemPrice = number_format($itemPrice);
            }
            else
            {
                $itemPrice = number_format($itemPrice, 2);
            }

            $split_item_amount = explode('.', $amount);
            if($split_item_amount[1] == 0)
            {
                $amount = number_format($amount);
            }
            else
            {
                $amount = number_format($amount, 2);
            }*/

            if($vatCd == 'VAT')
            {
                $vatCd = '1';
            }
            else
            {
                $vatCd = '';
            }
            $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$numrow,$itemNo);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$numrow,$supplierNumber);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$numrow,$supplierCd);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$numrow,$supplierName);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$numrow,$cnNo);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$numrow,$poNo);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$numrow,$chargedBu);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$numrow,$deliveryPlant);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$numrow,$itemName);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$numrow,$closedPoDate);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$numrow,$qty);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$numrow,$unitCd);
            $objPHPExcel->getActiveSheet()->getStyle('M'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$numrow,$itemPrice);
            $objPHPExcel->getActiveSheet()->getStyle('N'.$numrow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$numrow,$amount);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$numrow,$objectAccount);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$numrow,'VAT : '.$vatCd);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$numrow,$currencyCd);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$numrow,$prNo);
            $numrow++;
            $itemNo++;
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('CN_'.$currentCnMonth);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        /** Untuk Menghilangkan email DNIA DSIA HDI saat closing. */
//        if($companyCdVal == 'D')
//        {
//            $objPHPExcel->save('CN/EXCEL/'.$currentCnMonth.'/EPS_Credit_Notes_DNIA_'.$currentCnMonth.'.xlsx');
//            $fileName = "EPS_Credit_Notes_DNIA_".$currentCnMonth;
//            $companyNameVal = "DNIA";
//        }
//        if($companyCdVal == 'H')
//        {
//            $objPHPExcel->save('CN/EXCEL/'.$currentCnMonth.'/EPS_Credit_Notes_HDI_'.$currentCnMonth.'.xlsx');
//            $fileName = "EPS_Credit_Notes_HDI_".$currentCnMonth;
//            $companyNameVal = "HDI";
//        }
//        if($companyCdVal == 'S')
//        {
//            $objPHPExcel->save('CN/EXCEL/'.$currentCnMonth.'/EPS_Credit_Notes_DSIA_'.$currentCnMonth.'.xlsx');
//            $fileName = "EPS_Credit_Notes_DSIA_".$currentCnMonth;
//            $companyNameVal = "DSIA";
//        }
        if($companyCdVal == 'T')
        {
            $objPHPExcel->save('CN/EXCEL/'.$currentCnMonth.'/EPS_Credit_Notes_TACI_'.$currentCnMonth.'.xlsx');
            $fileName = "EPS_Credit_Notes_TACI_".$currentCnMonth;
            $companyNameVal = "TACI";
        }
        
        $mailFrom       = "IT.TACI@taci.toyota-industries.com";
        $mailFromName   = "EPS ADMINISTRATOR/TACI";  
  
        $mailCcInCharge    = "andry@taci.toyota-industries.com,WIHARYO@taci.toyota-industries.com,ahmadjafar@taci.toyota-industries.com
                              ,Elvin@taci.toyota-industries.com,i.softwan@taci.toyota-industries.com,dwiyatno@taci.toyota-industries.com
                              ,muh.iqbal@taci.toyota-industries.com,bayu.thr@taci.toyota-industries.com,hanafi@taci.toyota-industries.com, tinamartina@taci.toyota-industries.com";

        $mailToInCharge     = "karyoto@taci.toyota-industries.com";
        $mailSubject  = "[EPS] CREDIT NOTES ".$currenctCnMonth." ".$companyNameVal;
        $mailMessage  = "";
        cnSendMailToInCharge($mailToInCharge, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCcInCharge, $fileName, $currentCnMonth);  
    }
	$msg = 'Success';
}
else
{
    $msg = 'Mandatory_1';
}
echo $msg;
?>
