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

if(isset($_SESSION['sUserId']))
{      
    $sUserId    = $_SESSION['sUserId'];
    
    if($sUserId != '')
    {
        $sNPK       = $_SESSION['sNPK'];
        $sNama      = $_SESSION['sNama'];
        $sBunit     = $_SESSION['sBunit'];
        $sSeksi     = $_SESSION['sSeksi'];
        $sKdper     = $_SESSION['sKdper'];
        $sNmPer     = $_SESSION['sNmper'];
        $sKdPlant   = $_SESSION['sKDPL'];
        $sNmPlant   = $_SESSION['sNMPL'];
        $sRoleId    = $_SESSION['sRoleId'];
        $sInet      = $_SESSION['sinet'];
        $sNotes     = $_SESSION['snotes'];
        $sBuLogin   = $_SESSION['sBuLogin'];
        $sUserType  = $_SESSION['sUserType'];
        $action     = $_GET['action'];
        
        if($action == 'ProcessCn')
        {
            
            $currentMonthClosing    = $_GET['cnDatePrm'];
            $msg = "";
			
            if($currentMonthClosing != '')
            {
				/**
                 * SELECT EPS_T_CN_PERIOD
                 */
                $periodYear     = substr($currentMonthClosing,0,4);
                $periodMonth    = substr($currentMonthClosing,4,2);
                
                $query_select_t_cn_period = "select 
                                                CN_RUNNING_YEAR
                                                ,CN_RUNNING_MONTH
                                             from
                                                EPS_T_CN_PERIOD
                                             where
                                                CN_RUNNING_YEAR = '$periodYear'
                                                and CN_RUNNING_MONTH = '$periodMonth'";
                $sql_select_t_cn_period = $conn->query($query_select_t_cn_period);
                $row_select_t_cn_period = $sql_select_t_cn_period->fetch(PDO::FETCH_ASSOC);
				
				if(!$row_select_t_cn_period)
                {
					/**
                     * INSERT INTO EPS_T_CN_PERIOD
                     */
                    $query_insert_t_cn_period = "insert into
                                                    EPS_T_CN_PERIOD
                                                    (
                                                        CN_RUNNING_YEAR
                                                        ,CN_RUNNING_MONTH
                                                        ,CREATE_DATE
                                                        ,CREATE_BY
                                                        ,UPDATE_DATE
                                                        ,UPDATE_BY
                                                    )
                                                 values
                                                    (
                                                        '$periodYear'
                                                        ,'$periodMonth'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                        ,convert(VARCHAR(24), GETDATE(), 120)
                                                        ,'$sUserId'
                                                    )";
                    $conn->query($query_insert_t_cn_period);
					
					/*********************************************************
					 * SEND MAIL _ START
					 *********************************************************/
					$mailFrom       = "muh.iqbal@taci.toyota-industries.com";
					$mailFromName   = "EPS ADMINISTRATOR/TACI";  

					$procMailCriteria = array();
					$j = 0;
					$query_select_m_user = "select
												EPS_M_USER.NPK
												,EPS_M_DSCID.INETML
												,EPS_M_DSCID.INMAIL
											from
												EPS_M_USER
											inner join
												EPS_M_DSCID 
											on 
												EPS_M_USER.NPK = EPS_M_DSCID.INOPOK
											where
												(ROLE_ID in ('ROLE_02', 'ROLE_04', 'ROLE_05', 'ROLE_06', 'ROLE_07', 'ROLE_09')) 
												and (BU_CD = 'T4100 ')
												and (EPS_M_USER.NPK != '2151669')
												or (EPS_M_USER.NPK = '2120155')
											group by
												EPS_M_USER.NPK
												,EPS_M_DSCID.INETML
												,EPS_M_DSCID.INMAIL ";
					$sql_select_m_user = $conn->query($query_select_m_user);
					while($row_select_m_user = $sql_select_m_user->fetch(PDO::FETCH_ASSOC))
					{
						$procEmail = trim($row_select_m_user['INETML']);
						if($j == 0)
						{
							$procMailCriteria = $procEmail;
						}
						else
						{
							$procMailCriteria = $procMailCriteria.",".$procEmail;
						}
						$j++;
					}
					$mailTo         = $procMailCriteria;
					$mailSubject  = "[EPS] Monthly Closing ".$currentMonthClosing." (START)";
					$mailMessage  = "<font face='Trebuchet MS' size='-1'>";
					$mailMessage .= "Dear General Supplies Team,";
					$mailMessage .= "<br><br>Saat ini proses closing sudah dimulai. Berikut ini informasi mengenai EPS - Monthly Closing:";
					$mailMessage .= "<br>1. Periode Closing adalah $currentMonthClosing";
                    $mailMessage .= "<br>2. Proses closing dijalankan oleh Pak Wiharyo (as In Charge).";
                    $mailMessage .= "<br>3. Dibutuhkan waktu sekitar 20 - 30 menit untuk menyelesaikan proses closing.";
                    $mailMessage .= "<br>4. Tidak diperbolehkan untuk input Receiving PO agar tidak mengganggu data yang ada.";
                    $mailMessage .= "<br>5. Tidak diperbolehkan untuk meng-open-kan PO (status : PO Closed) agar tidak mengganggu data yang ada.";
					$mailMessage .= "<br><br>Thanks,";
					$mailMessage .= "<br>EPS Administrator";
					$mailMessage .= "</font>";
					cnProcessSendMail ($mailTo, $mailFrom, $mailFromName, $mailSubject, $mailMessage);
					
					$conn_as 	= odbc_connect('epstaci','ITODBC','ITODBC');
					set_time_limit(1800);
					$currenctYear   = date(Y);
					$x              = substr($currenctYear,0,2);

					if($x == 20)
					{
						$x = 1;
					}
					$currentYearTwoDigits = date(y);
					$dayNumber          = date("z") + 1; 
					$invoiceDate        = $x.$currentYearTwoDigits.$dayNumber;
					$batchNo            = date(Ymd);
					$mDate              = date(Ymd);
					$cnTransactionNo    = 0;
					$cnDtlNo            = 0;
					$countErrorAs400	= 0;

					$query_select_t_po_hdr_groupby_supplier = "select
																EPS_T_PO_HEADER.SUPPLIER_CD
																,EPS_T_PO_HEADER.SUPPLIER_NAME
																,EPS_T_PO_HEADER.COMPANY_CD
															from
																EPS_T_PO_HEADER
															where
																EPS_T_PO_HEADER.PO_STATUS = '1280'
																and EPS_T_PO_HEADER.CLOSED_PO_MONTH = '$currentMonthClosing'
																and (EPS_T_PO_HEADER.SUPPLIER_CD != 'C1')
															group by
																EPS_T_PO_HEADER.SUPPLIER_CD
																,EPS_T_PO_HEADER.SUPPLIER_NAME
																,EPS_T_PO_HEADER.COMPANY_CD";
					$sql_select_t_po_hdr_groupby_supplier = $conn->query($query_select_t_po_hdr_groupby_supplier);
					while($row_select_t_po_hdr_groupby_supplier = $sql_select_t_po_hdr_groupby_supplier->fetch(PDO::FETCH_ASSOC))
					{
						$supplierCd     = $row_select_t_po_hdr_groupby_supplier['SUPPLIER_CD'];
						$companyCd      = $row_select_t_po_hdr_groupby_supplier['COMPANY_CD'];
						$supplierNameVal= $row_select_t_po_hdr_groupby_supplier['SUPPLIER_NAME'];

					   /**
						* SELECT EPS_M_SUPPLIER 
						*/
						$query_select_m_supplier = "select
														SUPPLIER_NUMBER
														,SUPPLIER_NAME_ALIAS
														,TAX_RATE
														,NPWP
														,VAT
														,CURRENCY_CD
													from
														EPS_M_SUPPLIER
													where
														SUPPLIER_CD = '$supplierCd'";
						$sql_select_m_supplier = $conn->query($query_select_m_supplier);
						$row_select_m_supplier = $sql_select_m_supplier->fetch(PDO::FETCH_ASSOC);
						$supplierNumber     = $row_select_m_supplier['SUPPLIER_NUMBER'];
						$supplierNameAlias  = $row_select_m_supplier['SUPPLIER_NAME_ALIAS'];
						$taxRate            = $row_select_m_supplier['TAX_RATE'];
						$npwp               = $row_select_m_supplier['NPWP'];
						$vatCd              = $row_select_m_supplier['VAT'];
						$currencyCdSupplier = $row_select_m_supplier['CURRENCY_CD'];
						
					   /** 
						* SELECT EPS_T_CN_SEQUENCE
						*/
						$currentYear        = (int)date(y);
						$currentMonth       = substr($currentMonthClosing,4,2);
						$currenctYearMonth  = $currentYear.$currentMonth;
						$cnRunDate          = '';
						$query_select_t_cn_seq = "select
													CN_RUNNING_NO
													,CN_RUNNING_DATE
													,COMPANY_NO
												from
													EPS_T_CN_SEQUENCE
												where
													COMPANY_CD = '$companyCd'
													and CN_RUNNING_DATE = '$currenctYearMonth'";
						$sql_select_t_cn_seq = $conn->query($query_select_t_cn_seq);
						$row_select_t_cn_seq = $sql_select_t_cn_seq->fetch(PDO::FETCH_ASSOC);
						if($row_select_t_cn_seq){
							$cnRunNo    = $row_select_t_cn_seq['CN_RUNNING_NO'];
							$cnRunDate  = $row_select_t_cn_seq['CN_RUNNING_DATE'];
							$companyNo  = $row_select_t_cn_seq['COMPANY_NO'];
						}

						if($currenctYearMonth == $cnRunDate){
							$cnRunNo = (int)$cnRunNo + 1;
							$query_update_t_cn_seq = "update
														EPS_T_CN_SEQUENCE
													set
														CN_RUNNING_NO = '$cnRunNo'
														,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
														,UPDATE_BY = '$sUserId'
													where
														COMPANY_CD = '$companyCd'
														and CN_RUNNING_DATE = '$cnRunDate'";
							$conn->query($query_update_t_cn_seq);
						}
						else
						{
							$cnRunNo = 1;
							$cnRunDate = $currenctYearMonth;
							if($companyCd == 'D')
							{
								$companyNo = '3';
							}
							if($companyCd == 'S')
							{
								$companyNo = '6';
							}
							if($companyCd == 'H')
							{
								$companyNo = '9';
							}
                                                        // 1 Mar 2018 Add company code TACI 
							if($companyCd == 'T')
							{
								$companyNo = '8';
							}
						   /**
							* INSERT EPS_T_CN_SEQUENCE 
							**/
							$query_insert_t_cn_seq = "insert
														EPS_T_CN_SEQUENCE
														(
															COMPANY_CD
															,COMPANY_NO
															,CN_RUNNING_NO
															,CN_RUNNING_DATE
															,CREATE_DATE
															,CREATE_BY
															,UPDATE_DATE
															,UPDATE_BY
														)
													values
														(
															'$companyCd'
															,'$companyNo'
															,'$cnRunNo'
															,'$cnRunDate'
															,convert(VARCHAR(24), GETDATE(), 120)
															,'$sUserId'
															,convert(VARCHAR(24), GETDATE(), 120)
															,'$sUserId'
														)";
							$conn->query($query_insert_t_cn_seq);
						}

					   /**
						* INITIAL VALUE 
						**/
						$sequenceCnNo   = str_pad($cnRunNo, 3, "0", STR_PAD_LEFT);
						$cnNo           = $companyNo.$currentYear.($currentMonth + 32).$sequenceCnNo;

						$cnTransactionNo++;
						$seqCnTransactionNo = str_pad($cnTransactionNo, 6, "0", STR_PAD_LEFT);
						$cnTransferId   = $currenctYearMonth."CNH".$seqCnTransactionNo;
						$cnInterfaceId  = $currenctYearMonth."CNI".$seqCnTransactionNo;
					  
					   /**
						* SELECT EPS_T_PO_HEADER 
						**/
						$totalPoAmountSupplier = 0;
						$query_select_t_po_header = "select
														EPS_T_PO_HEADER.PO_NO
														,EPS_T_PO_HEADER.SUPPLIER_CD
														,EPS_T_PO_HEADER.SUPPLIER_NAME
														,EPS_T_PO_HEADER.CURRENCY_CD
														,EPS_T_PO_HEADER.DELIVERY_PLANT
														,EPS_T_PO_HEADER.DELIVERY_DATE
														,EPS_T_PO_HEADER.COMPANY_CD
														,EPS_T_PO_HEADER.VAT
														,EPS_T_PO_HEADER.SEND_PO_DATE
														,EPS_T_PO_HEADER.CLOSED_PO_DATE
														,convert(VARCHAR(24), CLOSED_PO_DATE, 112) as PO_CLOSED_DATE
														,isnull
														((select 
															sum(AMOUNT)
														from         
															EPS_T_PO_DETAIL
														where     
															EPS_T_PO_HEADER.PO_NO = EPS_T_PO_DETAIL.PO_NO), 0) as TOTAL_PO_AMOUNT
													from
														EPS_T_PO_HEADER
													where
														EPS_T_PO_HEADER.SUPPLIER_CD = '$supplierCd'
                                                                                                                and EPS_T_PO_HEADER.SUPPLIER_NAME = '$supplierNameVal'
														and EPS_T_PO_HEADER.COMPANY_CD = '$companyCd'
														and EPS_T_PO_HEADER.CLOSED_PO_MONTH = '$currentMonthClosing'
														and EPS_T_PO_HEADER.PO_STATUS = '1280'";
						$sql_select_t_po_header = $conn->query($query_select_t_po_header);
						while($row_select_t_po_header = $sql_select_t_po_header->fetch(PDO::FETCH_ASSOC)){
							$totalPoAmount  = $row_select_t_po_header['TOTAL_PO_AMOUNT'];
							$poNoVal        = $row_select_t_po_header['PO_NO'];
							$supplierName   = $row_select_t_po_header['SUPPLIER_NAME']; 
							$currencyCd     = $row_select_t_po_header['CURRENCY_CD'];
							$deliveryPlant  = $row_select_t_po_header['DELIVERY_PLANT'];
							$deliveryDate   = $row_select_t_po_header['DELIVERY_DATE'];
							$vat            = $row_select_t_po_header['VAT'];
							$vat            = $vatCd;
							$sendPoDate     = $row_select_t_po_header['SEND_PO_DATE'];
							$closedPoDate   = $row_select_t_po_header['CLOSED_PO_DATE'];
							$poClosedDate   = $row_select_t_po_header['PO_CLOSED_DATE'];
							
							$split_total_po_amount = explode('.', $totalPoAmount);
							if($split_total_po_amount[1] == 0)
							{
								$totalPoAmount = number_format($totalPoAmount);
							}
							else
							{
								$totalPoAmount = number_format($totalPoAmount, 2);
							}
							$totalPoAmount     = str_replace(',', '',$totalPoAmount);
							$totalPoAmount     = rtrim(rtrim(number_format($totalPoAmount, 2, ".", ""), '0'), '.');
							$totalPoAmountSupplier = $totalPoAmountSupplier + $totalPoAmount;
                            $totalPoAmountSupplier = rtrim(rtrim(number_format($totalPoAmountSupplier, 2, ".", ""), '0'), '.');
						   
						   /**
							* SELECT EPS_T_PO_DETAIL
							**/
							$query_select_t_po_detail = "select
															EPS_T_PO_DETAIL.PO_NO
															,EPS_T_PO_DETAIL.REF_TRANSFER_ID
															,SUBSTRING(EPS_T_PO_DETAIL.ITEM_CD,1,9) AS ITEM_CD
															,EPS_T_PO_DETAIL.ITEM_NAME
															,EPS_T_PO_DETAIL.QTY
															,EPS_T_PO_DETAIL.ITEM_PRICE
															,EPS_T_PO_DETAIL.AMOUNT
															,EPS_T_PO_DETAIL.UNIT_CD
															,EPS_T_PO_DETAIL.ITEM_TYPE_CD
															,EPS_T_PO_DETAIL.ACCOUNT_NO
															,EPS_T_PO_DETAIL.RFI_NO
															,EPS_T_TRANSFER.NEW_CHARGED_BU
															,EPS_T_TRANSFER.PR_NO
														from
															EPS_T_PO_DETAIL
														left join
															EPS_T_TRANSFER 
														on 
															EPS_T_PO_DETAIL.REF_TRANSFER_ID = EPS_T_TRANSFER.TRANSFER_ID
														where
															EPS_T_PO_DETAIL.PO_NO = '$poNoVal'
														order by
															EPS_T_PO_DETAIL.ITEM_NAME ";
							$sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
							while($row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC))
							{
								$poNoDtl            = $row_select_t_po_detail['PO_NO'];
								$refTransferIdDtl   = $row_select_t_po_detail['REF_TRANSFER_ID'];
								$itemCdDtl          = $row_select_t_po_detail['ITEM_CD'];
								$itemNameDtl        = $row_select_t_po_detail['ITEM_NAME'];
								$itemNameDtl        = str_replace("'", "''", $itemNameDtl);
								$qtyDtl             = $row_select_t_po_detail['QTY'];
								$itemPriceDtl       = $row_select_t_po_detail['ITEM_PRICE'];
								$amountDtl          = $row_select_t_po_detail['AMOUNT'];
								$unitCdDtl          = $row_select_t_po_detail['UNIT_CD'];
								$itemTypeCdDtl      = $row_select_t_po_detail['ITEM_TYPE_CD'];
								$accountNoDtl       = $row_select_t_po_detail['ACCOUNT_NO'];
								$rfiNoDtl           = trim($row_select_t_po_detail['RFI_NO']);
								$chargedBu          = $row_select_t_po_detail['NEW_CHARGED_BU'];
								$prNo               = $row_select_t_po_detail['PR_NO'];

								if($itemTypeCdDtl == '1' || $itemTypeCdDtl == '3' || $itemTypeCdDtl == '4' || $itemTypeCdDtl == '5')
								{
									$objectAccount = $accountNoDtl;

								   /**
									* SELECT EPS_M_ACCOUNT
									**/
									$query_select_m_account = "select
																ACCOUNT_CD
															from
																EPS_M_ACCOUNT
															where
																ACCOUNT_NO = '$accountNoDtl'";
									$sql_select_m_account = $conn->query($query_select_m_account);
									$row_select_m_account = $sql_select_m_account->fetch(PDO::FETCH_ASSOC);
									$accountCdDb = $row_select_m_account['ACCOUNT_CD'];
									$atbxtx      = $accountCdDb;
									$oiaost      = "E";
									if(strlen($accountNoDtl) == 1)
									{
										$amarcd = "0".$accountNoDtl;
									}
									else
									{
										$amarcd      = $accountNoDtl;
									}
								}
								if($itemTypeCdDtl == '2')
								{
									$objectAccount = $rfiNoDtl;
									$atbxtx        = $rfiNoDtl;
									$oiaost        = "I";
									$amarcd        = substr($rfiNoDtl,0,3);
									if($deliveryPlant != 'HD' && strlen(trim($rfiNoDtl)) == 6)
									{
										$amarcd = substr($rfiNoDtl,3);
									}
								}
								if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
									&& $deliveryPlant =='JK')
								{
									$chargedBu = trim($chargedBu).'S';
								}
								if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
									&& $deliveryPlant =='GT' )
								{
									//$chargedBu = trim($chargedBu).'C';
                                                                        // Update 1 Mar 2018 . TACI no need modify charged BU
                                                                        $chargedBu = trim($chargedBu);
								}
								if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
									&& $deliveryPlant =='JF')
								{
									$chargedBu = trim($chargedBu).'F';
								}
								if((substr(trim($chargedBu), 0, 4)!= '1000' || substr(trim($chargedBu), 0, 4)!= '1001')
									&& $deliveryPlant =='SI')
								{
									$chargedBu = trim($chargedBu).'S';
                                                                        
								}
                                                                
                                                                if(($itemTypeCdDtl == '3' || $itemTypeCdDtl == '5') && $deliveryPlant =='GT')
								{
									$chargedBu = 'T1000';
								}
//								if(($itemTypeCdDtl == '3' || $itemTypeCdDtl == '5') && $deliveryPlant =='JK')
//								{
//									$chargedBu = '1000S';
//								}
//								if(($itemTypeCdDtl == '3' || $itemTypeCdDtl == '5') && $deliveryPlant =='GT')
//								{
//									$chargedBu = 'T1000';
//								}
//								if(($itemTypeCdDtl == '3' || $itemTypeCdDtl == '5') && $deliveryPlant =='JF')
//								{
//									$chargedBu = '1000F';
//								}
//								if($itemTypeCdDtl == '4' && $deliveryPlant =='SI')
//								{
//									$chargedBu = '1001S';
//								}
							   /**
								* SELECT EPS_M_CURRENCY
								*/
								$query_select_m_currency = "select 
																CURRENCY_NAME_ALIAS
																,CURRENCY_NAME_CN
															from
																EPS_M_CURRENCY
															where
																CURRENCY_CD = '$currencyCd'";
								$sql_select_m_currency = $conn->query($query_select_m_currency);
								$row_select_m_currency = $sql_select_m_currency->fetch(PDO::FETCH_ASSOC);
								$currencyNameAlias  = $row_select_m_currency['CURRENCY_NAME_ALIAS'];
								$currencyNameCn     = $row_select_m_currency['CURRENCY_NAME_CN'];

								$cnDtlNo++;
								$seqCnDtlNo = str_pad($cnDtlNo, 6, "0", STR_PAD_LEFT);
								$cnDtlId    = $currenctYearMonth."CND".$seqCnDtlNo;
								$cnId       = $currenctYearMonth."O56".$seqCnDtlNo;
								$cnAs400Id  = $currenctYearMonth."AS4".$seqCnDtlNo;

							   /**
								* INSERT EPS_T_CN_DETAIL
								*/
								$query_insert_t_cn_detail = "insert into
																EPS_T_CN_DETAIL
																(
																	CN_DTL_ID
																	,CN_TRANSFER_ID
																	,CN_NO
																	,CN_DATE
																	,PO_NO
																	,SUPPLIER_CD
																	,SUPPLIER_NAME
																	,VAT
																	,CURRENCY_CD
																	,DELIVERY_DATE
																	,DELIVERY_PLANT
																	,COMPANY_CD
																	,SEND_PO_DATE
																	,CLOSED_PO_DATE
																	,REF_TRANSFER_ID
																	,PR_NO
																	,ITEM_CD
																	,ITEM_NAME
																	,QTY
																	,ITEM_PRICE
																	,AMOUNT
																	,UNIT_CD
																	,ITEM_TYPE_CD
																	,OBJECT_ACCOUNT
																	,CHARGED_BU
																	,CREATE_DATE
																	,CREATE_BY
																	,UPDATE_DATE
																	,UPDATE_BY
																)
															values
																(
																	'$cnDtlId'
																	,'$cnTransferId'
																	,'$cnNo'
																	,'$mDate'
																	,'$poNoDtl'
																	,'$supplierCd'
																	,'$supplierName'
																	,'$vat'
																	,'$currencyCd'
																	,'$deliveryDate'
																	,'$deliveryPlant'
																	,'$companyCd'
																	,'$sendPoDate'
																	,'$closedPoDate'
																	,'$refTransferIdDtl'
																	,'$prNo'
																	,'$itemCdDtl'
																	,'$itemNameDtl'
																	,'$qtyDtl' 
																	,'$itemPriceDtl'
																	,'$amountDtl'
																	,'$unitCdDtl'
																	,'$itemTypeCdDtl'
																	,'$objectAccount'
																	,'$chargedBu'
																	,convert(VARCHAR(24), GETDATE(), 120)
																	,'$sUserId'
																	,convert(VARCHAR(24), GETDATE(), 120)
																	,'$sUserId'
																)";
								$conn->query($query_insert_t_cn_detail);

							   /**
								* INSERT EPS_T_CN_AS400
								*/
								$crntno     = $cnNo;
								$cryrnm     = $currentMonthClosing;
								$tbnosj     = "";
								$tbaicd     = $poNoDtl;
								$tbainb     = 1;
								$okkdsk     = $chargedBu;
								$skkdlk     = $deliveryPlant;
								$tbpost     = "C";
								$sdokno     = $prNo;
								$sdahcd     = 1;
								$oiaost     = $oiaost;
								$oiamcd     = $supplierCd;
								$oia8tx     = substr($supplierName,0,30);
								$oiagcd     = $itemCdDtl;
								$oia3tx     = substr($itemNameDtl,0,50);
								$oijlor     = $qtyDtl;
								$oia4tx     = $unitCdDtl;
								$oiadpr     = $itemPriceDtl;
								$oicur      = $currencyCd;
								$tbatdt     = "1".substr($poClosedDate,2);
								$tbannb     = $qtyDtl;
								$tbawdt     = $poClosedDate;
								$amarcd     = $amarcd;
								$atbxtx     = $atbxtx;
								$vatfg      = $vat;
								$aban8      = trim($supplierNumber);

								// Amount, Price, Qty for EPS_T_CN_AS400
								$split_qty_as400 = explode('.', $tbannb);
								if($split_qty_as400[1] == 0)
								{
									$tbannb = number_format($tbannb);
									$oijlor = number_format($oijlor);
								} 
								$tbannb = str_replace(',', '',$tbannb);  
								$oijlor = str_replace(',', '',$oijlor);

								$split_price_as400 = explode('.', $oiadpr);
								if($split_price_as400[1] == 0)
								{
									$oiadpr = number_format($oiadpr);
								}
								else
								{
									$oiadpr = number_format($oiadpr, 2);
								}
								$oiadpr = str_replace(',', '',$oiadpr);

								if($vatfg == 'VAT')
								{
									$vatfg = '1';
								}
								else
								{
									$vatfg = '';
								}
								if($aban8 == '')
								{
									$aban8 = 0;
								}

								$query_insert_t_cn_as400 = "insert into
															EPS_T_CN_AS400
															(
																CN_AS400_ID
																,CRNTNO
																,CRYRNM
																,TBNOSJ
																,TBAICD
																,TBAINB
																,OKKDSK
																,SKKDLK
																,TBPOST
																,SDOKNO
																,SDAHCD
																,OIAOST
																,OIAMCD
																,OIA8TX
																,OIAGCD
																,OIA3TX
																,OIJLOR
																,OIA4TX
																,OIADPR
																,OICUR
																,TBATDT
																,TBANNB
																,TBAWDT
																,AMARCD
																,ATBXTX
																,VATFG
																,ABAN8
																,CREATE_DATE
																,CREATE_BY
																,UPDATE_DATE
																,UPDATE_BY
															)
															values
															(
																'$cnAs400Id'
																,'$crntno'
																,'$cryrnm'
																,'$tbnosj'
																,'$tbaicd'
																,'$tbainb'
																,'$okkdsk'
																,'$skkdlk'
																,'$tbpost'
																,'$sdokno'
																,'$sdahcd'
																,'$oiaost'
																,'$oiamcd'
																,'$oia8tx'
																,'$oiagcd'
																,'$oia3tx'
																,'$oijlor'
																,'$oia4tx'
																,'$oiadpr'
																,'$oicur'
																,'$tbatdt'
																,'$tbannb'
																,'$tbawdt'
																,'$amarcd'
																,'$atbxtx'
																,'$vatfg'
																,'$aban8'
																,convert(VARCHAR(24), GETDATE(), 120)
																,'$sUserId'
																,convert(VARCHAR(24), GETDATE(), 120)
																,'$sUserId'
															)";
                                                                echo $query_insert_t_cn_as400;
								$conn->query($query_insert_t_cn_as400);
                                                                
								
								$query_insert_t_wccncpp = "insert into
																PCRGEN.WCCNCPP
															(
																CRNTNO
																,CRYRNM
																,TBNOSJ
																,TBAICD
																,TBAINB
																,OKKDSK
																,SKKDLK
																,TBPOST
																,SDOKNO
																,SDAHCD
																,OIAOST
																,OIAMCD
																,OIA8TX
																,OIAGCD
																,OIA3TX
																,OIJLOR
																,OIA4TX
																,OIADPR
																,OICUR
																,TBATDT
																,TBANNB
																,TBAWDT
																,AMARCD
																,ATBXTX
																,VATFG
																,ABAN8
															)
															values
															(
																$crntno
																,$cryrnm
																,'$tbnosj'
																,'$tbaicd'
																,$tbainb
																,'$okkdsk'
																,'$skkdlk'
																,'$tbpost'
																,'$sdokno'
																,$sdahcd
																,'$oiaost'
																,'$oiamcd'
																,'$oia8tx'
																,'$oiagcd'
																,'$oia3tx'
																,$oijlor
																,'$oia4tx'
																,$oiadpr 
																,'$oicur'
																,$tbatdt
																,$tbannb
																,$tbawdt
																,'$amarcd'
																,'$atbxtx'
																,'$vatfg'
																,$aban8
															)";
                                                                echo $query_insert_t_wccncpp;
								if(!$result = odbc_exec($conn_as,$query_insert_t_wccncpp))
								{
									$countErrorAs400++;
									echo $query_insert_t_wccncpp;
								}
							}
						}
						
						$totalTaxPoAmount= ($totalPoAmountSupplier * 0.1);
						$split_total_tax_po_amount = explode('.', $totalTaxPoAmount);
						if($split_total_tax_po_amount[1] == 0)
						{
							$totalTaxPoAmount = number_format($totalTaxPoAmount);
						}
						else
						{
							$totalTaxPoAmount = number_format($totalTaxPoAmount, 2);
						}
						$totalTaxPoAmount     = str_replace(',', '',$totalTaxPoAmount);
						$totalTaxPoAmount     = rtrim(rtrim(number_format($totalTaxPoAmount, 2, ".", ""), '0'), '.');

						if(trim($vat) == 'VAT')
						{
							$totalGrossAmount   = $totalPoAmountSupplier + $totalTaxPoAmount;
						}
						else
						{
							$totalGrossAmount   = $totalPoAmountSupplier;
						}
						$totalGrossAmount     = str_replace(',', '',$totalGrossAmount);
                        $totalGrossAmount     = rtrim(rtrim(number_format($totalGrossAmount, 2, ".", ""), '0'), '.');
						
						$totalAmount        = $totalPoAmountSupplier;
						$totalAmount        = str_replace(',', '',$totalAmount);
						$totalAmount        = rtrim(rtrim(number_format($totalAmount, 2, ".", ""), '0'), '.');
						 
						/**
						 * INSERT EPS_T_CN_HEADER
						 **/
						$query_insert_t_cn_header = "insert into
														EPS_T_CN_HEADER
														(
															CN_HDR_ID
															,CN_NO
															,SUPPLIER_NUMBER
															,SUPPLIER_CD
															,SUPPLIER_NAME
															,CURRENCY_CD
															,COMPANY_CD
															,GROSS_AMOUNT
															,TAXABLE_AMOUNT
															,VAT_CD
															,INVOICE_DATE
															,INVOICE_NO
															,CLOSING_MONTH
															,CREATE_DATE
															,CREATE_BY
															,UPDATE_DATE
															,UPDATE_BY
														)
														values
														(
															'$cnTransferId'
															,'$cnNo'
															,'$supplierNumber'
															,'$supplierCd'
															,'$supplierNameVal'
															,'$currencyCdSupplier'
															,'$companyCd'
															,'$totalGrossAmount'
															,'$totalAmount'
															,'$vatCd'
															,'$invoiceDate'
															,'$cnNo'
															,'$currentMonthClosing'
															,convert(VARCHAR(24), GETDATE(), 120)
															,'$sUserId'
															,convert(VARCHAR(24), GETDATE(), 120)
															,'$sUserId'
														)";
						$conn->query($query_insert_t_cn_header);
					}
					
					$yearClosing = substr($currentMonthClosing,2,2);
					$monthClosing = substr($currentMonthClosing,4,2);
					$closingId = $yearClosing.$monthClosing; 
					
					/**
					 * SELECT COUNT EPS_T_CN_DETAIL
					 */
					$query_select_count_eps_t_cn_detail = "select 
															count(*) as COUNT_CN_DETAIL
														   from
															EPS_T_CN_DETAIL
														   where
															CN_DTL_ID like '$closingId%' ";
					$sql_select_count_eps_t_cn_detail = $conn->query($query_select_count_eps_t_cn_detail);
					$row_select_count_eps_t_cn_detail = $sql_select_count_eps_t_cn_detail->fetch(PDO::FETCH_ASSOC);
					$countCnDetail = $row_select_count_eps_t_cn_detail['COUNT_CN_DETAIL'];
					
					/**
					 * SELECT COUNT EPS_T_CN_AS400
					 */
					$query_select_count_eps_t_cn_as400 = "select 
															count(*) as COUNT_CN_AS400
														   from
															EPS_T_CN_AS400
														   where
															CN_AS400_ID like '$closingId%' ";
					$sql_select_count_eps_t_cn_as400 = $conn->query($query_select_count_eps_t_cn_as400);
					$row_select_count_eps_t_cn_as400 = $sql_select_count_eps_t_cn_as400->fetch(PDO::FETCH_ASSOC);
					$countCnAS400 = $row_select_count_eps_t_cn_as400['COUNT_CN_AS400'];
					
					/**
					 * SELECT COUNT EPS_T_PO_DETAIL
					 */
					$query_select_count_t_po_detail = "select
														count(*) as COUNT_PO_DETAIL
													   from
														EPS_T_PO_DETAIL
													   left join
														EPS_T_PO_HEADER
													   on
														EPS_T_PO_DETAIL.PO_NO = EPS_T_PO_HEADER.PO_NO 
													   where
														EPS_T_PO_HEADER.PO_STATUS = '1280'
														and CLOSED_PO_MONTH = '$currentMonthClosing'";
					$sql_select_count_t_po_detail = $conn->query($query_select_count_t_po_detail);
					$row_select_count_t_po_detail = $sql_select_count_t_po_detail->fetch(PDO::FETCH_ASSOC);
					$countPoDetail = $row_select_count_t_po_detail['COUNT_PO_DETAIL'];
					
					/**
					 * SELECT COUNT PCRGEN.WCCNCPP
					 */
					$query_select_count_wccncpp = "select 
													count(*) as COUNT_WCCNCPP
												   from
													PCRGEN.WCCNCPP
												   where
													CRYRNM = '$currentMonthClosing'";
					$result_wccncpp = odbc_exec($conn_as,$query_select_count_wccncpp);
					//$countPcrgen  = odbc_result($result_wccncpp,1);
					if($result_wccncpp)
					{
						$countPcrgen = odbc_result($result_wccncpp, "COUNT_WCCNCPP");
					}
					
					if($countCnDetail == $countCnAS400 && $countCnAS400 == $countPoDetail 
							&&  $countCnDetail == $countPoDetail &&  $countCnDetail == $countPcrgen
							&& $countCnAS400 == $countPcrgen &&  $countPoDetail == $countPcrgen)
					{
					
						/**********************************************************************
						 * CALL AS400 PROGRAM
						 **********************************************************************/
//						$setClosingMonth = substr($currentMonthClosing,4,2);
//						$setClosingYear = substr($currentMonthClosing,0,4);
//						$test = 'TEST';
                                                
                                                //Nyalain nanti pas Closing... iqbal 
                                                //ADA PERUBAHAN ADD PARAMETER (IQBAL)
//						$query_call_as400_cl = "call PCRGEN.XJDESBM PARM('".$setClosingMonth."','".$setClosingYear."', '".$test."')";
//						$result_call_cl = odbc_exec($conn_as,$query_call_as400_cl);
						
						/**
						 * UPDATE EPS_T_PO_HEADER
						 */
						$query_update_t_po_header = "update
														EPS_T_PO_HEADER
													  set
														PO_STATUS = '1330'
														,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
														,UPDATE_BY = '$sUserId'
													  where 
														PO_STATUS = '1280'
														and CLOSED_PO_MONTH = '$currentMonthClosing'";
						$conn->query($query_update_t_po_header);
						 
						/*********************************************************
						 * SEND MAIL _ FINISH
						 *********************************************************/
						$mailTo_2         = $procMailCriteria.",KARYOTO@taci.toyota-industries.com,wiharyo@taci.toyota-industries.com, ahmadjafar@taci.toyota-industries.com, bayu.thr@taci.toyota-industries.com, ISON@taci.toyota-industries.com, muh.iqbal@taci.toyota-industries.com";
						$mailSubject_2  = "[EPS] Monthly Closing ".$currentMonthClosing." (FINISH)";
						$mailMessage_2  = "<font face='Trebuchet MS' size='-1'>";
						$mailMessage_2 .= "<u>Dear General Supplies Team,</u>";
						$mailMessage_2 .= "<br><br>Saat ini proses closing sudah selesai. Berikut ini informasi mengenai EPS - Monthly Closing:";
						$mailMessage_2 .= "<br>1. Periode Closing adalah $currentMonthClosing";
						$mailMessage_2 .= "<br>2. Proses dijalankan oleh Pak Wiharyo (as In Charge).";
						$mailMessage_2 .= "<br>3. Aktivitas untuk meng-open-kan PO sudah bisa dilakukan kembali.";
						$mailMessage_2 .= "<br>4. CN Report akan dibentuk setelah pengecekan data dilakukan oleh Accounting.";
						$mailMessage_2 .= "<br><br><br><u>Dear Accounting Team,</u>";
						$mailMessage_2 .= "<br><br>Saat ini proses closing sudah selesai.";
						$mailMessage_2 .= "<br>Mohon dilakukan pengecekan di AS400 untuk user ID EPD12 apakah CN sudah terbentuk atau belum.";
						$mailMessage_2 .= "<br>Silahkan mengkonfirmasi by email ke Pak Suharno (as In Charge) agar bisa melanjutkan proses CN Report.";
						$mailMessage_2 .= "<br><br>Thanks,";
						$mailMessage_2 .= "<br>EPS Administrator";
						$mailMessage_2 .= "</font>";
						cnProcessSendMail ($mailTo_2, $mailFrom, $mailFromName, $mailSubject_2, $mailMessage_2);
						
						$msg = "Success";
					}
					else
					{
						/**
						* DELETE EPS_T_CN_DETAIL
						*/
						$query_delete_eps_t_cn_detail = "delete
														 from
															EPS_T_CN_DETAIL
														 where
															CN_DTL_ID like '$closingId%' ";
						$conn->query($query_delete_eps_t_cn_detail);

					   /**
						* DELETE EPS_T_CN_AS400
						*/
						$query_delete_eps_t_cn_as400 = "delete
														from
															EPS_T_CN_AS400
														where
															CN_AS400_ID like '$closingId%' ";
						$conn->query($query_delete_eps_t_cn_as400);

					   /**
						* DELETE EPS_T_CN_HEADER
						*/
						$query_delete_eps_t_cn_header = "delete
														 from
															EPS_T_CN_HEADER
														 where
															CN_HDR_ID like '$closingId%' ";
						$conn->query($query_delete_eps_t_cn_header);
					   
					   /**
						* DELETE EPS_T_CN_SEQUENCES
						*/
						$query_delete_eps_t_cn_seq = "delete
														 from
															EPS_T_CN_SEQUENCE
														 where
															CN_RUNNING_DATE = '$closingId' ";
						$conn->query($query_delete_eps_t_cn_seq);
						
					   /**
						* DELETE PCRGEN.WCCNCPP
						*/
						$query_delete_wccncpp = "delete
												 from
													PCRGEN.WCCNCPP
												 where
													CRYRNM = '$currentMonthClosing'";
						$result_delete_wccncpp = odbc_exec($conn_as,$query_delete_wccncpp);
						
					   /**
                        * DELETE EPS_T_CN_PERIOD
                        */
                        $query_delete_t_cn_period = "delete
                                                    from   
                                                        EPS_T_CN_PERIOD
                                                    where
                                                        CN_RUNNING_YEAR = '$periodYear'
                                                        and CN_RUNNING_MONTH = '$periodMonth'";
                        $conn->query($query_delete_t_cn_period);
						$msg = "UnmatchDataCount";
					}	
					
				}
                else if($row_select_t_cn_period)
                {
					/**
                     * DELETE EPS_T_CN_PERIOD
                     */
                    $query_delete_t_cn_period = "delete
                                                 from   
                                                    EPS_T_CN_PERIOD
                                                 where
                                                    CN_RUNNING_YEAR = '$periodYear'
                                                    and CN_RUNNING_MONTH = '$periodMonth'";
                    $conn->query($query_delete_t_cn_period);
                    $msg = "Duplicate";
                }
				else
				{
					$msg = "";
				}
            }
            else
            {
                $msg = 'Mandatory_1';
            }
        }
    }
    else
    {
        $msg = 'SessionExpired';
    }
}
else
{
    $msg = 'SessionExpired';
}
echo $msg;
?>
