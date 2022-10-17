<?php 
function Print_CN_REPORT($CN_NO, $cnDatePrm){
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/CN_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.smtp.php';
/** Include FPDF */
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/pdf/fpdf16/fpdf.php';
       
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 

date_default_timezone_set('Asia/Jakarta');
$currentDateFile    = date('n/d/y');
$currentTimeFile    = date('G:i');
//$currentCnMonth    = $_GET['cnDatePrm'];
$currentCnMonth    = $cnDatePrm;
$cn_no=$CN_NO;
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

    $dirCN= $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/REPORT/CN/PDF/".$currentCnMonth;
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
                                    EPS_T_CN_HEADER.CLOSING_MONTH = '$currentCnMonth' AND EPS_T_CN_HEADER.CN_NO='$cn_no' 
									
                                group by 
                                    EPS_T_CN_HEADER.CN_NO
                                    ,EPS_T_CN_HEADER.SUPPLIER_CD
                                    ,EPS_T_CN_HEADER.SUPPLIER_NAME
                                    ,EPS_T_CN_HEADER.COMPANY_CD
                                    ,EPS_M_COMPANY.COMPANY_NAME
                                    ,EPS_M_SUPPLIER.EMAIL
                                    ,EPS_T_CN_HEADER.SUPPLIER_NUMBER
                                    ,EPS_T_CN_HEADER.VAT_CD
                                    ,EPS_T_CN_HEADER.CURRENCY_CD ";
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
        $tax        = ($poAmount * 0.1);
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
            $pdf->Cell(25,4,'TAX (10%) :','L',0,'R');
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
        $dirCN_T = $dirCN.'/'.$companyNameAlias;
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
        echo "Save PDF in folder"."<br/>";

       /**********************************************************************
        * SEND MAIL
        **********************************************************************/
        $mailFrom       = "karyoto@taci.toyota-industries.com";
        $mailFromName   = "EPS ADMINISTRATOR/TACI";  

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
            $supplierMail = "karyoto@taci.toyota-industries.com";
        }    
        $mailCc         = "karyoto@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com,ahmadjafar@taci.toyota-industries.com,ISON@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com";

        $mailTo     = $supplierMail;
        $mailSubject  = "[EPS] CREDIT NOTES ".$currentCnMonth." ".$supplierName;
        $mailMessage  = "";
        $newFileName  = trim($companyNameVal)." CN ".$cnNo."-".$currentCnMonth." ".$newSupplierName;
        $fileLocation = $currentCnMonth."/".$companyNameAlias;
        cnSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $newFileName, $fileLocation);  
    }

  
}
echo $msg;
}
?>
