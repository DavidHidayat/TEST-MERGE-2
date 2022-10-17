<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PR_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Email/COM_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/mail_lib/class.smtp.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'/lib/pdf/fpdf16/fpdf.php';

$action         = $_GET['action'];
//$mailFrom       = $_SESSION['sinet'];
$mailFrom = "IT.TACI@taci.toyota-industries.com";
$mailFromName   = $_SESSION['snotes'];

//if($action == "ManualSendPo")
//{
//    $mailTo       = "muh.iqbal@taci.toyota-industries.com";
//    
//    $mailSubject  = "[EPS] Purchase Order Running Manually";
//    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
//    $mailMessage .= "Dear EPS Administrator,";
//    $mailMessage .= "<br><br>Please check EPS server because user already running PO report manually.";
//    $mailMessage .= "<br>Thank you";
//    $mailMessage .= "</font>";
//    
//    manualReportMail($mailTo,$mailFrom,$mailFromName,$mailSubject,$mailMessage);
//}

class SimpleTable extends FPDF
{
    function generateTable($no)
    {
        for($i=1;$i<=10;$i++)
        {
            $this->cell(20,10,$no,1,0,"C");
            $this->cell(20,10," * ".$i,1,0,"C");
            $this->cell(20,10," = ".$i*$no,1,1,"C");
        }
    }    
    
    function HeaderPo($deliveryPlantVal)
    {
        if($deliveryPlantVal == 'HAMADEN Sunter Plant')
        {
            //Logo
            $this->Image('../../lib/pdf/fpdf16/tutorial/hdi_small.jpg',5,3);

            //Font
            $this->SetFont('Arial','B',12);

            //Title
            $this->Text(47,5,'P.T HAMADEN INDONESIA MANUFACTURING');
            $this->Ln(3);

            //Font
            $this->SetFont('Arial','',6);
            $this->Text(47,9,'NPWP: 31.275.009.4-431.000');
            $this->SetFont('Arial','U',6);
            $this->Text(47,13,'HEAD OFFICE/Sunter Factory:');
            $this->SetFont('Arial','',6);
            $this->Text(47,16,'Jl. Gaya Motor I No.6 Sunter II Kel. Sungai Bambu,');
            $this->Text(47,19,'Tj.Priok Jakarta Utara 14330, Indonesia');
            $this->Text(47,22,'Phone : (+62)21 6512279 (Hunting)');
            $this->Ln(11);
        }
        else if($deliveryPlantVal == 'DENSO SALES Sunter Plant')
        {
            //Logo
            $this->Image('../../lib/pdf/fpdf16/tutorial/denso.jpg',3,3);

            //Font
            $this->SetFont('Arial','B',12);

            //Title
            $this->Text(47,5,'P.T DENSO SALES INDONESIA');
            $this->Ln(3);

            //Font
			$this->SetFont('Arial','',6);
            $this->Text(47,9,'NPWP: 02.414.335.6-056.000');
			$this->SetFont('Arial','U',6);
            $this->Text(47,13,'HEAD OFFICE/Sunter Factory:');
            $this->SetFont('Arial','',6);
            $this->Text(47,16,'Jl. Gaya Motor I No.6 Sunter II Kel. Sungai Bambu,');
            $this->Text(47,19,'Tj.Priok Jakarta Utara 14330, Indonesia');
            $this->Text(47,22,'Phone : (021) 6512279 (Hunting)');
            $this->Ln(11);
        }
        else
        {
            //Logo
            $this->Image('../../lib/pdf/fpdf16/tutorial/tacilogo2.jpg',3,3);

            //Font
            $this->SetFont('Arial','B',12);

            //Title
            $this->Text(47,5,'PT. TD Automotive Compressor Indonesia');
            $this->Ln(3);

            //Font
            $this->SetFont('Arial','',6);
            $this->Text(47,9,'NPWP: 31.275.009.4-431.000');
            $this->SetFont('Arial','U',6);
            $this->Text(47,13,'Office / Factory:');
            $this->SetFont('Arial','',6);
            $this->Text(47,16,'Jl. Selayar IV Blok L3, Kawasan Industri MM 2100');
            $this->Text(47,19,'Cikarang Barat, Bekasi 17530, Jawa Barat, Indonesia');
            $this->Text(47,22,'Phone : (+62)21 28517699 Fax : (+62)21 28517599');
            $this->Ln(11);
        }
    }
    
