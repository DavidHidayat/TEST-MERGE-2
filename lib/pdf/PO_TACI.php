<?php
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
require('fpdf16/fpdf.php');
ini_set('default_charset', 'utf-8');

$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 

$poNo   = $_GET['poNo'];
$userId = $_GET['userId'];
if(!isset($poNo)){
    echo "<script>document.location.href='".constant('NotAuthorize')."';</script>";
}
$query = "select 
            EPS_T_PO_HEADER.PO_NO
            ,EPS_T_PO_HEADER.SUPPLIER_NAME
            ,substring(EPS_T_PO_HEADER.ISSUED_DATE,7,2)
             +'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.ISSUED_DATE,1,4) as ISSUED_DATE
            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE,7,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,5,2)+'/'+substring(EPS_T_PO_HEADER.DELIVERY_DATE,1,4) as DELIVERY_DATE
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
            PO_NO = '$poNo'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$poNo           = $row['PO_NO'];
$supplierName   = $row['SUPPLIER_NAME'];
$issuedDate     = $row['ISSUED_DATE'];
$deliveryDate   = $row['DELIVERY_DATE'];
$addRemark      = $row['ADDITIONAL_REMARK'];
$contactName    = $row['CONTACT'];
$phone          = $row['PHONE'];
$fax            = $row['FAX'];
$currencyCd     = $row['CURRENCY_CD'];
$deliveryPlant  = $row['DELIVERY_PLANT'];
$supplierEmail  = $row['EMAIL'];
$vat		= $row['VAT'];
if(strlen($addRemark) > 70)
{
    $addRemark2 = substr($addRemark,70);
    $addRemark =  substr($addRemark,0,70);
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

class PDF_MC_Table extends FPDF
{
    
    var $angle=0;

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
    
    function WatermarkPo()
    {
        //Put the watermark
        $this->SetFont('helvetica','B',40);
        $this->SetTextColor(160,160,160);
        $this->RotatedText(55,37,'PO DOWNLOAD',0);
    }
    
    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }
    
    function HeaderPo($deliveryPlantVal)
    {
        $this->SetTextColor(0,0,0);
        if($deliveryPlantVal == 'HD')
        {
            //Logo
            $this->Image('fpdf16/tutorial/hdi_small.jpg',5,3);

            //Font
            $this->SetFont('Arial','B',12);

            //Title
            $this->Text(47,5,'P.T HAMADEN INDONESIA MANUFACTURING');
            $this->Ln(3);

            //Font
			$this->SetFont('Arial','',6);
            $this->Text(47,9,'NPWP: 01.071.827.8-055.000');
			$this->SetFont('Arial','U',6);
            $this->Text(47,13,'HEAD OFFICE/Sunter Factory:');
            $this->SetFont('Arial','',6);
			$this->Text(47,16,'Jl. Gaya Motor I No.6 Sunter II Kel. Sungai Bambu,');
			$this->Text(47,19,'Tj.Priok Jakarta Utara 14330, Indonesia');
            $this->Text(47,22,'Phone : (+62)21 6512279 (hunting)');
            $this->Ln(11);
        }
        else if($deliveryPlantVal == 'SI')
        {
            //Logo
            $this->Image('fpdf16/tutorial/denso.jpg',3,3);

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
            $this->Text(47,22,'Phone : (+62)21 6512279 (hunting)');
            $this->Ln(11);
        }
        else
        {
            //Logo
            $this->Image('fpdf16/tutorial/tacilogo2.jpg',3,3);

            //Font
            $this->SetFont('Arial','B',12);

            //Title
            $this->Text(47,5,'P.T TD AUTOMOTIVE COMPRESSOR INDONESIA');
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
        $this->SetTextColor(0,0,0);
        //$this->SetFillColor(255,255,255);
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
        $this->SetTextColor(0,0,0);
        //$this->SetFillColor(255,255,255);
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

//Create new pdf file
$pdf=new PDF_MC_Table('P','mm','A4');
//Add first page
$pdf->SetTextColor(0,0,0);
$pdf->AddPage();
$pdf->WatermarkPo();
$pdf->HeaderPo($deliveryPlant);

if($currencyCd == 'IDR')
{
    $currencyCd = 'Rp';
}
if($deliveryPlant == 'JK'){
    $deliveryPlant = 'DENSO Sunter Plant';
}

if($deliveryPlant == 'GT'){
    $deliveryPlant = 'TACI PLANT';
}

if($deliveryPlant == 'JF'){
    $deliveryPlant = 'DENSO 3rd Plant';
}

if($deliveryPlant == 'SI'){
    $deliveryPlant = 'DENSO SALES Sunter Plant';
}

if($deliveryPlant == 'HD'){
    $deliveryPlant = 'HAMADEN Sunter Plant';
}
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',12);
$pdf->SetX(4);
$pdf->Cell(202,7,'PURCHASE ORDER','LTR',1,'C');

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
$pdf->Cell(65,5,'NOTE: ','LT',0,'L');
$pdf->Cell(110,5,'REMARKS: ','LT',0,'L');
$pdf->Cell(27,5,'PROCUREMENT','LTR',1,'C');
    
$pdf->SetX(4);
$pdf->Cell(2,5,'','L',0,'C');
$pdf->Cell(33,5,'DELIVERY DUE DATE MAX ','',0,'L');
$pdf->Cell(30,5,': '.$deliveryDate,'',0,'L');
$pdf->Cell(2,5,'','L',0,'L');
if(strlen($addRemark) > 70)
{
    $pdf->SetFont('Arial','',6);
}
$pdf->Cell(108,5,$addRemark,'',0,'L');
$pdf->Cell(27,5,$approvalPoStatus,'LR',1,'C');

$pdf->SetX(4);
$pdf->Cell(2,5,'','L',0,'C');
$pdf->Cell(26,5,'DELIVERY TO ','',0,'L');
$pdf->Cell(37,5,': '.$deliveryPlant,'',0,'L');
$pdf->Cell(2,5,'','L',0,'L');
if(strlen($addRemark2) > 70)
{
    $pdf->SetFont('Arial','',6);
}
$pdf->Cell(108,5,$addRemark2,'',0,'L');
$pdf->Cell(27,5,$approvalDate,'LR',1,'C');
 
$pdf->SetX(4);
$pdf->Cell(5,5,'','LB',0,'C');
$pdf->Cell(60,5,'','B',0,'L');
$pdf->Cell(110,5,'','LB',0,'C');
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
        $itemName   = html_entity_decode($row_select_t_po_detail['ITEM_NAME']);
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
$split_total_amount = explode('.', $poAmount);
if($split_total_amount[1] == 0)
{
    $poAmount = number_format($poAmount);
}
else
{
    $poAmount = number_format($poAmount,2);
}

$pdf->Text(158,106,'TOTAL');
$pdf->Text(183,106,$currencyCd);
if(strlen($poAmount) == 14){
    $pdf->Text(187,106,$poAmount);
}
else if(strlen($poAmount) == 13){
    $pdf->Text(188,106,$poAmount);
}
else if(strlen($poAmount) == 12){
    $pdf->Text(189,106,$poAmount);
}
else if(strlen($poAmount) == 11){
    $pdf->Text(190,106,$poAmount);
}
else if(strlen($poAmount) == 10){
    $pdf->Text(191,106,$poAmount);
}  
else if(strlen($poAmount) == 9){
    $pdf->Text(192,106,$poAmount);
}  
else if(strlen($poAmount) == 8){
    $pdf->Text(193,106,$poAmount);
}   
else if(strlen($poAmount) == 7){
    $pdf->Text(194,106,$poAmount);
}   
else if(strlen($poAmount) == 6){
    $pdf->Text(195,106,$poAmount);
}   
else if(strlen($poAmount) == 5){
    $pdf->Text(196,106,$poAmount);
}   
else if(strlen($poAmount) == 4){
    $pdf->Text(197,106,$poAmount);
}  
else{
    $pdf->Text(198,106,$poAmount);
}

$pdf->SetFont('Arial','I',7);   
$pdf->SetX(4);
$pdf->Cell(162,5,'This Purchase Order created by PT. TD Automotive Compressor Indonesia System (Validation by Purchasing Section without Signature)','',0,'L');

$pdf->SetFont('Arial','',7);  
$pdf->Rect(177,129,29,4);
$pdf->Text(179,132,'No. : PT Y001FT Y004','1',1,'L');
$pdf->Rect(177,133,29,4);
$pdf->Text(179,136,'Revision : 3','1',1,'L');
   
//Send file
$pdf->Output();
?>