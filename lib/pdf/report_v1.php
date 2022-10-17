<?php

require('fpdf16/fpdf.php');
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 

$prNo   = $_GET['prNo'];
$userId = $_GET['userId'];
/** Search in EPS_M_PR_PROC_APPROVER */
$query = "select 
            NPK
          from 
            EPS_M_PR_PROC_APPROVER
          where
            NPK = '$userId'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
if($row){
    $wherePrDetail = array();
    $wherePrDetail[] = "EPS_T_PR_DETAIL.ITEM_STATUS = '1060' ";
}
class PDF_MC_Table extends FPDF
{
        function Header()
        {
            if($this->PageNo()==1)
            {  
                //Logo
                $this->Image('fpdf16/tutorial/denso.jpg',10,10,35);

                //Font
                $this->SetFont('Arial','U',21);

                //Title
                $this->Cell(0,5,'PERMINTAAN PEMBELIAN',0,1,'C');
                $this->Ln(30);
                $this->headerTabelItem();
            }
            else
            {
                $this->headerTabelItem();
            }
        }
        
        function Footer()
	{
            //Position at 1.5 cm from bottom
            $this->SetY(-15);
            //Arial italic 8
            $this->SetFont('Arial','I',8);
            //Text color in gray
            $this->SetTextColor(128);
            //Page number
            $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
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
            //Calculate the height of the row
            $nb=0;
            for($i=0;$i<count($data);$i++)
                    $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
                    $w=$this->widths[$i];
                    $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    //Save the current position
                    $x=$this->GetX();
                    $y=$this->GetY();
                    //Draw the border
                    $this->Rect($x,$y,$w,$h);
                    //Print the text
                    $this->MultiCell($w,5,$data[$i],0,$a);
                    //Put the position to the right of the cell
                    $this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}
	