    function HeaderTablePoItem()
    {
        $this->SetFillColor(255,255,255);
        //$this->SetMargins(1, 1);
        $this->SetFont('Arial','B',7);
        $this->Cell(7,10,'NO'.'.','LTR',0,'C',1); 
        $this->Cell(116,10,'ITEM DESCRIPTION','LTR',0,'C',1); 
        $this->Cell(13,10,'QTY','LTR',0,'C',1); 
        $this->Cell(12,10,'U/M','LTR',0,'C',1); 
        $this->Cell(27,10,'PRICE','LTR',0,'C',1); 
        $this->Cell(27,10,'TOTAL AMOUNT','LTR',0,'C',1); 
        $this->Ln(7);
    }
    
    function DetailTablePoItem()
    {
        $this->SetFillColor(255,255,255);
        //$this->SetMargins(1, 1);
        $this->Cell(7,55,'','LTR',0,'C',1); 
        $this->Cell(116,55,'','LTR',0,'C',1); 
        $this->Cell(13,55,'','LTR',0,'C',1); 
        $this->Cell(12,55,'','LTR',0,'C',1); 
        $this->Cell(27,55,'','LTR',0,'C',1); 
        $this->Cell(27,55,'','LTR',1,'C',1); 
        $this->SetX(4);
        $this->Cell(148,5,'','LTR',0,'C',1); 
        $this->Cell(27,5,'','LTR',0,'C',1); 
        $this->Cell(27,5,'','LTR',1,'C',1); 
    }
}

$query = "select 
            EPS_T_PO_HEADER.PO_NO
	    ,EPS_T_PO_HEADER.SUPPLIER_CD
            ,EPS_T_PO_HEADER.SUPPLIER_NAME
            ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)
             +'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
            ,EPS_T_PO_HEADER.ISSUED_BY
	    ,EPS_M_SUPPLIER.CONTACT
            ,EPS_M_SUPPLIER.PHONE  
            ,EPS_M_SUPPLIER.FAX  
            ,EPS_M_SUPPLIER.EMAIL
            ,EPS_M_SUPPLIER.VAT
            ,EPS_T_PO_HEADER.ADDITIONAL_REMARK    
            ,EPS_T_PO_HEADER.CURRENCY_CD       
            ,EPS_T_PO_HEADER.DELIVERY_PLANT                                                    
          from
            EPS_T_PO_HEADER
          left join
            EPS_M_SUPPLIER
          on
            EPS_T_PO_HEADER.SUPPLIER_CD = EPS_M_SUPPLIER.SUPPLIER_CD
          where
            PO_STATUS = '1230'";
