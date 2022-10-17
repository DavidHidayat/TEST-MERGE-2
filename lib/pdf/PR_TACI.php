<?php
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."/db/Constant.php";
require('fpdf16/fpdf.php');
$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 
$prNo   = $_GET['prNo'];
$userId = $_GET['userId'];
if(!isset($prNo)){
    echo "<script>document.location.href='".constant('NotAuthorize')."';</script>";
}
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
                $this->Image('fpdf16/tutorial/tacilogo.jpg',10,10,35);

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
            $this->SetFont('Times','B',7);
            $this->Cell(7,7,'No'.'.','LTR',0,'C',1); 
            $this->Cell(16,7,'Nomer PP','LTR',0,'C',1); 
            $this->Cell(25,7,'Nama Barang','LTR',0,'C',1); 
            $this->Cell(40,7,'Part No, Spec, Ukuran/Dimensi,','LTR',0,'C',1); 
            $this->Cell(15,7,'Merk','LTR',0,'C',1); 
            $this->Cell(10,7,'Rutin /','LTR',0,'C',1); 
            $this->Cell(10,7,'Subtitusi','LTR',0,'C',1); 
            $this->Cell(15,7,'TGL Kirim','LTR',0,'C',1); 
            $this->Cell(9,7,'Charge','LTR',0,'C',1); 
            $this->Cell(9,7,'Asset','LTR',0,'C',1); 
            $this->Cell(9,7,'No.','LTR',0,'C',1); 
            $this->Cell(9,7,'Qty','LTR',0,'C',1); 
            $this->Cell(7,7,'U/M','LTR',0,'C',1); 
            $this->Cell(73,7,'Referensi User','LTR',0,'C',1); 
            $this->Cell(23,7,'CATATAN','LTR',1,'C',1); 

            $this->Cell(7,5,'','LRB',0,'C',1);               //no.
            $this->Cell(16,5,'','LRB',0,'C',1);              //nomor pp
            $this->Cell(25,5,'','LRB',0,'C',1);              //nama barang
            $this->Cell(40,5,'Warna dll','LRB',0,'C',1);              
            $this->Cell(15,5,'','LRB',0,'C',1);              
            $this->Cell(10,5,'Tidak','LRB',0,'C',1);              
            $this->Cell(10,5,'Blh/Tdk','LRB',0,'C',1);              
            $this->Cell(15,5,'','LRB',0,'C',1);              //tgl kirim
            $this->Cell(9,5,'BU','LRB',0,'C',1);   //charge
            $this->Cell(9,5,'BU','LRB',0,'C',1);   //charge
            $this->Cell(9,5,'Item','LRB',0,'C',1);          //no. item
            $this->Cell(9,5,'','LRB',0,'C',1);              //qty
            $this->Cell(7,5,'','LRB',0,'C',1);              //um
            $this->Cell(23,5,'Unit Prices',1,0,'C',1); 
            $this->Cell(25,5,'Total',1,0,'C',1); 
            $this->Cell(25,5,'Supplier',1,0,'C',1); 
            $this->Cell(23,5,'','LRB',0,'C',1);              //alasan
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
            ,EPS_T_PR_HEADER.PURPOSE
          from 
            EPS_T_PR_HEADER 
          left join
            EPS_M_EMPLOYEE 
          on 
            ltrim(EPS_T_PR_HEADER.REQUESTER) = ltrim(EPS_M_EMPLOYEE.NPK) 
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
$requester  = stripslashes($row['REQUESTER_NAME']);
$buCd       = $row['BU_CD'];
$buName     = $row['BU_NAME'];
$prIssuer   = $row['REQ_BU_CD'];
$specialType= $row['SPECIAL_TYPE_ID'];
$procInCharge   = $row['PROC_IN_CHARGE'];
$procAcceptDate = $row['PROC_ACCEPT_DATE'];
$purpose        = $row['PURPOSE'];
if($specialType == 'IT'){
    $specialType = 'IT Equipment';
}else if($specialType == 'NIT'){
    $specialType = 'Non IT Equipment';
}else{
    $specialType = '';
}

