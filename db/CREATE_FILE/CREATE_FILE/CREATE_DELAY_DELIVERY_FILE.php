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
set_time_limit(4800);
ini_set('max_execution_time', 4800);
ini_set('memory_limit', '512M'); 

//$plantCd	= $_GET['plantCdVal'] ;	
$plantCd	= "7" ;	
$npkProcArray 	= array();

$action         = $_GET['action'];
$mailFrom       = $_SESSION['sinet'];
$mailFromName   = $_SESSION['snotes'];

if($action == "ManualSentDelayDelivery")
{
    $mailTo       = "muh.iqbal@taci.toyota-industries.com";
    
    $mailSubject  = "[EPS] Delay Delivery Running Manually";
    $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
    $mailMessage .= "Dear EPS Administrator,";
    $mailMessage .= "<br><br>Please check EPS server because user already running delay delivery report manually.";
    $mailMessage .= "<br>Thank you";
    $mailMessage .= "</font>";
    
    manualReportMail($mailTo,$mailFrom,$mailFromName,$mailSubject,$mailMessage);
}

if($plantCd == 7)
{
    $plantCdAlias  = "GT";
}

/** 
 * Search EPS_M_PLANT
 */
$query_select_m_plant = "select
                            PLANT_NAME
                         from
                            EPS_M_PLANT
                         where
                            PLANT_CD = '$plantCd'";
$sql_select_m_plant = $conn->query($query_select_m_plant);
while($row_select_m_plant = $sql_select_m_plant->fetch(PDO::FETCH_ASSOC)){	
    $plantName  = $row_select_m_plant['PLANT_NAME'];
}
/**
 * Search EPS_M_PR_PROC_APPROVER
 */
$query_select_m_pr_proc_app = "select
				distinct NPK
                               from
				EPS_M_PR_PROC_APPROVER
                               where
				PLANT_CD = '$plantCd'";
//echo $query_select_m_pr_proc_app;
$sql_select_m_pr_proc_app = $conn->query($query_select_m_pr_proc_app);
while($row_select_m_pr_proc_app = $sql_select_m_pr_proc_app->fetch(PDO::FETCH_ASSOC)){	
	$npkProc = $row_select_m_pr_proc_app['NPK'];
	$npkProcArray[] = array(
                        'npkProc' => $npkProc
                      );
    $addNpkProcArray = $npkProcArray;
    
    
}
$indexArray = 1;
foreach($addNpkProcArray as $addNpkProcArrays)
{
    $npkProcVal = $addNpkProcArrays['npkProc'];
	if($indexArray == 1)
	{
		$npkProcCriteria = "'".$npkProcVal."'";
	}
	else
	{
		$npkProcCriteria = $npkProcCriteria.",'".$npkProcVal."'";
	}
	$indexArray ++;
}
/** 
 * SELECT EPS_T_PO_HEADER
 */  
$query_select_t_po_header_groupby_supplier = "
        select     
            distinct EPS_T_PO_HEADER.SUPPLIER_CD, EPS_M_SUPPLIER.SUPPLIER_NAME
            
        from         
            EPS_T_PO_HEADER
			INNER JOIN EPS_M_SUPPLIER ON EPS_T_PO_HEADER.SUPPLIER_CD=EPS_M_SUPPLIER.SUPPLIER_CD
        where     
            (PO_STATUS = '1250')
            --and (EPS_T_PO_HEADER.DELIVERY_DATE < convert(char(10), GETDATE(), 112))
        group by 
            EPS_T_PO_HEADER.SUPPLIER_CD, EPS_M_SUPPLIER.SUPPLIER_NAME
            order by EPS_T_PO_HEADER.SUPPLIER_CD desc";