        function ApproverNama($dtapp)
        {
            //Calculate the height of the row
            $nb=0;
            for($i=0;$i<count($dtapp);$i++)
                    $nb=max($nb,$this->NbLines($this->widths[$i],$dtapp[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($dtapp);$i++)
		{
                    $w=$this->widths[$i];
                    $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    //Save the current position
                    $x=$this->GetX();
                    $y=$this->GetY();
                    //Draw the border
                    $this->Rect($x,$y,$w,$h);
                    //Print the text
                    $this->Cell($w,5,$dtapp[$i],1,0,$a);
                    //Put the position to the right of the cell
                    $this->SetXY($x+$w,$y);
		}
		//Go to the next line
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
        
        function headerTabelItem()
        {
            $this->SetFillColor(210,210,210);
            $this->SetFont('Arial','B',7);
            $this->Cell(7,10,'No'.'.','LTR',0,'C',1); 
            $this->Cell(80,10,'Nama Barang (Part No, Spec, Ukuran/Dimensi, Warna, Merk)','LTR',0,'C',1); 
            $this->Cell(30,10,'Remark','LTR',0,'C',1); 
            $this->Cell(10,8,'Rutin/','LTR',0,'C',1); 
            $this->Cell(15,10,'TGL Kirim','LTR',0,'C',1); 
            $this->Cell(13,7,'Charge ke','LTR',0,'C',1); 
            $this->Cell(15,8,'No.','LTR',0,'C',1); 
            $this->Cell(12,10,'Qty','LTR',0,'C',1); 
            $this->Cell(15,10,'U/M','LTR',0,'C',1); 
            $this->Cell(80,5,'Referensi User','LTR',1,'C',1); 

            $this->Cell(7,5,'','LRB',0,'C',0);               //no.
            $this->Cell(80,5,'','LRB',0,'C',0);              //nama barang
            $this->Cell(30,5,'','LRB',0,'C',0);              //remark
            $this->Cell(10,5,'Tidak','LRB',0,'C',1);         //rutin/tidak
            $this->Cell(15,5,'','LRB',0,'C',0);              //tgl kirim
            $this->Cell(13,5,'BU/Seksi','LRB',0,'C',1);      //charge
            $this->Cell(15,5,'Item','LRB',0,'C',1);          //item
            $this->Cell(12,5,'','LRB',0,'C',0);              //qty
            $this->Cell(15,5,'','LRB',0,'C',0);              //um
            $this->Cell(15,5,'Unit Prices',1,0,'C',1); 
            $this->Cell(45,5,'Supplier',1,0,'C',1); 
            $this->Cell(20,5,'Amount',1,0,'C',1); 
            $this->Ln(5);
        }
}


//Create new pdf file
$pdf=new PDF_MC_Table('L','mm','A4');
//Add first page
$pdf->AddPage();

/***************************************
 * Search in EPS_T_PR_HEADER
 ***************************************/
$query = "select 
            EPS_T_PR_HEADER.PR_NO
            ,EPS_T_PR_HEADER.COMPANY_CD
            ,EPS_M_COMPANY.COMPANY_NAME
            ,EPS_T_PR_HEADER.PLANT_CD
            ,EPS_M_PLANT.PLANT_NAME
            ,EPS_T_PR_HEADER.REQUESTER
            ,EPS_M_EMPLOYEE.NAMA1 as REQUESTER_NAME
            ,len(EPS_M_EMPLOYEE.NAMA1) as NAME_LENGTH
            ,EPS_T_PR_HEADER.BU_CD
            ,EPS_M_TBUNIT.NMBU1 as BU_NAME
            ,EPS_T_PR_HEADER.SPECIAL_TYPE_ID
            ,EPS_T_PR_HEADER.REQ_BU_CD
            ,EPS_T_PR_HEADER.CHARGED_BU_CD
            ,substring(EPS_T_PR_HEADER.ISSUED_DATE, 7, 2) + '/' 
            + substring(EPS_T_PR_HEADER.ISSUED_DATE, 5, 2) + '/' 
            + substring(EPS_T_PR_HEADER.ISSUED_DATE, 1, 4) as ISSUED_DATE
            ,EPS_M_EMPLOYEE2.NAMA1 as PROC_IN_CHARGE
            ,EPS_T_PR_HEADER.PROC_ACCEPT_DATE
          from 
            EPS_T_PR_HEADER 
          left join
            EPS_M_EMPLOYEE 
          on 
            ltrim(EPS_T_PR_HEADER.REQUESTER) = ltrim(EPS_M_EMPLOYEE.NPK) 
            and EPS_T_PR_HEADER.BU_CD = EPS_M_EMPLOYEE.LKDP 
          left join
            EPS_M_EMPLOYEE EPS_M_EMPLOYEE2
          on
            EPS_M_EMPLOYEE2.NPK = EPS_T_PR_HEADER.PROC_IN_CHARGE
          left join
            EPS_M_COMPANY 
          on 
            EPS_T_PR_HEADER.COMPANY_CD = EPS_M_COMPANY.COMPANY_CD 
          left join
            EPS_M_PLANT
          on 
            EPS_T_PR_HEADER.PLANT_CD = EPS_M_PLANT.PLANT_CD 
          left join
            EPS_M_TBUNIT 
          on 
            EPS_T_PR_HEADER.REQ_BU_CD = EPS_M_TBUNIT.KDBU
          where 
            EPS_T_PR_HEADER.PR_NO = '".$prNo."'";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);

$prNo       = $row['PR_NO'];
$issuedDate = $row['ISSUED_DATE'];
$requester  = $row['REQUESTER_NAME'];
$buCd       = $row['BU_CD'];
$buName     = $row['BU_NAME'];
$prIssuer   = $row['REQ_BU_CD'];
$specialType= $row['SPECIAL_TYPE_ID'];
$procInCharge   = $row['PROC_IN_CHARGE'];
$procAcceptDate = $row['PROC_ACCEPT_DATE'];
if($specialType == 'IT'){
    $specialType = 'IT Equipment';
}else if($specialType == 'NIT'){
    $specialType = 'Non IT Equipment';
}else{
    $specialType = '';
}

$pdf->SetFont('Arial','B',7);
$pdf->Text(10,22,'Nomer PP');
$pdf->Text(10,27,'Tanggal Buat PP');
$pdf->Text(10,32,'Ditulis Oleh');
$pdf->Text(10,37,'Dept./BU Code');
$pdf->Text(10,42,'Category');
$pdf->Text(35,22,': '.$prNo);
$pdf->Text(35,27,': '.$issuedDate);
$pdf->Text(35,32,': '.$requester);
$pdf->Text(35,37,': '.trim($buName).' / '.$prIssuer);
$pdf->Text(35,42,': '.$specialType);

/***************************************
 * Search in EPS_T_PR_APPROVER
 ***************************************/
$query = "select
            EPS_T_PR_APPROVER.APPROVAL_STATUS
            ,EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_NAME
            ,EPS_T_PR_APPROVER.APPROVAL_DATE
            ,EPS_M_EMPLOYEE.NAMA1 as APPROVER_NAME
            ,EPS_M_EMPLOYEE.JABAT
            ,EPS_M_OCCUPATION.ASJBT as OCCUPATION
          from 
            EPS_T_PR_APPROVER 
          left join
            EPS_M_EMPLOYEE 
          on 
            EPS_T_PR_APPROVER.NPK = EPS_M_EMPLOYEE.NPK 
          left join
            EPS_M_OCCUPATION 
          on 
            EPS_M_EMPLOYEE.JABAT = EPS_M_OCCUPATION.KDJBT
          left join 
            EPS_M_APPROVAL_STATUS
          on
            EPS_T_PR_APPROVER.APPROVAL_STATUS = EPS_M_APPROVAL_STATUS.APPROVAL_STATUS_CD
          where 
            EPS_T_PR_APPROVER.PR_NO = '$prNo' 
          order by 
            EPS_T_PR_APPROVER.APPROVER_NO asc";
$sql = $conn->query($query);
$xa = 230;
$ta = 231;
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $approverName   = $row['APPROVER_NAME'];
    $occupation     = $row['OCCUPATION'];
    $approvalStatus = $row['APPROVAL_STATUS_NAME'];
    $approvalDate   = $row['APPROVAL_DATE'];
    $pdf->SetFont('Arial','',6);
    //$pdf->SetWidths(array(27));
    //$pdf->SetAligns(array('C'));
    //$pdf->ApproverNama(array($nmapp));
    $pdf->Rect($xa, 19, 27, 5);
    $pdf->Text($ta, 22, $occupation);
    $pdf->Rect($xa, 19, 27, 19);        //koordinat x,y,w,h
    $pdf->Text($ta, 30, $approvalStatus);
    $pdf->Text($ta, 35, $approvalDate);
    $pdf->Rect($xa, 38, 27, 5);
    $pdf->Text($ta, 41, $approverName);
    $xa=$xa-27;
    $ta=$ta-27;
}
$pdf->Rect(257, 19, 30, 5);
$pdf->Text(258, 22, 'Diminta Oleh');
$pdf->Rect(257, 19, 30, 19);
$pdf->Text(258, 30, '');
$pdf->Rect(257, 38, 30, 5);
$pdf->Text(258, 41, $requester);

/***************************************
 * Search in EPS_T_PR_DETAIL
 ***************************************/
$query = "select 
            EPS_T_PR_DETAIL.ITEM_CD
            ,EPS_T_PR_DETAIL.ITEM_NAME
            ,substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 7, 2) 
            + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 5, 2) 
            + '/' + substring(EPS_T_PR_DETAIL.DELIVERY_DATE, 1, 4) as DELIVERY_DATE 
            ,EPS_T_PR_DETAIL.QTY
            ,EPS_T_PR_DETAIL.ITEM_PRICE
            ,EPS_T_PR_DETAIL.AMOUNT
            ,EPS_T_PR_DETAIL.ITEM_TYPE_CD
            ,EPS_T_PR_DETAIL.ACCOUNT_NO
            ,EPS_T_PR_DETAIL.RFI_NO
            ,EPS_T_PR_DETAIL.UNIT_CD
            ,EPS_T_PR_DETAIL.SUPPLIER_CD
            ,EPS_T_PR_DETAIL.SUPPLIER_NAME
            ,EPS_T_PR_DETAIL.REMARK
            ,EPS_T_PR_DETAIL.ITEM_STATUS
            ,EPS_T_PR_DETAIL.REASON_TO_REJECT_ITEM
            ,EPS_T_PR_DETAIL.REJECT_ITEM_BY
            ,EPS_T_PR_HEADER.CHARGED_BU_CD
            ,EPS_T_PR_DETAIL.ITEM_STATUS
          from 
            EPS_T_PR_DETAIL 
          left join
            EPS_T_PR_HEADER
          on
            EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
          where 
            EPS_T_PR_DETAIL.PR_NO = '".$prNo."'";