$pdf->SetFont('Times','B',8);
//$pdf->Text(10,22,'Nomer PP');
$pdf->Text(10,27,'Tanggal Buat PP');
$pdf->Text(10,32,'Ditulis Oleh');
$pdf->Text(10,37,'Dept./BU Code');
//$pdf->Text(10,42,'Category');
//$pdf->Text(35,22,': '.$prNo);
$pdf->Text(35,27,': '.$issuedDate);
$pdf->Text(35,32,': '.$requester);
$pdf->Text(35,37,': '.trim($buName).' / '.$prIssuer);
//$pdf->Text(35,42,': '.$specialType);

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
            EPS_T_PR_APPROVER.PR_NO = '$prNo' AND EPS_M_EMPLOYEE.NAMA1 NOT LIKE  '%TAUFIK HIDAYAT%'
          order by 
            EPS_T_PR_APPROVER.APPROVER_NO asc";
$sql = $conn->query($query);
$xa = 230;
$ta = 231;
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
    $approverName   = addslashes($row['APPROVER_NAME']);
    $occupation     = $row['OCCUPATION'];
    $approvalStatus = $row['APPROVAL_STATUS_NAME'];
    $approvalDate   = $row['APPROVAL_DATE'];
    
        $pdf->SetFont('Arial','',6);
    $pdf->Rect($xa, 19, 27, 5);
    $pdf->Text($ta, 22, $occupation);
    $pdf->Rect($xa, 19, 27, 16);        //koordinat x,y,w,h
    $pdf->Text($ta, 27, $approvalStatus);
    $pdf->Text($ta, 32, $approvalDate);
    $pdf->Rect($xa, 35, 27, 5);
    $pdf->Text($ta, 38, $approverName);
    $xa=$xa-27;
    $ta=$ta-27;
    }
    

$pdf->Rect(257, 19, 30, 5);
$pdf->Text(258, 22, 'Diminta Oleh');
$pdf->Rect(257, 19, 30, 16);
$pdf->Text(258, 30, '');
$pdf->Rect(257, 35, 30, 5);
$pdf->Text(258, 38, $requester);
$pdf->SetFont('Times', '', 6);
$pdf->Text(210, 42, 'Pembelian di atas Rp 5 Juta / Item harus s/d Direktur incharge & buat adjustment expense.');
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
            ,EPS_T_PR_DETAIL.REMARK_2
            ,EPS_T_PR_DETAIL.RUTIN
            ,EPS_T_PR_DETAIL.SUBTITUSI
            ,EPS_T_PR_DETAIL.PR_CHARGED_BU
            ,EPS_T_PR_HEADER.CHARGED_BU_CD
            ,(select
                COMPANY_CD
              from
                EPS_M_BUNIT
              where      
                BU_CD = EPS_T_PR_HEADER.CHARGED_BU_CD) 
              as COMPANY_CHARGED_BU
            ,EPS_T_PR_DETAIL.ITEM_STATUS as ITEM_STATUS
            ,(select count (*)
              from          
                EPS_T_PR_ATTACHMENT
              where      
                EPS_T_PR_HEADER.PR_NO = EPS_T_PR_ATTACHMENT.PR_NO 
              and 
                EPS_T_PR_DETAIL.ITEM_NAME = EPS_T_PR_ATTACHMENT.ITEM_NAME) as ATTACHMENT_ITEM_COUNT
            ,case 
                when 
                    CHARINDEX('.', ITEM_NAME) - 1 > 0 
                then 
                    case 
                        when 
                            isnumeric(substring(ITEM_NAME, 1, CHARINDEX('.',ITEM_NAME) - 1)) = 1 
                        then 
                            substring(ITEM_NAME, 1, CHARINDEX('.', ITEM_NAME) - 1) 
                        else 
                            999 
                        end 
                else 
                    999 
                end 
            as INDEX_ITEM_NAME
          from 
            EPS_T_PR_DETAIL 
          left join
            EPS_T_PR_HEADER
          on
            EPS_T_PR_DETAIL.PR_NO = EPS_T_PR_HEADER.PR_NO
          left join
            EPS_M_EMPLOYEE 
          on 
            EPS_T_PR_DETAIL.REJECT_ITEM_BY = EPS_M_EMPLOYEE.NPK
          where 
            EPS_T_PR_DETAIL.PR_NO = '".$prNo."'";
if(count($wherePrDetail)) {
    $query .= "and " . implode(' ', $wherePrDetail);
}
$query .= " order by 
                INDEX_ITEM_NAME, EPS_T_PR_DETAIL.ITEM_NAME";
