<?

require('../fpdf.php');

define('DB_HOST','10.82.101.27');
define('DB_NAME','eProcurement');
define('DB_USER','sa');
define('DB_PASS','t4c1@BKS');
$conn = new PDO('mssql:host=172.31.1.248;dbname=eProcurement','sa','P@ssw0rD');
if(!$conn) die('Could not connect to DB'); 

$nopp=$_GET['nopp'];

$qc=$conn->query("select count(*) as jumlah from PPdetail where nopp='".$nopp."'");
$jr = $qc->fetch(PDO::FETCH_ASSOC);
$jml=$jr['jumlah']; 

/**
 * Query for PP Information
 * **/
$query = "SELECT PP.nopp,Employee.PERSH, Persh.NMPER, Employee.PLANT, PLANT.NMPLN, PP.npk, 
                Employee.NAMA1,  LEN(Employee.NAMA1) AS lnama, PP.kdBU, PP.kategori, 
                PP.skminta, PP.skpn, 
                SUBSTRING(PP.tglpp, 7, 2) + '/' + SUBSTRING(PP.tglpp, 5, 2) + '/' + SUBSTRING(PP.tglpp, 1, 4) AS tglpp
            FROM PP INNER JOIN
                Employee ON LTRIM(PP.npk) = LTRIM(Employee.NPK) AND PP.kdBU = Employee.LKDP INNER JOIN
                Persh ON Employee.PERSH = Persh.KDPER INNER JOIN
                PLANT ON Employee.PLANT = PLANT.KDPLN INNER JOIN
                TBUNIT ON PP.skpn = TBUNIT.KDBU
            WHERE PP.nopp='".$nopp."'";
$sql = $conn->query($query);
$arr=$sql->fetch(PDO::FETCH_ASSOC);

$tglpp=$arr['tglpp'];
$nama=$arr['NAMA1'];
$lnama=$arr['lnama'];
$kdbu=$arr['kdBU'];
$skpn=$arr['skpn'];
$npk=$arr['npk'];
$nmpersh=$arr['NMPER'];
$nmpln=$arr['NMPLN'];
$kategori=$arr['kategori'];
if($kategori=='c'){
    $kategori='Computer';
}else{
    $kategori='Not Computer';
}

$qskm=$conn->query("SELECT nmbu1 FROM TBUNIT WHERE kdbu='$kdbu'");
$arm=$qskm->fetch(PDO::FETCH_ASSOC);
$nmskminta=$arm['nmbu1'];

class PDF extends FPDF
{
    
}
$pdf=new FPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,5,'PERMINTAAN PEMBELIAN',0,1,'C');
$pdf->Ln(2);
$pdf->SetFont('Arial','B',7);
/*$pdf->Cell(0,5,'Nomer PP: '.$nopp,0,1);
$pdf->Cell(0,5,'Tanggal Buat PP: '.$tglpp,0,1);
$pdf->Cell(0,5,'Ditulis Oleh: '.$nama,0,1);
$pdf->Cell(0,5,'BU Code: '.$kdbu,0,1);
$pdf->Cell(0,5,'Category: '.$kategori,0,1);*/
$image='denso.jpg';
$pdf->Cell( 40, 40, $pdf->Image($image, 5, 10, 33.78), 0, 0, 'L', false );

$pdf->Text(5,20,'Nomer PP');
$pdf->Text(5,25,'Tanggal Buat PP');
$pdf->Text(5,30,'Ditulis Oleh');
$pdf->Text(5,35,'Dept./BU Code');
$pdf->Text(5,40,'Category');
    
$pdf->Text(35,20,': '.$nopp);
$pdf->Text(35,25,': '.$tglpp);
$pdf->Text(35,30,': '.$nama);
$pdf->Text(35,35,': '.trim($nmskminta).' / '.$kdbu);
$pdf->Text(35,40,': '.$kategori);

$pdf->SetFont('Arial','',8);
/**
 * Create Table for Approver PP in pdf
 * **/
//Fields Name position
$Y_Fields_Approver_position = 10;
//Table position, under Fields Name
$Y_Approver_Position = 30;

//First create each Field Name
//Gray color filling each Field Name box
$pdf->SetFillColor(255, 255, 255);
//Bold Font for Field Name
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetY($Y_Fields_Approver_position);