if(count($wherePrDetail)) {
    $query .= "and " . implode(' ', $wherePrDetail);
}
$sql = $conn->query($query);
$no = 0;
while($row = $sql->fetch(PDO::FETCH_ASSOC))
{
	$no++.'.';
        $itemCd         = $row['ITEM_CD'];
        $itemName       = $row['ITEM_NAME'];
        $deliveryDate   = $row['DELIVERY_DATE'];
        $qty            = $row['QTY'];
        $itemPrice      = $row['ITEM_PRICE'];
        $amount         = $row['AMOUNT'];
        $itemType       = $row['ITEM_TYPE_CD'];
        $accountCd      = $row['ACCOUNT_NO'];
        $rfiNo          = $row['RFI_NO'];
        $unitCd         = $row['UNIT_CD'];
        $supplierCd     = $row['SUPPLIER_CD'];
        $supplierName   = $row['SUPPLIER_NAME'];
        $remark         = $row['REMARK'];
        $prCharged      = $row['CHARGED_BU_CD'];
        $itemStatus     = $row['ITEM_STATUS'];
        // Set "Charge ke BU/Seksi" column
        if($itemType == '3'){
            $prCharged = 'N1000';
        }
        // set "No. Item" column
        if($itemType == '1' || $itemType == '3'){
            $itemType = $accountCd;
        }else if($itemType == '2'){
            $itemType = $rfiNo;
        }else{
            $itemType = '';
        }
        //Convert format number
        $qty = number_format($qty);  
        $itemPrice = number_format($itemPrice);    
        $amount = number_format($amount);
        // set reject item font
	if($itemStatus == '1070'){
            $amount = "(".$amount.")";
            $pdf->SetFont('Arial','B',6);
            $pdf->SetTextColor(231,15,15);
        }else{
            $pdf->SetFont('Arial','',6);
            $pdf->SetTextColor(0,0,0);
        }
	$pdf->SetWidths(array(7,80,30,10,15,13,15,12,15,15,45,20));
        $pdf->SetAligns(array('C','L','C','C','C','C','C','C','C','R','C','R'));
	$pdf->Row(array($no,$itemName,$remark,'',$deliveryDate,$prCharged,$itemType,$qty,$unitCd,$itemPrice,$supplierName,$amount));
        //echo $itemStatus;
        
}
$pdf->SetTextColor(0,0,0);
// Get total of amount item
$query = "select 
            SUM(amount) as PR_AMOUNT 
          from 
            EPS_T_PR_DETAIL 
          where 
            (PR_NO = '$prNo')
            and ITEM_STATUS = '1060' ";