$sql_select_t_po_header_groupby_supplier = $conn->query($query_select_t_po_header_groupby_supplier);
while($row_select_t_po_header_groupby_supplier = $sql_select_t_po_header_groupby_supplier->fetch(PDO::FETCH_ASSOC)){
    $itemDelayDelivery      = array();
    $addItemDelayDelivery   = array();
    
    $itemNo             = 0;
    $supplierCd         = $row_select_t_po_header_groupby_supplier['SUPPLIER_CD'];
    $supplierName       = $row_select_t_po_header_groupby_supplier['SUPPLIER_NAME'];
    $query_select_t_po_header = "
        select     
            EPS_T_TRANSFER.PR_NO
            ,EPS_T_PO_DETAIL.PO_NO
            ,EPS_T_PO_HEADER.SUPPLIER_NAME
            ,substring(EPS_T_PO_HEADER.DELIVERY_DATE, 7, 2)
            + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 5, 2) 
            + '/' + substring(EPS_T_PO_HEADER.DELIVERY_DATE, 1, 4) as DELIVERY_DATE
            ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 120) as SEND_PO_DATE
            ,EPS_T_PO_DETAIL.ITEM_CD
            ,EPS_T_PO_DETAIL.ITEM_NAME
            ,EPS_T_PO_DETAIL.QTY
            ,EPS_T_PO_DETAIL.UNIT_CD
            ,EPS_T_PO_HEADER.CURRENCY_CD
            ,EPS_T_PO_DETAIL.ITEM_PRICE
            ,isnull(
                (select 
                    sum(TRANSACTION_QTY)
                 from 
                    EPS_T_RO_DETAIL
                 where   
                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A')
              ,0)
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
              ,0)
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
              ,0)
            as TOTAL_OPENED_QTY
            ,EPS_T_TRANSFER.NEW_CHARGED_BU
            ,EPS_T_TRANSFER.NEW_ITEM_TYPE_CD
            ,EPS_T_TRANSFER.NEW_ACCOUNT_NO
            ,EPS_T_TRANSFER.NEW_RFI_NO
            ,EPS_M_BUNIT.BU_NAME
            ,datediff
                (day, EPS_T_PO_HEADER.DELIVERY_DATE, convert(char(10), GETDATE(), 112)) 
             as COUNT_DATE_DIFF
            ,EPS_T_PO_HEADER.DELIVERY_PLANT
        from         
            EPS_T_PO_DETAIL 
        left join
            EPS_T_PO_HEADER 
        on 
            EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
        left join
            EPS_T_TRANSFER
        on
            EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
        left join
            EPS_M_BUNIT
        on
            EPS_T_TRANSFER.NEW_CHARGED_BU = EPS_M_BUNIT.BU_CD
        where     
            EPS_T_PO_HEADER.PO_STATUS = '1250' 
            and EPS_T_PO_DETAIL.RO_STATUS != '1320'
            and EPS_T_PO_HEADER.SUPPLIER_CD = '$supplierCd'
            and EPS_T_PO_HEADER.ISSUED_BY IN ($npkProcCriteria)
            and EPS_T_PO_HEADER.CURRENCY_CD = 'IDR'
            and case 
                    when(substring(EPS_T_TRANSFER.PR_NO, 1, 1)) = 'T' 
                       then (substring(EPS_T_TRANSFER.PR_NO, 1, 5)) 
                    else (substring(EPS_T_TRANSFER.PR_NO, 1, 4)) 
                end
                in (select     
                    BU_CD
                from
                    EPS_M_PR_PROC_APPROVER
                where      
                    PLANT_ALIAS = '$plantCdAlias')
        order by 
            convert(DATETIME, DELIVERY_DATE, 103)
            ,EPS_T_PO_HEADER.PO_NO";
    
    //echo $query_select_t_po_header;
    
    $sql_select_t_po_header = $conn->query($query_select_t_po_header);
    while($row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC)){
        $poNoVal            = $row_select_t_po_header['PO_NO'];
        $deliveryDateVal    = $row_select_t_po_header['DELIVERY_DATE'];
        $sendPoDate         = $row_select_t_po_header['SEND_PO_DATE'];
        $itemNameVal        = $row_select_t_po_header['ITEM_NAME'];
        $qtyVal             = $row_select_t_po_header['QTY'];
        $totalReceivedQtyVal= $row_select_t_po_header['TOTAL_RECEIVED_QTY'];
        $totalCanceledQtyVal= $row_select_t_po_header['TOTAL_CANCELED_QTY'];
        $totalOpenedQtyVal  = $row_select_t_po_header['TOTAL_OPENED_QTY'];
        $totalOpenQty       = ($qtyVal - $totalReceivedQtyVal) + $totalCanceledQtyVal + $totalOpenedQtyVal;
        $deliveryPlantVal   = $row_select_t_po_header['DELIVERY_PLANT'];
        $dateDiff           = $row_select_t_po_header['COUNT_DATE_DIFF'];
        
        if($deliveryPlantVal == 'GT'){
            $deliveryPlantVal = 'TACI Plant';
        }
        
        $itemNo++;
        $itemDelayDelivery[] = array(
                                    'itemNo'        => $itemNo
                                    ,'poNo'         => $poNoVal
                                    ,'deliveryDate' => $deliveryDateVal
                                    ,'sendPoDate'   => $sendPoDate
                                    ,'itemName'     => $itemNameVal
                                    ,'totalOpenQty' => $totalOpenQty
                                    ,'dateDiff'     => $dateDiff
                                );
        $addItemDelayDelivery = $itemDelayDelivery;
    }
    if(count($addItemDelayDelivery) > 0)
    {
       /**********************************************************************
        * SEND MAIL
        **********************************************************************/
        $mailFrom       = "ahmadjafar@taci.toyota-industries.com";
        $mailFromName   = "EPS ADMINISTRATOR/TACI";  
        //$mailCc         = "WIHARYO@taci.toyota-industries.com,ahmadjafar@taci.toyota-industries.com,ISON@taci.toyota-industries.com,ASEPSETIAWAN@taci.toyota-industries.com, muh.iqbal@taci.toyota-industries.com";
        $mailCc         = "ahmadjafar@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com, andry@taci.toyota-industries.com, WIHARYO@taci.toyota-industries.com, idapian@taci.toyota-industries.com,maintenance.sparepart2@taci.toyota-industries.com, maintenance.sparepart1@taci.toyota-industries.com";

        $query_select_m_supplier = "select
                                        EMAIL
                                        ,EMAIL_CC
                                        ,EMAIL_CC_UP
                                        ,CURRENCY_CD
                                    from
                                        EPS_M_SUPPLIER
                                    where
                                        SUPPLIER_CD = '$supplierCd' ";
        $sql_select_m_supplier = $conn->query($query_select_m_supplier);
        $row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
        $supplierEmail      = $row_select_m_supplier['EMAIL'];
        $supplierEmailCc    = $row_select_m_supplier['EMAIL_CC'];
        $supplierEmailCcUp  = $row_select_m_supplier['EMAIL_CC_UP'];
        $currencyCd         = $row_select_m_supplier['CURRENCY_CD'];
		
        $mailTo = trim($supplierEmail);
		if($mailTo == '')
		{
			$mailTo = "ahmadjafar@taci.toyota-industries.com";
		}
        
        //$mailTo = "karyoto@taci.toyota-industries.com";
		
        $mailSubject  = "** [EPS] OUTSTANDING DELIVERY SUPPLIER: ".$supplierName." (".trim($plantName).")";
        $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
        if($currencyCd == 'IDR')
        {
            $mailMessage .= "Yth. Bapak/Ibu Supplier PT. TD Automotive Compressor Indonesia";
            $mailMessage .= "<br><br>Berikut ini adalah informasi mengenai Outstanding Delivery yang terbentuk secara otomatis dari sistem PT. TD Automotive Compressor Indonesia:";

        }
        else
        {
            $mailMessage .= "Dear Sir or Madam,";
            $mailMessage .= "<br><br>Herewith we would like to send you information about Outstanding DELIVERY that sent automatically by PT. TD Automotive Compressor Indonesia system:";
        }
        $mailMessage .= "<br><table style='font-family: Arial; font-size: 12px; border: 1px solid #000000; boder=1 ;border: 1px solid #000000;'>";
        $mailMessage .= "<tr style='font-weight: bold; text-align: center;'>
                            <td width= 30px>No.</td>
                            <td width= 75px>PO No.</td>
                            <td width= 125px>Sent PO Date</td>
                            <td width= 85px>Due Date</td>
                            <td width= 450px>Item Name</td>
                            <td width= 80px>Open Qty</td>
                            <td width= 120px>Outstanding Outflag (days)</td>
                        </tr>";
        $countMailCc = 0;
        $countMailCcUp = 0;
        foreach($addItemDelayDelivery as $addItemDelayDeliverys)
        {
            $itemNoArray        = trim($addItemDelayDeliverys['itemNo']);
            $poNoArray          = strtoupper(trim($addItemDelayDeliverys['poNo']));
            $deliveryDateArray  = strtoupper(trim($addItemDelayDeliverys['deliveryDate']));
            $sendPoDateArray    = strtoupper(trim($addItemDelayDeliverys['sendPoDate']));
            $itemNameArray      = strtoupper(trim($addItemDelayDeliverys['itemName']));
            $openQtyArray       = trim($addItemDelayDeliverys['totalOpenQty']);
            $dateDiffArray      = trim($addItemDelayDeliverys['dateDiff']);
			
			if($dateDiffArray < 1)
            {
                $outFlag = '';
            }
            else if($dateDiffArray < 8)
            {
                $outFlag = '*';
            }
            else if($dateDiffArray < 15)
            {
                $outFlag = '**';
                $countMailCc++;
            } 
            else if($dateDiffArray < 22)
            {
                $outFlag = '***';
                $countMailCcUp++;
            }
            else
            {
                $outFlag = '****';
            }
			
            if($dateDiffArray <= 0)
            {
                $dateDiffArray = '';
            }
            else
            {
                $dateDiffArray = '> '.$dateDiffArray;
            }
            $mailMessage .= "<tr>
                                <td>".$itemNoArray."</td>
                                <td>".$poNoArray."</td>
                                <td>".$sendPoDateArray."</td>
                                <td>".$deliveryDateArray."</td>
                                <td>".$itemNameArray."</td>
                                <td>".$openQtyArray."</td>
                                <td>".$outFlag." (".$dateDiffArray.") "."</td>
                            </tr>";

        }
        $mailMessage .= "</table>";

		if($countMailCc > 0 && trim($supplierEmailCc) != "")
        {
            $mailTo = $mailTo.",".$supplierEmailCc;
        }
        if($countMailCcUp > 0 && trim($supplierEmailCcUp) != "")
        {
            $mailTo = $mailTo.",".$supplierEmailCcUp;
        }
		
        if($currencyCd == 'IDR')
        {
            $mailMessage .= "<br>Harap memberikan konfirmasi kepada PIC Procurement yang bersangkutan mengenai hal ini. ";
            $mailMessage .= "<br>Kami tunggu konfirmasinya hari ini untuk kepastian kedatangan barang tersebut.";
            $mailMessage .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
            $mailMessage .= "<br><br>Hormat kami,";
            $mailMessage .= "<br><br>Procurement Dept. | General Supplies";
            $mailMessage .= "<br>PT. TD Automotive Compressor Indonesia";
            $mailMessage .= "<br><br>(TACI Plant) Bayu | e-mail: bayu.thr@taci.toyota-industries.com | Phone: (+62)21 2851 7699 ext. 301";
        }
        else
        {
            $mailMessage .= "<br>Kindly confirm to Procurement who handled about this matter today.";
            $mailMessage .= "<br><br>Thank you for your attention and cooperation.";
            $mailMessage .= "<br><br>Best regards,";
            $mailMessage .= "<br><br>Procurement Dept. | General Supplies";
            $mailMessage .= "<br>PT. TD Automotive Compressor Indonesia";
            $mailMessage .= "<br><br>(TACI Plant) Mr. Bayu | e-mail: bayu.thr@taci.toyota-industries.com | Phone: (+62)21 2851 7699 ext. 301";
            
        }
        $mailMessage .= "<br><br></font>";

        //echo $supplierCd." ".$mailTo."<br>";
        delayDeliverySendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc);
    }
    
}

?>