/*$pdf->SetX(125);
$pdf->Cell(20, 6, 'Direktur', 1, 0, 'C', 1);
$pdf->SetX(145);
$pdf->Cell(20, 6, 'GM/AGM', 1, 0, 'C', 1);
$pdf->SetX(165);
$pdf->Cell(20, 6, 'DM/SM/ASM', 1, 0, 'C', 1);
$pdf->SetX(185);
$pdf->Cell(20, 6, 'Diminta', 1, 0, 'C', 1);

$pdf->SetY(15);
$pdf->SetX(125);
$pdf->Cell(20, 15, ' ', 1, 0, 'C', 1);
$pdf->SetX(145);
$pdf->Cell(20, 15, ' ', 1, 0, 'C', 1);
$pdf->SetX(165);
$pdf->Cell(20, 15, ' ', 1, 0, 'C', 1);
$pdf->SetX(185);
$pdf->Cell(20, 15, ' ', 1, 0, 'C', 1);*/

$xatab=125;

$qa="SELECT Employee.NAMA1 AS NmApp, LEN(Employee.NAMA1) AS lnmapp, Employee.JABAT, JABATAN.ASJBT
        FROM PPApprover INNER JOIN
        Employee ON PPApprover.NPKapp = Employee.NPK INNER JOIN
        JABATAN ON Employee.JABAT = JABATAN.KDJBT
        WHERE nopp='$nopp' ORDER BY PPApprover.AppNo ASC";
$sqa=$conn->query($qa);
$rectapp=223;
$numapp=225;
while($jra = $sqa->fetch(PDO::FETCH_ASSOC)){
    $nmapp=$jra['NmApp'];
    $jbtapp=$jra['ASJBT'];
    $lnmapp=$jra['lnmapp'];
    $pdf->SetFont('Arial', 'B', 6);
    if($jbtapp=='DIR.'){
        $jbtapp='DIREKTUR';
    }
    /*$pdf->SetY($Y_Approver_Position);
    $pdf->SetX($xatab);
    $pdf->MultiCell(20, 5, $nmapp, 1,'C');
    $xatab=$xatab+20;*/
    
        
    $pdf->Rect($rectapp, 17, 30, 5);
    $pdf->Text($numapp, 20, $jbtapp);
    
    $pdf->Rect($rectapp, 22, 30, 15);
    
    $pdf->Rect($rectapp, 37, 30, 5);
    $pdf->Text($numapp, 40, $nmapp);
        
    $rectapp=$rectapp-30;
    $numapp=$numapp-30;
}

$pdf->Rect(253, 17, 33, 5);
$pdf->Text(255, 20, 'Diminta');
$pdf->Rect(253, 22, 33, 15);
$pdf->Text(255, 40, $nama);
$pdf->Rect(253, 37, 33, 5);

/*$pdf->SetFont('Arial', 'B', 6);
$pdf->SetY($Y_Approver_Position);
$pdf->SetX(185);
$pdf->MultiCell(20, 5, $nama, 1,'C');*/

/**
 * Create Table for Item PP in pdf
 * **/

//Fields Name position
$Y_Fields_Name_position = 50;
//Table position, under Fields Name
$Y_Table_Position = 58;

//First create each Field Name
//Gray color filling each Field Name box
$pdf->SetFillColor(232, 232, 232);
//Bold Font for Field Name
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetY($Y_Fields_Name_position);

$pdf->SetX(5);
$pdf->Cell(5, 10, 'No.', 1, 0, 'C', 1);

$pdf->SetX(10);
$pdf->Cell(85, 10, 'Nama Barang (Part No, Spec, Ukuran/Dimensi, Warna, Merk)', 1, 0, 'C', 1);

$pdf->SetX(95);
$pdf->Cell(20, 10, 'Remark', 1, 0, 'C', 1);

$pdf->SetX(115);
$pdf->Cell(15, 10, 'Rutin/Tidak', 1, 0, 'C', 1);

$pdf->SetX(130);
$pdf->Cell(15, 10, 'Tgl Kirim', 1, 0, 'C', 1);

$pdf->SetX(145);
$pdf->Cell(12, 10, 'Charge', 1, 0, 'C', 1);

$pdf->SetX(157);
$pdf->Cell(12, 10, 'No.Item', 1, 0, 'C', 1);

$pdf->SetX(169);
$pdf->Cell(10, 10, 'Qty', 1, 0, 'C', 1);

$pdf->SetX(179);
$pdf->Cell(10, 10, 'U/M', 1, 0, 'C', 1);

$pdf->SetX(189);
$pdf->Cell(16, 10, 'Unit Prices', 1, 0, 'C', 1);

$pdf->SetX(205);
$pdf->Cell(45, 10, 'Supplier', 1, 0, 'C', 1);

$pdf->SetX(250);
$pdf->Cell(23, 10, 'Amount', 1, 0, 'C', 1);

$pdf->Ln();