$sql = $conn->query($query);
$row = $sql->fetch(PDO::FETCH_ASSOC);
$prAmount = $row['PR_AMOUNT'];
$prAmount=  number_format($prAmount);

$pdf->Cell(212,8,'* Untuk Pembelian mesin, harus ada pembanding baik merk, type, spec, fungsi, harga, supplier dsb',1,0,'R');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(45,8,'Total',1,0,'C');
$pdf->Cell(20,8,$prAmount,1,1,'R');

$pdf->Cell(212,8,'CATATAN :','LTR',0,'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25,8,'Diterima Oleh/Tgl','LTR',0,'C');
$pdf->Cell(40,8,$procInCharge,'LTR',1,'L');
$pdf->Cell(212,5,'','LBR',0,'R');
$pdf->Cell(25,5,'','LBR',0,'R');
$pdf->Cell(40,5,$procAcceptDate,'LBR',1,'R');

$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(40,6,'Referensi(Guidance only):',0);
$pdf->Cell(197,6,'',0);
$pdf->Cell(40,6,'PT. DENSO INDONESIA',0,1,'R');

$pdf->SetFont('Arial', '', 6);
$pdf->Cell(7,6,'',0,0,'C'); 
$pdf->Cell(20,6,'Jenis Barang','LTR',0,'C'); 
$pdf->Cell(20,6,'Supplier','LTR',0,'C'); 
$pdf->Cell(17,4,'Stock di','LTR',0,'C'); 
$pdf->Cell(60,3,'LEAD TIME (Rec PP ~ Rec Parts)','LTR',0,'C'); 
$pdf->Cell(88,3,'','trb',0,'C'); 
$pdf->Cell(65,3,'No: PTY001 - FT - Y - 003','LTR',1,'L'); 

$pdf->Cell(7,3,'',0,0,'C');               
$pdf->Cell(20,3,'','LRB',0,'C');                //jenis barang
$pdf->Cell(20,3,'','LRB',0,'C');                //supplier
$pdf->Cell(17,3,'Pasaran','LRB',0,'C');         //stock         
$pdf->Cell(25,3,'s/d Rp 5 Juta',1,0,'C');       //lead time
$pdf->Cell(35,3,'Diatas Rp 5 Juta',1,0,'C'); 
$pdf->Cell(88,3,'','trb',0,'C'); 
$pdf->Cell(65,3,'REVISI: 3',1,1,'L'); 

$pdf->Cell(7,3,'',0,0,'C');  
$pdf->Cell(20,3,'RUTIN',1,0,'L'); 
$pdf->Cell(20,3,'Jelas',1,0,'L'); 
$pdf->Cell(17,3,'Ada',1,0,'L'); 
$pdf->Cell(25,3,'1 Minggu',1,0,'C'); 
$pdf->Cell(35,3,'1 Minggu',1,1,'C'); 

$pdf->Cell(7,3,'',0,0,'C');  
$pdf->Cell(20,3,'TIDAK RUTIN','LTR',0,'L'); 
$pdf->Cell(20,3,'Jelas','LTR',0,'L'); 
$pdf->Cell(17,3,'Ada',1,0,'L'); 
$pdf->Cell(25,3,'1 Minggu',1,0,'C'); 
$pdf->Cell(35,3,'2 Minggu',1,1,'C'); 

$pdf->Cell(7,3,'',0,0,'C');  
$pdf->Cell(20,3,'','LR',0,'C'); 
$pdf->Cell(20,3,'','LR',0,'C'); 
$pdf->Cell(17,3,'Tidak Ada',1,0,'L'); 
$pdf->Cell(25,3,'4 Minggu',1,0,'C'); 
$pdf->Cell(35,3,'4 Minggu',1,1,'C'); 

$pdf->Cell(7,3,'',0,0,'C');  
$pdf->Cell(20,3,'','LR',0,'C'); 
$pdf->Cell(20,3,'Tidak Jelas','LTR',0,'L'); 
$pdf->Cell(17,3,'Ada',1,0,'L'); 
$pdf->Cell(25,3,'1 Minggu',1,0,'C'); 
$pdf->Cell(35,3,'2 Minggu',1,1,'C'); 

$pdf->Cell(7,3,'',0,0,'C');  
$pdf->Cell(20,3,'','LBR',0,'C'); 
$pdf->Cell(20,3,'','LBR',0,'C'); 
$pdf->Cell(17,3,'Tidak Ada',1,0,'L'); 
$pdf->Cell(25,3,'4 Minggu',1,0,'C'); 
$pdf->Cell(35,3,'4 Minggu',1,1,'C'); 
//Send file
$pdf->Output();
?>