$sql = $conn->query($query);
$no = 0;
while($row = $sql->fetch(PDO::FETCH_ASSOC))
{
	$no++.'.';
        $itemCd         = $row['ITEM_CD'];
        $itemName       = addslashes($row['ITEM_NAME']);
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
        $prChargedCom   = $row['COMPANY_CHARGED_BU'];
        $itemStatus     = $row['ITEM_STATUS'];
        $remark2     = $row['REMARK_2'];
        $rutin     = $row['RUTIN'];
        $subtitusi     = $row['SUBTITUSI'];
        $prChargedItem     = $row['PR_CHARGED_BU'];
        $remark_total = $remark.", ".$remark2;
        $query_select_m_account = "select 
                                    ITEM_TYPE_CD
                                   from
                                    EPS_M_ACCOUNT
                                   where
                                    ACCOUNT_NO = '$accountCd'";
        $sql_select_m_account = $conn->query($query_select_m_account);
        $row_select_m_account = $sql_select_m_account->fetch(PDO::FETCH_ASSOC);
        
        // Set "Charge ke BU/Seksi" column
        if($itemType == '4' && $row_select_m_account['ITEM_TYPE_CD'] == '4'){
            $prCharged = 'N1001';
        }
        if($itemType == '3' && $row_select_m_account['ITEM_TYPE_CD'] == '3' && $prChargedCom == "D"){
            $prCharged = 'N1000';
        }
        // set "No. Item" column
        if($itemType == '1' || $itemType == '3' || $itemType == '4'){
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
            $itemPrice = "(".$itemPrice.")";
            $amount = "(".$amount.")";
            $pdf->SetFont('Arial','B',6);
            $pdf->SetTextColor(231,15,15);
        }else{
            $pdf->SetFont('Arial','',6);
            $pdf->SetTextColor(0,0,0);
        }
        
	$pdf->SetWidths(array(7,16,80,10,10,15,9,9,9,9,7,23,25,25,23));
        $pdf->SetAligns(array('C','L','L','C','C','L','L','R','C','R','C','R','R','L','L'));
	$pdf->Row(array($no,$prNo,$itemName,$rutin,$subtitusi,$deliveryDate,$prCharged,$prChargedItem,$itemType,$qty,$unitCd,$itemPrice,$amount,$supplierName,$remark_total));
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

$pdf->Cell(22,4,'Alasan Membeli:','L',0,'L');
$pdf->Cell(30,4,'1 : Production',0,0,'L');
$pdf->Cell(40,4,'4 : Quality Up',0,0,'L');
$pdf->Cell(60,4,'7 : Building',0,0,'L');
$pdf->Cell(76,4,'* Untuk Pembelian mesin, harus ada pembanding baik merk, type,',0,0,'L');
$pdf->Cell(49,4,'','R',1,'L');

$pdf->Cell(22,4,'','L',0,'L');
$pdf->Cell(30,4,'2 : Repair / Maintenance',0,0,'L');
$pdf->Cell(40,4,'5 : Replacement',0,0,'L');
$pdf->Cell(60,4,'8 : Other (tulis alasan)',0,0,'L');
$pdf->Cell(76,4,'spec, fungsi, harga, supplier dsb',0,0,'C');
$pdf->Cell(49,4,'','R',1,'L');

$pdf->Cell(22,4,'','L',0,'L');
$pdf->Cell(30,4,'3 : Safety',0,0,'L');
$pdf->Cell(40,4,'6 : Packaging',0,0,'L');
$pdf->Cell(60,4,'',0,0,'L');
$pdf->Cell(76,4,'',0,0,'C');
$pdf->Cell(49,4,'','R',1,'L');

$pdf->Cell(204,8,'ALASAN MEMBELI :','LTR',0,'L');
$pdf->SetFont('Times', '', 7);
$pdf->Cell(25,8,'Diterima Oleh/Tgl','LTR',0,'C');
$pdf->Cell(48,8,$procInCharge,'LTR',1,'L');
$pdf->Cell(204,5,$purpose,'LBR',0,'L');
$pdf->Cell(25,5,'','LBR',0,'R');
$pdf->Cell(48,5,$procAcceptDate,'LBR',1,'R');

$pdf->SetFont('Times', 'U', 8);
$pdf->Cell(40,6,'Referensi(Guidance only):',0);
$pdf->Cell(197,6,'',0);
$pdf->Cell(40,6,'PT. TD AUTOMOTIVE COMPRESSOR INDONESIA',0,1,'R');

$pdf->SetFont('Times', '', 6);
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