$query2="SELECT PPdetail.kdbrg, PPdetail.nmbrg, Barang.Type, PPdetail.remark, SUBSTRING(PPdetail.tglkirim, 7, 2) + '/' + SUBSTRING(PPdetail.tglkirim, 5, 2) 
            + '/' + SUBSTRING(PPdetail.tglkirim, 1, 4) AS tglkirim, PPdetail.qty, PPdetail.harga, PPdetail.amount, PPdetail.jenisinvest, PPdetail.investno, PPdetail.kditem, 
            PPdetail.kdUM, PPdetail.nmsup
        FROM PPdetail 
        LEFT OUTER JOIN Barang ON PPdetail.kdbrg = Barang.KdBrg         
        WHERE nopp='".$nopp."'";
$sql2=$conn->query($query2);

$i=1;
$num=63;
$rect=60;

while($arr2=$sql2->fetch(PDO::FETCH_ASSOC)){
    $pdf->SetFont('Arial','',8);
    $kdbrg=$arr2['kdbrg'];
    $nmbrg=$arr2['nmbrg'];
    $type=$arr2['Type'];
    $remark=$arr2['remark'];
    $tglkirim=$arr2['tglkirim'];
    $jenisinvest=$arr2['jenisinvest'];
    $investno=$arr2['investno'];
    $kditem=$arr2['kditem'];
    $qty=$arr2['qty'];
    $kdum=$arr2['kdUM'];
    $harga=$arr2['harga'];
    $nmsup=$arr2['nmsup'];
    $amount=$arr2['amount'];
    
    //Cek Type
    if($type=='R'){
        $type='Rutin';
    }else{
        $type='Tidak';
    }
    //Cek Kode Item
    $qi="SELECT Expense from Item WHERE Code='$kditem'";
    $sqi=$conn->query($qi);
    $jr = $sqi->fetch(PDO::FETCH_ASSOC);
    $exp=$jr['Expense'];
    
    //Cek Jenis Invest/Tidak
    if($jenisinvest=='t'){
        $jenisinvest='Tidak';
        //$kditem=$exp;
    }else{
        $jenisinvest='Ya';
        $kditem='RFI-'.$investno;
    }
    $qty = number_format($qty);  
    $harga = number_format($harga);    
    $amount = number_format($amount);
            
    //Now show the columns
    $pdf->SetFont('Arial', '', 6);
    
    $pdf->SetY($Y_Table_Position);
    $pdf->SetX(5);
        
    $pdf->Rect(5, $rect, 5, 5);
    $pdf->Text(6, $num, $i.'.');
    
    $pdf->Rect(10, $rect, 85, 5);
    $pdf->Text(12, $num, $nmbrg);
    
    $pdf->Rect(95, $rect, 20, 5); 
    $pdf->Text(97, $num, $remark);
    
    $pdf->Rect(115, $rect, 15, 5); 
    $pdf->Text(117, $num, $type);
    
    $pdf->Rect(130, $rect, 15, 5); 
    $pdf->Text(132, $num, $tglkirim);
    
    $pdf->Rect(145, $rect, 12, 5);
    $pdf->Text(147, $num, $skpn);
   
    $pdf->Rect(157, $rect, 12, 5);
    $pdf->Text(159, $num, $kditem);
    
    
    $pdf->Rect(169, $rect, 10, 5);
    $pdf->Text(171, $num, $qty);
    
    $pdf->Rect(179, $rect, 10, 5);
    $pdf->Text(181, $num, $kdum);
    
    $pdf->Rect(189, $rect, 16, 5);
    $pdf->Text(191, $num, $harga);
    
    $pdf->Rect(205, $rect, 45, 5);
    $pdf->Text(207, $num, $nmsup);
    
    $pdf->Rect(250, $rect, 23, 5);
    $pdf->Text(252, $num, $amount);
    
    $rect=$rect+5;
    $num=$num+5;
    $i++;
}

$qs="SELECT SUM(amount) AS total FROM PPdetail WHERE (nopp = '$nopp')";
$sqs=$conn->query($qs);
$ars=$sqs->fetch(PDO::FETCH_ASSOC);
$total=$ars['total'];
$total=  number_format($total);
$pdf->Rect(5, $rect+6, 200, 14);
$pdf->Text(15, $rect+4, '* Untuk pembelian mesin,harus ada pembanding. Baik merk, type, spec, fungsi, harga, supplier, dsb');
$pdf->Rect(205, $rect+6, 23, 14);
$pdf->Text(207, $rect+14, 'Diterima Oleh/Tgl');
$pdf->Rect(228, $rect+6, 45, 14);

$pdf->Rect(5, $rect, 200, 6);
$pdf->SetFont('Arial','B',9);
$pdf->Rect(205, $rect, 45, 6);
$pdf->Text(207, $num+1, 'Total');
$pdf->Rect(250, $rect, 23, 6);
$pdf->Text(252, $num+1, $total);
$pdf->Text(6, $num+7, 'CATATAN :');

$pdf->Output();
?>

