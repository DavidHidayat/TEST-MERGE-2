<?php session_start(); 
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/db/Email/PO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/EPS/lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/EPS/lib/mail_lib/class.smtp.php';

$conn = new PDO('mssql:host=10.82.101.27;dbname=OMS','sa','t4c1@BKS');
if(!$conn) die('Could not connect to DB'); 

$action = $_GET['action'];

if($action == "POSummaryDayBefore")
{
   /**
    * Search EPS_M_PR_PROC_APPROVER
    */
    $query_select_t_po_header_bysupplier = "select
                                                SUPPLIER_CD
                                            from
                                                EPS_T_PO_HEADER
                                            where
                                                (PO_STATUS = '1250') 
                                                and (DATEDIFF(day, SEND_PO_DATE, GETDATE()) = 1)
                                                and CURRENCY_CD = 'IDR'
                                            group by
                                                SUPPLIER_CD";
    $sql_select_t_po_header_bysupplier = $conn->query($query_select_t_po_header_bysupplier);
    while($row_select_t_po_header_bysupplier = $sql_select_t_po_header_bysupplier->fetch(PDO::FETCH_ASSOC))
    {	
        $supplierCd     = $row_select_t_po_header_bysupplier['SUPPLIER_CD'];
        
        $poNoArray 	= array();
        $x = 0;
        
        $query_select_t_po_header = "select
                                        PO_NO
                                        ,convert(VARCHAR(24), EPS_T_PO_HEADER.SEND_PO_DATE, 103) as SEND_PO_DATE
                                        ,SUPPLIER_NAME
                                     from
                                        EPS_T_PO_HEADER
                                     where
                                        SUPPLIER_CD = '$supplierCd'
                                        and (PO_STATUS = '1250') 
                                        and (DATEDIFF(day, SEND_PO_DATE, GETDATE()) = 1)
                                        and CURRENCY_CD = 'IDR' ";
        $sql_select_t_po_header = $conn->query($query_select_t_po_header);
        while($row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC))
        {
            $poNo           = $row_select_t_po_header['PO_NO'];
            $sendPoDate     = $row_select_t_po_header['SEND_PO_DATE'];
            $supplierName   = $row_select_t_po_header['SUPPLIER_NAME'];
            if($x == 0)
            {
                $poNoArray = "- ".$poNo;
            }
            else
            {
                $poNoArray = $poNoArray."<br>- ".$poNo;
            }
            $x++;
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
			$supplierMail = "muh.iqbal@taci.toyota-industries.com";
		}    
		
		/**********************************************************************
		 * SEND MAIL
		 **********************************************************************/
		$mailFrom       = "bayu.thr@taci.toyota-industries.com";
		$mailFromName   = "EPS ADMINISTRATOR/DNIA";  
		
		$mailCc         = "bayu.thr@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com,muh.iqbal@taci.toyota-industries.com, ahmadjafar@taci.toyota-industries.com";
		
        $mailTo     	= $supplierMail;
        
        $mailSubject  = "** [EPS] PO SUMMARY - $supplierName";
        $mailMessage  = "<font face='Trebuchet MS' size='-1'>";
        $mailMessage .= "Yth. Bapak/Ibu Supplier TACI";
        $mailMessage .= "<br><br>Berikut ini adalah PO yang kami kirim pada tanggal $sendPoDate:";
        $mailMessage .= "<br>".$poNoArray;
        $mailMessage .= "<br><br>Mohon konfirmasinya apakah PO tersebut sudah diterima atau belum.";
        $mailMessage .= "<br><br>Terima kasih atas perhatian dan kerjasamanya.";
        $mailMessage .= "<br><br>Hormat kami,";
        $mailMessage .= "<br><br>Procurement Dept. | General Supplies";
        $mailMessage .= "<br>PT. TD Automotive Compressor Indonesia";
        $mailMessage .= "<br><br>(+62 21) 28517699 ext. 301 / 310";
        $mailMessage .= "<br><br>";
        $mailMessage .= "</font>";
        
        delayDeliverySendMailToSupplier($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCc);
    }
}
   
?>