$sql = $conn->query($query);
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $poNo           = $row['PO_NO'];
	$supplierCd		= $row['SUPPLIER_CD'];
    $supplierName   = $row['SUPPLIER_NAME'];
    $issuedDate     = $row['ISSUED_DATE'];
    $issuedBy       = $row['ISSUED_BY'];
    $deliveryDate   = $row['DELIVERY_DATE'];
    $addRemark      = $row['ADDITIONAL_REMARK'];
    $contactName    = $row['CONTACT'];
    $phone          = $row['PHONE'];
    $fax            = $row['FAX'];
    $currencyCd     = $row['CURRENCY_CD'];
    $deliveryPlant  = $row['DELIVERY_PLANT'];
    $supplierEmail  = $row['EMAIL'];
    $vat            = $row['VAT'];
    $addRemark2     = '';
    
	if(strlen($addRemark) > 70)
    {
        $addRemark2 = substr($addRemark,70);
        $addRemark =  substr($addRemark,0,70);
    }
	
	if($currencyCd == 'IDR')
    {
        $currencyCd = 'Rp';
    }
    
    if($deliveryPlant == 'JK'){
        $deliveryPlant = 'DENSO Sunter Plant';
    }

    if($deliveryPlant == 'GT'){
        $deliveryPlant = 'PT. TD Aotomotive Compressor Indonesia';
    }

    if($deliveryPlant == 'JF'){
        $deliveryPlant = 'DENSO Fajar Plant';
    }

    if($deliveryPlant == 'SI'){
        $deliveryPlant = 'DENSO SALES Sunter Plant';
    }

    if($deliveryPlant == 'HD'){
        $deliveryPlant = 'HAMADEN Sunter Plant';
    }
    
    $query_select_t_po_approver = "select top 1 
                                    EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
                                    ,EPS_T_PO_APPROVER.APPROVAL_DATE
                                    ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
                                from
                                    EPS_T_PO_APPROVER 
                                inner join
                                    EPS_M_EMPLOYEE 
                                on 
                                    EPS_T_PO_APPROVER.NPK = EPS_M_EMPLOYEE.NPK
                                left join
                                    EPS_M_APPROVAL_STATUS
                                on
                                    EPS_T_PO_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
                                where      
                                    EPS_T_PO_APPROVER.PO_NO = '$poNo'
                                order by
                                    EPS_T_PO_APPROVER.APPROVER_NO desc";
    $sql_select_t_po_approver = $conn->query($query_select_t_po_approver);
    $row_select_t_po_approver = $sql_select_t_po_approver->fetch(PDO::FETCH_ASSOC);
    $approverName   = $row_select_t_po_approver['APPROVER_NAME'];
    $approvalDate   = $row_select_t_po_approver['APPROVAL_DATE'];
    $approvalPoStatus = $row_select_t_po_approver['APPROVAL_STATUS_NAME'];
    
    $pdf=new SimpleTable();
    $pdf->AddPage();
    $pdf->HeaderPo($deliveryPlant);
    $pdf->SetFillColor(255,255,255);
    
    $pdf->SetFont('Arial','B',12);
    $pdf->SetX(4);
    $pdf->Cell(202,5,'PURCHASE ORDER','LTR',1,'C');

    $pdf->SetFont('Arial','',8);     
    $pdf->SetX(4);
    $pdf->Cell(7,5,'TO','L',0,'L');
    $pdf->Cell(70,5,': '.$supplierName,'',0,'L');
    $pdf->Cell(10,5,'','',0,'L');
    $pdf->Cell(65,5,'','',0,'L');
    $pdf->Cell(12,5,'NO','',0,'L');
    $pdf->Cell(38,5,': '.$poNo,'R',1,'L');

    $pdf->SetX(4);
    $pdf->Cell(7,5,'PIC','L',0,'L');
    $pdf->Cell(70,5,': '.$contactName,'',0,'L');
    $pdf->Cell(10,5,$vat,'',0,'L');
    $pdf->Cell(65,5,'','',0,'L');
    $pdf->Cell(12,5,'DATE','',0,'L');
    $pdf->Cell(38,5,': '.$issuedDate,'R',1,'L');

    $pdf->SetX(4);
    $pdf->HeaderTablePoItem();
    
    $pdf->SetX(4);
    $pdf->DetailTablePoItem();
    
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
    
    $pdf->SetX(4);
    $pdf->Cell(85,5,'NOTE: ','LT',0,'L');
    $pdf->Cell(90,5,'REMARKS: ','LT',0,'L');
    $pdf->Cell(27,5,'PROCUREMENT','LTR',1,'C');

    $pdf->SetX(4);
    $pdf->Cell(2,5,'','L',0,'C');
    $pdf->Cell(33,5,'DELIVERY DUE DATE MAX ','',0,'L');
    $pdf->Cell(50,5,': '.$deliveryDate,'',0,'L');
    $pdf->Cell(2,5,'','L',0,'L');
    $pdf->Cell(88,5,$addRemark,'',0,'L');
    $pdf->Cell(27,5,$approvalPoStatus,'LR',1,'C');

    $pdf->SetX(4);
    $pdf->Cell(2,5,'','L',0,'C');
    $pdf->Cell(26,5,'DELIVERY TO ','',0,'L');
    $pdf->Cell(57,5,': '.$deliveryPlant,'',0,'L');
    $pdf->Cell(2,5,'','L',0,'L');
    $pdf->Cell(88,5,$addRemark2,'',0,'L');
    $pdf->Cell(27,5,$approvalDate,'LR',1,'C');
 
    $pdf->SetX(4);
    $pdf->Cell(5,5,'','LB',0,'C');
    $pdf->Cell(80,5,'','B',0,'L');
    $pdf->Cell(90,5,'','LB',0,'C');
    $pdf->Cell(27,5,trim($approverName),'LBR',1,'C');

    $itemPoNo = 1;
    $intY = 51;
    $query_select_t_po_detail = "select
                                    ITEM_NAME
                                    ,QTY
                                    ,UNIT_CD
                                    ,ITEM_PRICE
                                    ,AMOUNT
                                 from
                                    EPS_T_PO_DETAIL
                                 where
                                    PO_NO = '$poNo'";
    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
    
    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
        $itemName   = $row_select_t_po_detail['ITEM_NAME'];
        $qty        = $row_select_t_po_detail['QTY'];
        $unitCd     = $row_select_t_po_detail['UNIT_CD'];
        $itemPrice  = $row_select_t_po_detail['ITEM_PRICE'];
        $amount     = $row_select_t_po_detail['AMOUNT'];
		
        $split = explode('.', $qty);
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
		
		$split_amount = explode('.', $amount);
        if($split_amount[1] == 0)
        {
            $amount = number_format($amount);
        }
        else
        {
			$amount = number_format($amount,2);
        }
        $pdf->SetFont('Arial','',7);   
        $pdf->Text(6,$intY,$itemPoNo);
        $pdf->SetFont('Arial','',6); 
        if(strlen($itemName) > 98)
        {
            $pdf->SetFont('Arial','',5); 
        }
        $pdf->Text(12,$intY,$itemName);
        $pdf->SetFont('Arial','',7); 
        if(strlen($qty) == 1){
            $pdf->Text(135,$intY,$qty);
        }
        else if(strlen($qty) == 2){
            $pdf->Text(134,$intY,$qty);
        }
        else if(strlen($qty) == 3){
            $pdf->Text(133,$intY,$qty);
        }
        else if(strlen($qty) == 4){
            $pdf->Text(132,$intY,$qty);
        }
        else if(strlen($qty) == 5){
            $pdf->Text(131,$intY,$qty);
        }
        else if(strlen($qty) == 6){
            $pdf->Text(130,$intY,$qty);
        }
        $pdf->Text(144,$intY,$unitCd);
        
        $pdf->Text(155,$intY,$currencyCd);
        if(strlen($itemPrice) == 14){
            $pdf->Text(160,$intY,$itemPrice);
        }
        else if(strlen($itemPrice) == 13){
            $pdf->Text(161,$intY,$itemPrice);
        }
        else if(strlen($itemPrice) == 12){
            $pdf->Text(162,$intY,$itemPrice);
        }
        else if(strlen($itemPrice) == 11){
            $pdf->Text(163,$intY,$itemPrice);
        }
        else if(strlen($itemPrice) == 10){
            $pdf->Text(164,$intY,$itemPrice);
        }  
        else if(strlen($itemPrice) == 9){
            $pdf->Text(165,$intY,$itemPrice);
        }  
        else if(strlen($itemPrice) == 8){
            $pdf->Text(166,$intY,$itemPrice);
        }   
        else if(strlen($itemPrice) == 7){
            $pdf->Text(167,$intY,$itemPrice);
        }   
        else if(strlen($itemPrice) == 6){
            $pdf->Text(168,$intY,$itemPrice);
        }   
        else if(strlen($itemPrice) == 5){
            $pdf->Text(169,$intY,$itemPrice);
        }   
        else if(strlen($itemPrice) == 4){
            $pdf->Text(170,$intY,$itemPrice);
        }  
        else if(strlen($itemPrice) == 3){
            $pdf->Text(171,$intY,$itemPrice);
        }  
        else{
            $pdf->Text(172,$intY,$itemPrice);
        }
        
        $pdf->Text(183,$intY,$currencyCd);
        if(strlen($amount) == 14){
            $pdf->Text(187,$intY,$amount);
        }
        else if(strlen($amount) == 13){
            $pdf->Text(188,$intY,$amount);
        }
        else if(strlen($amount) == 12){
            $pdf->Text(189,$intY,$amount);
        }
        else if(strlen($amount) == 11){
            $pdf->Text(190,$intY,$amount);
        }
        else if(strlen($amount) == 10){
            $pdf->Text(191,$intY,$amount);
        }  
        else if(strlen($amount) == 9){
            $pdf->Text(192,$intY,$amount);
        }  
        else if(strlen($amount) == 8){
            $pdf->Text(193,$intY,$amount);
        }   
        else if(strlen($amount) == 7){
            $pdf->Text(194,$intY,$amount);
        }   
        else if(strlen($amount) == 6){
            $pdf->Text(195,$intY,$amount);
        }   
        else if(strlen($amount) == 5){
            $pdf->Text(196,$intY,$amount);
        }   
        else if(strlen($amount) == 4){
            $pdf->Text(197,$intY,$amount);
        }  
        else{
            $pdf->Text(198,$intY,$amount);
        }
        $itemPoNo++;
        $intY =  $intY + 5;
    }
    
    // Get total of amount item
    $query_select_total_t_po_detail = "select 
                                            SUM(amount) as PO_AMOUNT 
                                        from 
                                            EPS_T_PO_DETAIL 
                                        where 
                                            (PO_NO = '$poNo')";
    $sql_select_total_t_po_detail = $conn->query($query_select_total_t_po_detail);
    $row_select_total_t_po_detail = $sql_select_total_t_po_detail->fetch(PDO::FETCH_ASSOC);
    $poAmount = $row_select_total_t_po_detail['PO_AMOUNT'];
    //$poAmount=  number_format($poAmount);
    $split_total_amount = explode('.', $poAmount);
    if($split_total_amount[1] == 0)
    {
        $poAmount = number_format($poAmount);
    }
    else
    {
        $poAmount = number_format($poAmount,2);
    }
	
    $pdf->Text(158,105,'TOTAL');
    $pdf->Text(183,105,$currencyCd);
    if(strlen($poAmount) == 14){
		$pdf->Text(187,105,$poAmount);
	}
	else if(strlen($poAmount) == 13){
		$pdf->Text(188,105,$poAmount);
	}
	else if(strlen($poAmount) == 12){
		$pdf->Text(189,105,$poAmount);
	}
	else if(strlen($poAmount) == 11){
		$pdf->Text(190,105,$poAmount);
	}
	else if(strlen($poAmount) == 10){
		$pdf->Text(191,105,$poAmount);
	}  
	else if(strlen($poAmount) == 9){
		$pdf->Text(192,105,$poAmount);
	}  
	else if(strlen($poAmount) == 8){
		$pdf->Text(193,105,$poAmount);
	}   
	else if(strlen($poAmount) == 7){
		$pdf->Text(194,105,$poAmount);
	}   
	else if(strlen($poAmount) == 6){
		$pdf->Text(195,105,$poAmount);
	}   
	else if(strlen($poAmount) == 5){
		$pdf->Text(196,105,$poAmount);
	}   
	else if(strlen($poAmount) == 4){
		$pdf->Text(197,105,$poAmount);
	}  
	else{
		$pdf->Text(198,105,$poAmount);
	}

    $pdf->SetFont('Arial','I',7);   
    $pdf->SetX(4);
    $pdf->Cell(162,5,'This Purchase Order created by PT. TD Automotive Compressor Indonesia System (Validation by Purchasing Section without Signature)','',0,'L');

    $pdf->SetFont('Arial','',7);  
    $pdf->Rect(177,129,29,4);
    $pdf->Text(179,132,'No. : PTY-001-FT-Y004','1',1,'L');
    $pdf->Rect(177,133,29,4);
    $pdf->Text(179,136,'Revision : 4','1',1,'L');

    $dir='C:/xampp/htdocs/EPS/db/CREATE_FILE/PDF_FILE/';
    $filename= $poNo.".pdf";
    $pdf ->Output($dir.$filename);
    echo "Save PDF in folder";

    /** 
     * UPDATE EPS_T_PO_HEADER
     */
    $query_update_t_po_header = "update 
                                    EPS_T_PO_HEADER 
                                 set 
                                    PO_STATUS = '1250' 
                                    ,SEND_PO_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                 where 
                                    PO_NO = '$poNo'";
    $conn->query($query_update_t_po_header);
    /** 
     * UPDATE EPS_T_PO_DETAIL
     */            
     $query_update_t_po_detail = "update
                                    EPS_T_PO_DETAIL
                                  set
                                    RO_STATUS = '1310'
                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                  where
                                    PO_NO = '$poNo'";
     $conn ->query($query_update_t_po_detail);
                
    /**
     * UPDATE EPS_T_TRANSFER
     **/
    $query_select_t_po_detail = "select 
                                    REF_TRANSFER_ID
                                 from
                                    EPS_T_PO_DETAIL
                                 where
                                    PO_NO = '$poNo'";
    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
    while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC)){
        $refTransferId = $row_select_t_po_detail['REF_TRANSFER_ID'];
        
		/** 
         * SELECT EPS_T_TRANSFER
         **/
        $query_select_t_transfer = "select
                                        NEW_QTY
                                        ,ACTUAL_QTY
                                    from
                                        EPS_T_TRANSFER
                                    where 
                                        TRANSFER_ID = '$refTransferId'";
        $sql_select_t_transfer = $conn->query($query_select_t_transfer);
        $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
        $actualQty  = $row_select_t_transfer['ACTUAL_QTY'];
        $newQty     = $row_select_t_transfer['NEW_QTY'];
        
        if($newQty == $actualQty)
        {
            $query_update_t_transfer = "update
                                        EPS_T_TRANSFER
                                    set
                                        ITEM_STATUS = '".constant('1310')."'
                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                    where
                                        TRANSFER_ID = '$refTransferId'";
            $conn ->query($query_update_t_transfer);
        }
    }
    
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
	
    if($supplierMail == '')
	{
		$supplierMail = "wiharyo@taci.toyota-industries.com";
	}    
	/**
     * SELECT MAIL PROC.IN CHARGE
     */
    $query_select_m_dscid = "select
                                INETML
                            from
                                EPS_M_DSCID
                            where 
                                INOPOK in ($issuedBy)";
	$sql_select_m_dscid = $conn ->query($query_select_m_dscid);
    while($row_select_m_dscid = $sql_select_m_dscid->fetch(PDO::FETCH_ASSOC)){
        $issuerMail = trim($row_select_m_dscid['INETML']);
    }
    
    
	
	
    /**********************************************************************
     * SEND MAIL
     **********************************************************************/
    $mailFrCreateom       = "it.taci@taci.toyota-industries.com";
    $mailFromName   = "EPS ADMINISTRATOR/TACI";  
	
    $mailCc         = $issuerMail.","."wiharyo@taci.toyota-industries.com,KARYOTO@taci.toyota-industries.com,ahmadjafar@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com";
    $mailTo     	= trim($supplierMail);
    $supplierMailVb =  str_replace(",",";", $mailTo);
    
	
    $mailSubject  = "[EPS] Purchase Order No.".$poNo;
    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
                
                
                $query_insert_mail = "insert into 
                                            EPS_T_PO_MAIL_SUPPLIER
                                            (PO_NO
                                                ,SUPPLIER_MAIL
                                                ,CURRENCY_CD
                                                ,MAIL_SUBJECT
                                                ,SENT)
                                    VALUES
                                            ('$poNo'
                                            ,'$supplierMailVb'
                                            ,'$currencyCd'
                                            ,'$mailSubject'
                                            ,'0')";
            $conn ->query($query_insert_mail);
	
    if($currencyCd == "Rp")
    {
        $mailMessage .= "Yth. Bapak/Ibu Supplier PT.TD Automotive Compressor Indonesia";
        $mailMessage .= "<br><br>Berikut pesanan kami :";
        $mailMessage .= "<br><br>Kami tunggu kedatangan barang sesuai jadwal pengiriman pada PO.";
        $mailMessage .= "<br><br><u>Jika dalam waktu maksimal 3 hari setelah PO terkirim dari system tidak ada konfirmasi/keluhan dari pihak supplier, maka kami anggap pihak supplier sanggup mengirim barang sesuai isi PO.</u>";
        $mailMessage .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
        $mailMessage .= "<br><br>Hormat kami,";
        $mailMessage .= "<br>Procurement Dept. | General Supplies";
        $mailMessage .= "<br>PT. TD Automotive Compressor Indonesia";
        $mailMessage .= "<br><br> 021- 28517699 Ext. 301 / 310";
        $mailMessage .= "<br><br>";
    }
    else
    {
		$mailCc		  = $issuerMail.","."wiharyo@taci.toyota-industries.com,KARYOTO@taci.toyota-industries.com,ahmadjafar@taci.toyota-industries.com,muh.iqbal@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com";
        $mailMessage .= "Dear Sir or Madam";
        $mailMessage .= "<br><br>Herewith we would like to send you our order: ";
        $mailMessage .= "<br><br>Kindly confirm to us if you have received this PO.";
        $mailMessage .= "<br>Please don't hesitate to contact us if there are any discrepancy between PO and your quotation.";
        $mailMessage .= "<br><br>**Note:";
        $mailMessage .= "<br>1. Every Goods Shipping must comply with Indonesian Government Regulation (or Custom rule).";
        $mailMessage .= "<br>2. In terms of Service supplied by Non-Domestic Indonesian resident, application of Double Taxation Avoidance (DTA) should be proceeded under Tax Treaty between supplier's country and Indonesia.";
        $mailMessage .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;Format of Certificate of Domicile specified in the ruling must be completed (Form-DGT 1 & Form-DGT 2).";
        $mailMessage .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;Having the 2 forms completed, the supplier shall be deducted by Income Tax in Indonesia 10%. Otherwise, the service income shall be deducted 20%.";
        $mailMessage .= "<br>3. Please mention PO No. on your shipping documents (invoice, packing list, B/L) and send it to our email A.S.A.P.";
        $mailMessage .= "<br>4. Invoice address: Jl. Selayar IV Blok L3, Kawasan Industri MM 2100, Cikarang Barat, Bekasi 17530, Jawa Barat, Indonesia ";
        $mailMessage .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;Shipping address is depend on PO.";
        $mailMessage .= "<br>5. Please send all original documents (invoice and packing list) to Mr. Karyoto (Procurement Dept.)";
        $mailMessage .= "<br><br>Thank you for your attention and cooperation.";
        $mailMessage .= "<br><br>Best regards,";
        $mailMessage .= "<br><br>Procurement Dept. | General Supplies";
        $mailMessage .= "<br>PT. TD Automotive Compressor Indoensia";
        $mailMessage .= "<br><br>";
        $mailMessage .= " Mr. Karyoto e-mail: KARTOYO@taci.toyota-industries.com Phone: (+62 21) 28517699 ext. 301 / 310.";
        $mailMessage .= "<br><br>";
    }
                
    //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
    //$a = poSendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc, $poNo);
}
?>
