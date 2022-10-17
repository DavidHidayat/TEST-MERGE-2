<?php session_start();
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/PDOdb.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/Constant.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/Common.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//db/Email/RO_EMAIL.php";
include $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/'))."//lib/mail_lib/crypt.php";
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'//lib/mail_lib/class.phpmailer.php';
require $_SERVER['DOCUMENT_ROOT']."/".substr($_SERVER['REQUEST_URI'],1,strpos(substr($_SERVER['REQUEST_URI'],1),'/')).'//lib/mail_lib/class.smtp.php';
if (isset($_SESSION['sUserId'])) {
    $sUserId    = $_SESSION['sUserId'];

    if (trim($sUserId) != '') {
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

        if ($action == 'SaveReceiving') {
            $currentDate       = date("Ymd");
            $poNoPrm           = strtoupper(trim($_GET['poNoPrm']));
            $transferIdPrm     = strtoupper(trim($_GET['transferIdValPrm']));
            $updateDatePrm     = strtoupper(trim($_GET['updateDatePrm']));
            $totalReceivedQtyPrm = trim($_GET['totalReceivedQtyPrm']);
            $poQtyPrm          = trim($_GET['poQtyPrm']);
            $itemReceived      = array();
            $itemReceivedTemp  = array();
            $itemReceived      = ($_SESSION['roDetail']);

            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                        UPDATE_DATE
                                        ,SUPPLIER_CD
                                      from 
                                        EPS_T_PO_HEADER
                                      where
                                        PO_NO = '$poNoPrm'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header = $sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_po_header['UPDATE_DATE']);
            $supplierCd     = $row_eps_t_po_header['SUPPLIER_CD'];

            if ($updateDatePrm == $newUpdateDate) {
                if (count($itemReceived) > 0) {
                    if ($totalReceivedQtyPrm <= $poQtyPrm) {
                        unset($_SESSION['roStatus']);
                        $currentMonth = date("Ym");

                        /**
                         * SELECT EPS_T_RO_DETAIL - Initial Count
                         */
                        $query_select_count_t_ro = "select
                                                        count(*) as COUNT_RECEIVED_QTY
                                                    from
                                                        EPS_T_RO_DETAIL
                                                    where 
                                                        PO_NO = '$poNoPrm'
                                                        and REF_TRANSFER_ID = '$transferIdPrm'
                                                        and TRANSACTION_FLAG = 'A' ";
                        $sql_select_count_t_ro = $conn->query($query_select_count_t_ro);
                        $row_select_count_t_ro = $sql_select_count_t_ro->fetch(PDO::FETCH_ASSOC);
                        $countReceiving = $row_select_count_t_ro['COUNT_RECEIVED_QTY'];

                        /**
                         * INSERT into EPS_T_RO_DETAIL
                         */
                        for ($i = 0; $i < count($itemReceived); $i++) {
                            $roNo           = $itemReceived[$i]['roNo'];
                            $roSeq          = $itemReceived[$i]['roSeq'];
                            $poNo           = $itemReceived[$i]['poNo'];
                            $refTransferId  = $itemReceived[$i]['refTransferId'];
                            $transactionQty = $itemReceived[$i]['transactionQty'];
                            $transactionFlag = $itemReceived[$i]['transactionFlag'];
                            $transactionDate = encodeDate($itemReceived[$i]['transactionDate']);

                            // Define RO NO
                            $query2 = "select 
                                        count(*) as RO_COUNT 
                                    from 
                                        EPS_T_RO_DETAIL 
                                    where 
                                        substring(RO_NO, 1, 8) = '$currentDate'";
                            $sql2 = $conn->query($query2);
                            $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                            $roCount = $row2['RO_COUNT'];

                            if ($roCount == 0) {
                                $sequences = '1';
                            } else {
                                $sequences = $roCount + 1;
                            }
                            //$roNo = $currentDate.'RO'.$sequences;
                            $sequencesNo = str_pad($sequences, 4, "0", STR_PAD_LEFT);
                            $roNo = $currentDate . trim($sUserId) . 'RO' . $sequencesNo;

                            /**
                             * SELECT from EPS_T_RO_DETAIL
                             */
                            $query_select_t_ro_detail = "select
                                                            RO_SEQ
                                                            ,PO_NO
                                                            ,REF_TRANSFER_ID
                                                        from
                                                            EPS_T_RO_DETAIL
                                                        where
                                                            RO_SEQ = '$roSeq'
                                                            and PO_NO = '$poNo'
                                                            and REF_TRANSFER_ID = '$refTransferId'";
                            $sql_select_t_ro_detail = $conn->query($query_select_t_ro_detail);
                            $row_select_t_ro_detail = $sql_select_t_ro_detail->fetch(PDO::FETCH_ASSOC);

                            if (!$row_select_t_ro_detail) {
                                /**
                                 * INSERT in EPS_T_RO_DETAIL
                                 **/
                                $query_insert_t_ro_detail = "insert into
                                                                EPS_T_RO_DETAIL
                                                                (
                                                                    RO_NO
                                                                    ,RO_SEQ
                                                                    ,PO_NO
                                                                    ,REF_TRANSFER_ID
                                                                    ,TRANSACTION_QTY
                                                                    ,TRANSACTION_DATE
                                                                    ,TRANSACTION_DATE_TIME
                                                                    ,TRANSACTION_FLAG
                                                                    ,CREATE_DATE
                                                                    ,CREATE_BY
                                                                    ,UPDATE_DATE
                                                                    ,UPDATE_BY
                                                                )
                                                            values
                                                                (
                                                                    '$roNo'
                                                                    ,'$roSeq'
                                                                    ,'$poNo'
                                                                    ,'$refTransferId'
                                                                    ,'$transactionQty'
                                                                    ,'$transactionDate'
                                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                                    ,'$transactionFlag'
                                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                                    ,'$sUserId'
                                                                    ,convert(VARCHAR(24), GETDATE(), 120)
                                                                    ,'$sUserId'
                                                                )";
                                $conn->query($query_insert_t_ro_detail);
                            } else {
                                $query_update_t_ro_detail = "update
                                                                EPS_T_RO_DETAIL
                                                            set
                                                                UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                                ,UPDATE_BY = '$sUserId'
                                                            where
                                                                RO_SEQ = '$roSeq'
                                                                and PO_NO = '$poNo'
                                                                and REF_TRANSFER_ID = '$refTransferId'";
                                $conn->query($query_update_t_ro_detail);
                            }
                        }

                        /**
                         * SELECT EPS_T_RO_DETAIL
                         */
                        $query_select_total_t_ro_detail = "select distinct
                                                            isnull(
                                                                (select 
                                                                    sum(TRANSACTION_QTY)
                                                                from 
                                                                    EPS_T_RO_DETAIL
                                                                where   
                                                                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A'
                                                                )
                                                            ,0
                                                            ) as TOTAL_RECEIVED_QTY
                                                            ,isnull(
                                                                (select 
                                                                    sum(TRANSACTION_QTY)
                                                                from 
                                                                    EPS_T_RO_DETAIL
                                                                where   
                                                                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'C')
                                                                ,0
                                                            ) as TOTAL_CANCELED_QTY
                                                            ,isnull(
                                                                (select 
                                                                    sum(TRANSACTION_QTY)
                                                                from 
                                                                    EPS_T_RO_DETAIL
                                                                where   
                                                                    EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                    and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                                    and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                                                                ,0
                                                            ) as TOTAL_OPENED_QTY
                                                            ,EPS_T_PO_DETAIL.QTY
                                                            from
                                                                EPS_T_PO_DETAIL
                                                            left join
                                                                EPS_T_RO_DETAIL
                                                            on
                                                                EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                                and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                            where
                                                                EPS_T_PO_DETAIL.PO_NO = '$poNoPrm'
                                                                and EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'";
                        $sql_select_total_t_ro_detail = $conn->query($query_select_total_t_ro_detail);
                        $row_select_total_t_ro_detail = $sql_select_total_t_ro_detail->fetch(PDO::FETCH_ASSOC);
                        $totalReceivedQty   = $row_select_total_t_ro_detail['TOTAL_RECEIVED_QTY'];
                        $totalCanceledQty   = $row_select_total_t_ro_detail['TOTAL_CANCELED_QTY'];
                        $totalOpenedQty     = $row_select_total_t_ro_detail['TOTAL_OPENED_QTY'];
                        $qty                = $row_select_total_t_ro_detail['QTY'];
                        $totalAllReceivedQty = $totalReceivedQty - $totalCanceledQty - $totalOpenedQty;

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
                        $qtyTransfer = $row_select_t_transfer['NEW_QTY'];
                        $actualQty = $row_select_t_transfer['ACTUAL_QTY'];

                        /**
                         * SELECT EPS_T_PO_HEADER, EPS_T_PO_DETAIL
                         **/
                        $query_select_sum_t_po_detail = "select
                                                            sum(EPS_T_PO_DETAIL.QTY) as TOTAL_CLOSED_QTY
                                                        from
                                                            EPS_T_PO_DETAIL 
                                                        where
                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'
                                                            and EPS_T_PO_DETAIL.RO_STATUS = '1320' ";
                        $sql_select_sum_t_po_detail = $conn->query($query_select_sum_t_po_detail);
                        $row_select_sum_t_po_detail = $sql_select_sum_t_po_detail->fetch(PDO::FETCH_ASSOC);
                        $totalClosedQty = $row_select_sum_t_po_detail['TOTAL_CLOSED_QTY'];

                        /**
                         * CHECK FOR PARTIAL QTY TRANSFER
                         */
                        /*if($qty == $totalAllReceivedQty)
                        {
                            /**
                            * UPDATE EPS_T_PO_DETAIL
                            */
                        /*$query_update_t_ro_header = "update
                                                            EPS_T_PO_DETAIL
                                                        set
                                                            RO_STATUS = '1320'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            PO_NO = '$poNoPrm'
                                                            and REF_TRANSFER_ID = '$transferIdPrm'";
                            $conn->query($query_update_t_ro_header);

                            /**
                            * UPDATE EPS_T_TRANSFER 
                            */
                        /*$query_update_t_transfer = "update
                                                            EPS_T_TRANSFER
                                                        set
                                                            ITEM_STATUS = '1320'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            TRANSFER_ID = '$transferIdPrm'";
                            $conn->query($query_update_t_transfer);

                            $msg = 'Success_Closed_Item';
                        }
                        else
                        {
                            /**
                            * UPDATE EPS_T_PO_DETAIL
                            */
                        /*($query_update_t_ro_header = "update
                                                            EPS_T_PO_DETAIL
                                                        set
                                                            UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            PO_NO = '$poNoPrm'
                                                            and REF_TRANSFER_ID = '$transferIdPrm'";
                            $conn->query($query_update_t_ro_header);
                            $msg = 'Success';
                        }*/


                        /**
                         * CHECK FOR PARTIAL QTY TRANSFER or LAST PARTIAL QTY
                         * Update on 11 Mar 2016, 07.58AM
                         */
                        if ($qtyTransfer == $totalAllReceivedQty || $totalClosedQty == $qtyTransfer) {
                            $roStatus = '1320';
                            $msg = 'Success_Closed_Item';
                        } else {
                            if ($qty == $totalAllReceivedQty) {
                                $roStatus = '1320';
                                $msg = 'Success_Closed_Item';
                            } else {
                                $roStatus = '1310';
                                $msg = 'Success';
                            }
                        }

                        /**
                         * UPDATE EPS_T_PO_DETAIL
                         */
                        $query_update_t_ro_header = "update
                                                        EPS_T_PO_DETAIL
                                                     set
                                                        RO_STATUS = '$roStatus'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                     where
                                                        PO_NO = '$poNoPrm'
                                                        and REF_TRANSFER_ID = '$transferIdPrm'";
                        $conn->query($query_update_t_ro_header);

                        /**
                         * SELECT EPS_T_PO_HEADER, EPS_T_PO_DETAIL
                         **/
                        $query_select_sum_t_po_detail = "select
                                                            sum(EPS_T_PO_DETAIL.QTY) as TOTAL_CLOSED_QTY
                                                        from
                                                            EPS_T_PO_DETAIL 
                                                        where
                                                            EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$transferIdPrm'
                                                            and EPS_T_PO_DETAIL.RO_STATUS = '1320' ";
                        $sql_select_sum_t_po_detail = $conn->query($query_select_sum_t_po_detail);
                        $row_select_sum_t_po_detail = $sql_select_sum_t_po_detail->fetch(PDO::FETCH_ASSOC);
                        $totalClosedQty = $row_select_sum_t_po_detail['TOTAL_CLOSED_QTY'];

                        if ($qtyTransfer == $totalAllReceivedQty || $totalClosedQty == $qtyTransfer) {
                            /**
                             * UPDATE EPS_T_TRANSFER 
                             */
                            $query_update_t_transfer = "update
                                                            EPS_T_TRANSFER
                                                        set
                                                            ITEM_STATUS = '$roStatus'
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            TRANSFER_ID = '$transferIdPrm'";
                            $conn->query($query_update_t_transfer);
                        }


                        /**
                         * SELECT EPS_T_PO_DETAIL
                         */
                        $query_count_t_po_detail = "select
                                                        count(*) as ITEM_OPEN
                                                    from
                                                        EPS_T_PO_DETAIL
                                                    where
                                                        PO_NO = '$poNoPrm'
                                                        and RO_STATUS = '1310'";
                        $sql_count_t_po_detaill = $conn->query($query_count_t_po_detail);
                        $row_count_t_po_detail = $sql_count_t_po_detaill->fetch(PDO::FETCH_ASSOC);
                        $countItemOpen = $row_count_t_po_detail['ITEM_OPEN'];

                        if ($countItemOpen == 0) {
                            if ($supplierCd == 'C1') {
                                $poStatus = '1370';
                            } else {
                                $poStatus = '1280';
                            }
                            /**
                             * UPDATE EPS_T_PO_HEADER
                             */
                            $query_update_t_po_header = "update
                                                            EPS_T_PO_HEADER
                                                        set
                                                            PO_STATUS = '$poStatus'
                                                            ,CLOSED_PO_MONTH = '$currentMonth'
                                                            ,CLOSED_PO_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            PO_NO = '$poNoPrm' ";
                            $conn->query($query_update_t_po_header);

                            $msg = 'Success_Closed_Po';
                        } else {
                            /**
                             * UPDATE EPS_T_PO_HEADER
                             */
                            $query_update_t_po_header = "update
                                                            EPS_T_PO_HEADER
                                                        set
                                                            UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                            ,UPDATE_BY = '$sUserId'
                                                        where
                                                            PO_NO = '$poNoPrm' ";
                            $conn->query($query_update_t_po_header);
                        }

                        /**********************************************************************
                         * SEND MAIL
                         **********************************************************************/
                        $mailFrom       = $sInet;
                        $mailFromName   = $sNotes;

                        if (count($itemReceived) > $countReceiving) {
                            $query_select_t_transfer_req = "select 
                                                                REQUESTER
                                                                ,PR_NO
                                                                ,ITEM_NAME
                                                            from
                                                                EPS_T_TRANSFER
                                                            where
                                                                TRANSFER_ID = '$transferIdPrm'";
                            $sql_select_t_transfer_req = $conn->query($query_select_t_transfer_req);
                            $row_select_t_transfer_req  = $sql_select_t_transfer_req->fetch(PDO::FETCH_ASSOC);
                            $requester  = $row_select_t_transfer_req['REQUESTER'];
                            $prNo       = $row_select_t_transfer_req['PR_NO'];
                            $itemName   = $row_select_t_transfer_req['ITEM_NAME'];

                            $query_select_dscid = "select 
                                                        EPS_M_DSCID.INETML
                                                        ,EPS_M_USER.PASSWORD 
                                                    from 
                                                        EPS_M_DSCID 
                                                    inner join 
                                                        EPS_M_USER 
                                                    on 
                                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                                    where  
                                                        rtrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('" . $requester . "')";
                            $sql_select_dscid = $conn->query($query_select_dscid);
                            $row_select_dscid = $sql_select_dscid->fetch(PDO::FETCH_ASSOC);
                            if ($row_select_dscid) {
                                $mailTo       = $row_select_dscid['INETML'];
                                $password       = $row_select_dscid['PASSWORD'];
                                $getParamLink   = paramEncrypt("action=open&prNo=$prNo&userId=$requester&password=$password");
                                $mailSubject = "[EPS] ITEM RECEIVED. PR No: " . $prNo;
                                $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                                $mailMessage .= "<tr><td>PR No</td><td>: </td><td>" . $prNo . "</td></tr>";
                                $mailMessage .= "<tr><td>Item Name</td><td>:</td><td>" . $itemName . "</td></tr>";
                                $mailMessage .= "<tr><td colspan='2'>** Barang segera diambil di Receiving Procurement (max. 1 x 24 jam)</td></tr>";
                                $mailMessage .= "</table>";
                                //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                                //Matikan sementara untuk trial
                                // roSendMailReceived($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                                $query_send_mail = "insert into 
                                            EPS_T_PR_MAIL 
                                            (
                                            PR_NO
                                            ,REQUESTER_MAIL
                                            ,REQUESTER_MAIL_NAME
                                            ,NEW_APPROVER
                                            ,GET_PARAM_APPROVER
                                            ,SENT
                                            ,SUBJECT_MAIL
                                            ,ITEM_NAME
                                            ) 
                                    VALUES
                                            (
                                            '$prNo'
                                            ,'$mailFrom'
                                            ,'$mailFromName'
                                            ,'$mailTo'
                                            ,'$getParamLink'
                                            ,'5'
                                            ,'$mailSubject'
                                            ,'$itemName'
                                )";
                                $sql = $conn->query($query_send_mail);
                                $row = $sql->fetch(PDO::FETCH_ASSOC);
                            }
                        }
                        /* Select New Item Code and Item Price */
                        $where = "where PO_D.PO_NO ='".$_SESSION['poNoSession']."' AND PO_D.REF_TRANSFER_ID ='".$_SESSION['refTransferIdSession']."';";
                        $sql_new_item_cd = "
                        SELECT
                            ITEM_PRICE,
                            SUBSTRING(PO_D.ITEM_CD,0,10) + PO_H.SUPPLIER_CD + SUBSTRING(PO_D.ITEM_CD,14,2) as ITEM_CD
                        FROM
                            EPS_T_PO_HEADER as PO_H
                            LEFT JOIN EPS_T_PO_DETAIL as PO_D ON PO_H.PO_NO = PO_D.PO_NO
                        $where";
                        $sql_new_item_cd = $conn->query($sql_new_item_cd);
                        $row_new_item_cd = $sql_new_item_cd->fetch(PDO::FETCH_ASSOC);
                        /* Check New Item Code Exist/not in EPS_M_ITEM */
                        $sql_check_m_item = $conn->query("SELECT * FROM EPS_M_ITEM WHERE ITEM_CD ='".$row_new_item_cd['ITEM_CD']."'");
                        $row_check_m_item = $sql_check_m_item->fetch(PDO::FETCH_ASSOC);
                        if (!$row_check_m_item) {
                            /* If not Exist insert EPS_M_ITEM using PO Data*/
                            $sql_insert_m_item ="INSERT INTO EPS_M_ITEM
                            SELECT
                                '".$row_new_item_cd['ITEM_CD']."',
                                M_I.ITEM_NAME, 
                                M_I.ITEM_GROUP_CD, 
                                M_I.ACTIVE_FLAG, 
                                GETDATE() as CREATE_DATE, 
                                '".$sUserId."' as CREATE_BY, 
                                null as UPDATE_DATE, 
                                null as UPDATE_BY, 
                                M_I.OBJECT_ACCOUNT_CD, 
                                M_I.TRANSAKSI_CD
                            FROM
                                EPS_T_PO_HEADER as PO_H
                                LEFT JOIN EPS_T_PO_DETAIL as PO_D ON PO_H.PO_NO = PO_D.PO_NO
                                LEFT JOIN EPS_M_ITEM as M_I ON PO_D.ITEM_CD = M_I.ITEM_CD
                            $where";
                            $conn->query($sql_insert_m_item);
                        }

                        /* Check New Item Code Exist/not in EPS_M_ITEM_PRICE */
                        $sql_check_m_item_price = $conn->query("SELECT * FROM EPS_M_ITEM_PRICE WHERE ITEM_CD ='".$row_new_item_cd['ITEM_CD']."'");
                        $row_check_m_item_price = $sql_check_m_item_price->fetch(PDO::FETCH_ASSOC);
                        if ($row_check_m_item_price) {
                            /* If Exist UPDATE EPS_M_ITEM_PRICE*/
                            $sql_update_m_item_price ="UPDATE EPS_M_ITEM_PRICE SET
                            UPDATE_DATE = GETDATE(),
                            UPDATE_BY = '".$sUserId."',
                            ITEM_PRICE = ".$row_new_item_cd['ITEM_PRICE']."
                            WHERE ITEM_CD ='".$row_new_item_cd['ITEM_CD']."'";
                            $conn->query($sql_update_m_item_price);

                        }else{
                            /* If not Exist insert EPS_M_ITEM_PRICE using PO Data*/
                            $sql_insert_m_item_price ="INSERT INTO EPS_M_ITEM_PRICE
                            SELECT
                                SUBSTRING(PO_D.ITEM_CD,0,10) + PO_H.SUPPLIER_CD + SUBSTRING(PO_D.ITEM_CD,14,2) as ITEM_CD,
                                PO_H.SUPPLIER_CD,
                                PO_D.UNIT_CD,
                                PO_H.CURRENCY_CD,
                                PO_D.ITEM_PRICE,
                                '".date('Ymd')."',
                                null as EFFECTIVE_DATE_END,
                                null as ATTACHMENT_QUOTATION,
                                M_IP.LEAD_TIME,
                                GETDATE() as CREATE_DATE,
                                '".$sUserId."' as CREATE_BY,
                                null as UPDATE_DATE,
                                null as UPDATE_BY,
                                M_IP.OBJECT_ACCOUNT_CD,
                                M_IP.BU_CD,
                                 M_IP.ITEM_CATEGORY
                            FROM
                                EPS_T_PO_HEADER as PO_H
                                LEFT JOIN EPS_T_PO_DETAIL as PO_D ON PO_H.PO_NO = PO_D.PO_NO
                                LEFT JOIN EPS_M_ITEM as M_I ON PO_D.ITEM_CD = M_I.ITEM_CD
                                LEFT JOIN EPS_M_ITEM_PRICE as M_IP ON M_I.ITEM_CD = M_IP.ITEM_CD
                            $where";
                            $conn->query($sql_insert_m_item_price);
                        }                        
                        $msg = 'Success';
                    } else {
                        $msg = 'Mandatory_3';
                    }
                } else {
                    $msg = 'Mandatory_1';
                }
            } else {
                $msg = 'Mandatory_2';
            }
        }

        if ($action == 'CancelReceiving') {
            $currentDate       = date('Ymd');
            $poNoPrm           = strtoupper(trim($_GET['poNoPrm']));
            $transferIdPrm     = strtoupper(trim($_GET['transferIdValPrm']));
            $updateDatePrm     = strtoupper(trim($_GET['updateDatePrm']));
            $itemReceived      = array();
            $itemReceivedTemp  = array();
            $itemReceived      = ($_SESSION['roDetail']);

            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                            UPDATE_DATE
                                        from 
                                            EPS_T_PO_HEADER
                                        where
                                            PO_NO = '$poNoPrm'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header = $sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_po_header['UPDATE_DATE']);

            if ($updateDatePrm == $newUpdateDate) {
                unset($_SESSION['roStatus']);

                /**
                 * INSERT into EPS_T_RO_DETAIL
                 */
                for ($i = 0; $i < count($itemReceived); $i++) {
                    $roNo           = $itemReceived[$i]['roNo'];
                    $roSeq          = $itemReceived[$i]['roSeq'];
                    $poNo           = $itemReceived[$i]['poNo'];
                    $refTransferId  = $itemReceived[$i]['refTransferId'];
                    $transactionQty = $itemReceived[$i]['transactionQty'];
                    $transactionFlag = $itemReceived[$i]['transactionFlag'];
                    $roRemark       = $itemReceived[$i]['roRemark'];
                    $transactionDate = encodeDate($itemReceived[$i]['transactionDate']);

                    // Define RO NO
                    $query2 = "select 
                                    count(*) as RO_COUNT 
                            from 
                                    EPS_T_RO_DETAIL 
                            where 
                                    substring(RO_NO, 1, 8) = '$currentDate'";
                    $sql2 = $conn->query($query2);
                    $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                    $roCount = $row2['RO_COUNT'];

                    if ($roCount == 0) {
                        $sequences = '1';
                    } else {
                        $sequences = $roCount + 1;
                    }
                    //$roNo = $currentDate.'RO'.$sequences;
                    $sequencesNo = str_pad($sequences, 4, "0", STR_PAD_LEFT);
                    $roNo = $currentDate . trim($sUserId) . 'RO' . $sequencesNo;

                    /**
                     * SELECT from EPS_T_RO_DETAIL
                     */
                    $query_select_t_ro_detail = "select
                                                    RO_SEQ
                                                    ,PO_NO
                                                    ,REF_TRANSFER_ID
                                                from
                                                    EPS_T_RO_DETAIL
                                                where
                                                    RO_SEQ = '$roSeq'
                                                    and PO_NO = '$poNo'
                                                    and REF_TRANSFER_ID = '$refTransferId'";
                    $sql_select_t_ro_detail = $conn->query($query_select_t_ro_detail);
                    $row_select_t_ro_detail = $sql_select_t_ro_detail->fetch(PDO::FETCH_ASSOC);

                    if (!$row_select_t_ro_detail) {
                        /**
                         * INSERT in EPS_T_RO_DETAIL
                         **/
                        $query_insert_t_ro_detail = "insert into
                                                        EPS_T_RO_DETAIL
                                                        (
                                                            RO_NO
                                                            ,RO_SEQ
                                                            ,PO_NO
                                                            ,REF_TRANSFER_ID
                                                            ,TRANSACTION_QTY
                                                            ,TRANSACTION_DATE
                                                            ,TRANSACTION_DATE_TIME
                                                            ,TRANSACTION_FLAG
                                                            ,RO_REMARK
                                                            ,CREATE_DATE
                                                            ,CREATE_BY
                                                            ,UPDATE_DATE
                                                            ,UPDATE_BY
                                                        )
                                                    values
                                                        (
                                                            '$roNo'
                                                            ,'$roSeq'
                                                            ,'$poNoPrm'
                                                            ,'$refTransferId'
                                                            ,'$transactionQty'
                                                            ,'$transactionDate'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$transactionFlag'
                                                            ,'$roRemark'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                        )";
                        $conn->query($query_insert_t_ro_detail);
                    } else {
                        /**
                         * UPDATE in EPS_T_RO_DETAIL
                         **/
                        $query_update_t_ro_detail = "update
                                                        EPS_T_RO_DETAIL
                                                    set
                                                        UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        RO_SEQ = '$roSeq'
                                                        and PO_NO = '$poNo'
                                                        and REF_TRANSFER_ID = '$refTransferId'";
                        $conn->query($query_update_t_ro_detail);
                    }
                }

                /**
                 * SELECT EPS_T_RO_DETAIL
                 */
                $query_select_total_t_ro_detail = "select distinct
                                                    isnull(
                                                        (select 
                                                            sum(TRANSACTION_QTY)
                                                         from 
                                                            EPS_T_RO_DETAIL
                                                         where   
                                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'A'
                                                         )
                                                      ,0
                                                    ) as TOTAL_RECEIVED_QTY
                                                    ,isnull(
                                                        (select 
                                                            sum(TRANSACTION_QTY)
                                                         from 
                                                            EPS_T_RO_DETAIL
                                                         where   
                                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'C')
                                                         ,0
                                                    ) as TOTAL_CANCELED_QTY
                                                    ,isnull(
                                                        (select 
                                                            sum(TRANSACTION_QTY)
                                                         from 
                                                            EPS_T_RO_DETAIL
                                                         where   
                                                            EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                            and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                            and EPS_T_RO_DETAIL.TRANSACTION_FLAG = 'O')
                                                         ,0
                                                    ) as TOTAL_OPENED_QTY
                                                    ,EPS_T_PO_DETAIL.QTY
                                                    from
                                                        EPS_T_PO_DETAIL
                                                    left join
                                                        EPS_T_RO_DETAIL
                                                    on
                                                        EPS_T_RO_DETAIL.REF_TRANSFER_ID = EPS_T_PO_DETAIL.REF_TRANSFER_ID
                                                        and EPS_T_RO_DETAIL.PO_NO = EPS_T_PO_DETAIL.PO_NO
                                                    where
                                                        EPS_T_PO_DETAIL.PO_NO = '$poNo'
                                                        and EPS_T_PO_DETAIL.REF_TRANSFER_ID = '$refTransferId'";
                $sql_select_total_t_ro_detail = $conn->query($query_select_total_t_ro_detail);
                $row_select_total_t_ro_detail = $sql_select_total_t_ro_detail->fetch(PDO::FETCH_ASSOC);
                $totalReceivedQty   = $row_select_total_t_ro_detail['TOTAL_RECEIVED_QTY'];
                $totalCanceledQty   = $row_select_total_t_ro_detail['TOTAL_CANCELED_QTY'];
                $totalOpenedQty     = $row_select_total_t_ro_detail['TOTAL_OPENED_QTY'];
                $qty                = $row_select_total_t_ro_detail['QTY'];
                $totalOpenQty       = ($qty - $totalReceivedQty) + $totalCanceledQty + $totalOpenedQty;
                $totalTransaction   = $totalReceivedQty - $totalCanceledQty - $totalOpenedQty;

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
                $qtyTransfer = $row_select_t_transfer['NEW_QTY'];
                $actualQty = $row_select_t_transfer['ACTUAL_QTY'];

                /**
                 * CHECK FOR NON PARTIAL QTY TRANSFER
                 */
                if ($qtyTransfer == $qty) {
                    /**
                     * UPDATE EPS_T_TRANSFER
                     */
                    $query_update_t_transfer = "update
                                                    EPS_T_TRANSFER
                                                set
                                                    ITEM_STATUS = '1310'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    TRANSFER_ID = '$refTransferId'";
                    $conn->query($query_update_t_transfer);

                    /**
                     * UPDATE EPS_T_PO_DETAIL
                     */
                    $query_update_t_po_detail = "update
                                                    EPS_T_PO_DETAIL
                                                 set
                                                    RO_STATUS = '1310'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                  where
                                                    PO_NO = '$poNoPrm'
                                                    and REF_TRANSFER_ID = '$transferIdPrm'";
                    $conn->query($query_update_t_po_detail);

                    /**
                     * UPDATE EPS_T_PO_HEADER
                     */
                    $query_update_t_po_header = "update
                                                    EPS_T_PO_HEADER
                                                 set
                                                    PO_STATUS = '1250'
													,CLOSED_PO_MONTH = NULL
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                 where
                                                    PO_NO = '$poNoPrm' ";
                    $conn->query($query_update_t_po_header);
                }
                /**
                 * CHECK FOR PARTIAL QTY TRANSFER
                 * Update : May 17, 2016 8.37 AM
                 * By : Byan Purbapranidhana
                 */
                else if ($totalTransaction == 0 || ($totalTransaction > 0 && $totalTransaction < $qty)) {
                    /**
                     * UPDATE EPS_T_PO_DETAIL
                     */
                    $query_update_t_po_detail = "update
                                                        EPS_T_PO_DETAIL
                                                    set
                                                        RO_STATUS = '1310'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        PO_NO = '$poNoPrm'
                                                        and REF_TRANSFER_ID = '$transferIdPrm'";
                    $conn->query($query_update_t_po_detail);

                    /**
                     * UPDATE EPS_T_PO_HEADER
                     */
                    $query_update_t_po_header = "update
                                                        EPS_T_PO_HEADER
                                                    set
                                                        PO_STATUS = '1250'
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        PO_NO = '$poNoPrm' ";
                    $conn->query($query_update_t_po_header);
                } else {
                    /**
                     * UPDATE EPS_T_TRANSFER
                     */
                    $query_update_t_transfer = "update
                                                        EPS_T_TRANSFER
                                                    set
                                                        UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        TRANSFER_ID = '$refTransferId'";
                    $conn->query($query_update_t_transfer);

                    /**
                     * UPDATE EPS_T_PO_DETAIL
                     */
                    $query_update_t_po_detail = "update
                                                    EPS_T_PO_DETAIL
                                                set
                                                    UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    PO_NO = '$poNoPrm'
                                                    and REF_TRANSFER_ID = '$transferIdPrm'";
                    $conn->query($query_update_t_po_detail);

                    /**
                     * UPDATE EPS_T_PO_HEADER
                     */
                    $query_update_t_po_header = "update
                                                    EPS_T_PO_HEADER
                                                set
                                                    UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    PO_NO = '$poNoPrm' ";
                    $conn->query($query_update_t_po_header);
                }

                $msg = 'Success';
            } else {
                $msg = 'Mandatory_1';
            }
        }

        if ($action == 'OpenReceiving') {
            $currentDate       = date('Ymd');
            $poNoPrm           = strtoupper(trim($_GET['poNoPrm']));
            $transferIdPrm     = strtoupper(trim($_GET['transferIdValPrm']));
            $updateDatePrm     = strtoupper(trim($_GET['updateDatePrm']));
            $itemReceived      = array();
            $itemReceivedTemp  = array();
            $itemReceived      = ($_SESSION['roDetail']);

            /**
             * SELECT EPS_T_PO_HEADER
             */
            $query_eps_t_po_header = "select
                                            UPDATE_DATE,
                                            UPDATE_BY
                                        from 
                                            EPS_T_PO_HEADER
                                        where
                                            PO_NO = '$poNoPrm'";
            $sql_eps_t_po_header = $conn->query($query_eps_t_po_header);
            $row_eps_t_po_header = $sql_eps_t_po_header->fetch(PDO::FETCH_ASSOC);
            $newUpdateDate = strtoupper($row_eps_t_po_header['UPDATE_DATE']);
            $updateBy = strtoupper($row_eps_t_po_header['UPDATE_BY']);

            if ($updateDatePrm == $newUpdateDate) {
                unset($_SESSION['roStatus']);

                /**
                 * INSERT into EPS_T_RO_DETAIL
                 */
                for ($i = 0; $i < count($itemReceived); $i++) {
                    $roNo           = $itemReceived[$i]['roNo'];
                    $roSeq          = $itemReceived[$i]['roSeq'];
                    $poNo           = $itemReceived[$i]['poNo'];
                    $refTransferId  = $itemReceived[$i]['refTransferId'];
                    $transactionQty = $itemReceived[$i]['transactionQty'];
                    $transactionFlag = $itemReceived[$i]['transactionFlag'];
                    $roRemark       = $itemReceived[$i]['roRemark'];
                    $transactionDate = encodeDate($itemReceived[$i]['transactionDate']);

                    // Define RO NO
                    $query2 = "select 
                                    count(*) as RO_COUNT 
                            from 
                                    EPS_T_RO_DETAIL 
                            where 
                                    substring(RO_NO, 1, 8) = '$currentDate'";
                    $sql2 = $conn->query($query2);
                    $row2 = $sql2->fetch(PDO::FETCH_ASSOC);
                    $roCount = $row2['RO_COUNT'];

                    if ($roCount == 0) {
                        $sequences = '1';
                    } else {
                        $sequences = $roCount + 1;
                    }
                    //$roNo = $currentDate.'RO'.$sequences;
                    $sequencesNo = str_pad($sequences, 4, "0", STR_PAD_LEFT);
                    $roNo = $currentDate . trim($sUserId) . 'RO' . $sequencesNo;

                    /**
                     * SELECT from EPS_T_RO_DETAIL
                     */
                    $query_select_t_ro_detail = "select
                                                    RO_SEQ
                                                    ,PO_NO
                                                    ,REF_TRANSFER_ID
                                                from
                                                    EPS_T_RO_DETAIL
                                                where
                                                    RO_SEQ = '$roSeq'
                                                    and PO_NO = '$poNo'
                                                    and REF_TRANSFER_ID = '$refTransferId'";
                    $sql_select_t_ro_detail = $conn->query($query_select_t_ro_detail);
                    $row_select_t_ro_detail = $sql_select_t_ro_detail->fetch(PDO::FETCH_ASSOC);

                    if (!$row_select_t_ro_detail) {
                        /**
                         * INSERT in EPS_T_RO_DETAIL
                         **/
                        $query_insert_t_ro_detail = "insert into
                                                        EPS_T_RO_DETAIL
                                                        (
                                                            RO_NO
                                                            ,RO_SEQ
                                                            ,PO_NO
                                                            ,REF_TRANSFER_ID
                                                            ,TRANSACTION_QTY
                                                            ,TRANSACTION_DATE
                                                            ,TRANSACTION_DATE_TIME
                                                            ,TRANSACTION_FLAG
                                                            ,RO_REMARK
                                                            ,CREATE_DATE
                                                            ,CREATE_BY
                                                            ,UPDATE_DATE
                                                            ,UPDATE_BY
                                                        )
                                                    values
                                                        (
                                                            '$roNo'
                                                            ,'$roSeq'
                                                            ,'$poNo'
                                                            ,'$refTransferId'
                                                            ,'$transactionQty'
                                                            ,'$transactionDate'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$transactionFlag'
                                                            ,'$roRemark'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                            ,convert(VARCHAR(24), GETDATE(), 120)
                                                            ,'$sUserId'
                                                        )";
                        $conn->query($query_insert_t_ro_detail);
                    } else {
                        $query_update_t_ro_detail = "update
                                                        EPS_T_RO_DETAIL
                                                    set
                                                        UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                        ,UPDATE_BY = '$sUserId'
                                                    where
                                                        RO_SEQ = '$roSeq'
                                                        and PO_NO = '$poNo'
                                                        and REF_TRANSFER_ID = '$refTransferId'";
                        $conn->query($query_update_t_ro_detail);
                    }
                }

                if (!$row_select_t_ro_detail) {
                    /**
                     * UPDATE EPS_T_PO_DETAIL (** Open Item)
                     */
                    $roStatus = '1310';
                    $query_update_eps_t_po_detail = "update 
                                                        EPS_T_PO_DETAIL
                                                     set
                                                        RO_STATUS = '$roStatus'
                                                        ,UPDATE_BY = '$sUserId' 
                                                        ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                     where
                                                        PO_NO = '$poNoPrm'
                                                        and REF_TRANSFER_ID = '$transferIdPrm'";
                    $conn->query($query_update_eps_t_po_detail);

                    /**
                     * SELECT EPS_T_TRANSFER
                     **/
                    $query_select_t_transfer = "select
                                                    NEW_QTY
                                                    ,ACTUAL_QTY
                                                    ,PR_NO
                                                    ,NEW_ITEM_NAME
                                                from
                                                    EPS_T_TRANSFER
                                                where
                                                    TRANSFER_ID = '$refTransferId'";
                    $sql_select_t_transfer = $conn->query($query_select_t_transfer);
                    $row_select_t_transfer = $sql_select_t_transfer->fetch(PDO::FETCH_ASSOC);
                    $qtyTransfer = $row_select_t_transfer['NEW_QTY'];
                    $actualQty = $row_select_t_transfer['ACTUAL_QTY'];
                    $prNo = $row_select_t_transfer['PR_NO'];
                    $newItemName = $row_select_t_transfer['NEW_ITEM_NAME'];

                    /**
                     * SELECT EPS_T_PO_DETAIL
                     **/
                    $query_select_t_po_detail = "select
                                                    QTY
                                                from
                                                    EPS_T_PO_DETAIL
                                                where
                                                    PO_NO = '$poNo'
                                                    and REF_TRANSFER_ID = '$refTransferId'";
                    $sql_select_t_po_detail = $conn->query($query_select_t_po_detail);
                    $row_select_t_po_detail = $sql_select_t_po_detail->fetch(PDO::FETCH_ASSOC);
                    $poQty = $row_select_t_po_detail['QTY'];

                    if ($poQty == $qtyTransfer) {
                        $roStatus = '1310';

                        /**
                         * UPDATE EPS_T_TRANSFER (** Open Item)
                         */
                        $query_update_eps_t_transfer = "update 
															EPS_T_TRANSFER
														set
															ITEM_STATUS = '$roStatus'
															,UPDATE_BY = '$sUserId' 
															,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
														where
															TRANSFER_ID = '$transferIdPrm'";
                        $conn->query($query_update_eps_t_transfer);
                    }

                    /**
                     * UPDATE EPS_T_PO_HEADER
                     */
                    $query_update_t_po_header = "update
                                                    EPS_T_PO_HEADER
                                                set
                                                    PO_STATUS = '1250'
                                                    ,UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                    ,UPDATE_BY = '$sUserId'
                                                where
                                                    PO_NO = '$poNoPrm' ";
                    $conn->query($query_update_t_po_header);

                    /**********************************************************************
                     * SEND MAIL
                     **********************************************************************/
                    $mailFrom       = $sInet;
                    $mailFromName   = $sNotes;

                    /**
                     * TO RECEIVING IN CHARGE
                     **/
                    $query_m_dscid = "select 
                                        EPS_M_DSCID.INETML
                                        ,EPS_M_USER.PASSWORD 
                                    from 
                                        EPS_M_DSCID 
                                    inner join 
                                        EPS_M_USER 
                                    on 
                                        ltrim(EPS_M_DSCID.INOPOK) = ltrim(EPS_M_USER.NPK) 
                                    where  
                                        ltrim(ltrim(EPS_M_DSCID.INOPOK)) = ltrim('" . $updateBy . "')";
                    $sql_m_dscid = $conn->query($query_m_dscid);
                    $row_m_dscid = $sql_m_dscid->fetch(PDO::FETCH_ASSOC);
                    if ($row_m_dscid) {
                        $mailTo    = $row_m_dscid['INETML'];
                        //$mailTo      = 'muh.iqbal@taci.toyota-industries.com';
                        $password  = $row_m_dscid['PASSWORD'];
                        $getParamLink  = paramEncrypt("action=open&poNo=$poNo&userId=$updateBy&password=$password");
                        $mailSubject = "[EPS] ITEM RE-OPEN (by Procurement). PO No: " . $poNo;
                        $mailMessage = "<table style='font-family: Arial; font-size: 12px;'>";
                        $mailMessage .= "<tr><td>PO No</td><td>: </td><td>" . $poNo . "</td></tr>";
                        $mailMessage .= "<tr><td>PR No</td><td>:</td><td>" . $prNo . "</td></tr>";
                        $mailMessage .= "<tr><td>Item Name</td><td>:</td><td>" . $newItemName . "</td></tr>";
                        $mailMessage .= "<tr><td>Status</td><td>:</td><td> Reopened by " . trim($sNama) . "</td></tr>";
                        $mailMessage .= "<tr><td>Reason</td><td>:</td><td>" . $roRemark . "</td></tr>";
                        $mailMessage .= "</table>";
                        //$mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage
                        //roSendMailReopened($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage);
                    }
                }

                /**
                 * UPDATE EPS_T_PO_DETAIL
                 */
                $query_update_t_ro_header = "update
                                                EPS_T_PO_DETAIL
                                             set
                                                UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                             where
                                                PO_NO = '$poNoPrm'
                                                and REF_TRANSFER_ID = '$transferIdPrm'";
                $conn->query($query_update_t_ro_header);

                /**
                 * UPDATE EPS_T_PO_HEADER
                 */
                $query_update_t_po_header = "update
                                                EPS_T_PO_HEADER
                                             set
                                                UPDATE_DATE = convert(VARCHAR(24), GETDATE(), 120)
                                                ,UPDATE_BY = '$sUserId'
                                             where
                                                PO_NO = '$poNoPrm' ";
                $conn->query($query_update_t_po_header);

                $msg = 'Success';
            } else {
                $msg = 'Mandatory_1';
            }
        }
    } else {
        $msg = 'SessionExpired';
    }
    echo $msg;
} else {
?>
    <script language="javascript">
        alert("Sorry, you are not authorized to access this page.");
        document.location = "../db/Login/Logout.php";
    </script>
<?
}
