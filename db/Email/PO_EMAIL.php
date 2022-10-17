<?php

function poSendMail ($poNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    //$mail->Host       = "216.82.241.243"; // SMTP server
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
//    $mail->Host       = "172.31.1.240"; // SMTP server
//    $mail->Username   = "srinindito@denco.co.id";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = trim($mailFromName);

    $to = $mailTo;
    
    //$to = "muh.iqbal@taci.toyota-industries.com";
    
    $mail->AddAddress($to);
    $mail->addCustomHeader("Sensitivity:1");
    
    $bcc = "muh.iqbal@taci.toyota-industries.com";
    $mail->AddBCC($bcc);
    
    $mail->Subject  = $mailSubject;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . $mailMessage;
    $body = $body . "<font face='Trebuchet MS' size='-1'><br>Please follow the link below :<br>";  
    $body = $body . "<a href='http://".$_SERVER['HTTP_HOST']."/EPS/db/Login/PO_MAIL_LOGIN.php?".$getParamLink."'>"."Click to open the Document"."</a></font>";
    $body = $body . "<br><font face='Trebuchet MS' size='-2'><i> (** This link contains your user id and password to EPS, please do not forward this email. If you want to forward this email, please remove this link first before send.)</i></font>";
    
    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
    }
}


function poSendMailToSupplier ($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCCs, $poNo){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
//    $mail->Host       = "172.31.1.240"; // SMTP server
//    $mail->Username   = "srinindito@denco.co.id";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = trim($mailFromName);

    //$to = $mailTo;
    //$mail->AddAddress($to);
    //$mailTos1 = "muh.iqbal@taci.toyota-industries.com, ahmadjafar@taci.toyota-industries.com";
    $mailTo = explode(',', $mailTos);
    //$mailTo ="muh.iqbal@taci.toyota-industries.com";
    foreach ($mailTo as $value) {
        $to = trim($value);
        //$to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($to);
    }
    //$mail->AddAddress($mailTo);
    $mailCc = explode(',', $mailCCs);
    foreach ($mailCc as $value) {
        $cc = $value;
        $mail->AddCC($cc);
    }
    //$mail->AddCC("muh.iqbal@taci.toyota-industries.com");
    $path="../CREATE_FILE/PDF_FILE/".$poNo.".pdf";
    if(!preg_match("/CANCELED/",$mailSubject) && !preg_match("/CHANGE DUE DATE/",$mailSubject)
             && !preg_match("/CHANGE CLOSING MONTH/",$mailSubject) && !preg_match("/COMPLETED (NOT CREATED CN)/",$mailSubject) ) {
        $mail->AddAttachment($path);
    }
	
    $mail->Subject  = $mailSubject;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . "<font face='Trebuchet MS' size='-1'>".$mailMessage."</font>";
    
    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
    }
}

function outstandingPoSendMailToSupplier ($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCCs, $mailBccs, $fileName){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
   // $mail->Host       = "172.31.1.240"; // SMTP server
   // $mail->Username   = "srinindito@denco.co.id";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = trim($mailFromName);

    //$to = $mailTo;
    //$mail->AddAddress($to);
    $mailTo = explode(',', $mailTos);
    foreach ($mailTo as $value) {
        $to = $value;
        //$mail->AddAddress($to);
    }
	
    $mailCc = explode(',', $mailCCs);
    foreach ($mailCc as $value) {
        $cc = $value;
        $mail->AddCC($cc);
    }
    
	$mailBcc = explode(',', $mailBccs);
    foreach ($mailBcc as $value) {
        $bcc = $value;
        $mail->AddBCC($bcc);
    }
    //$path="../CREATE_FILE/EXCEL_FILE/".$fileName.".xls";
    $path="../REPORT/OUTSTANDING_PO/".$fileName.".xlsx";
    $mail->AddAttachment($path);
    
    $mail->Subject  = $mailSubject;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . "<font face='Trebuchet MS' size='-1'>".$mailMessage."</font>";
    
    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
    }
}
function delayDeliverySendMailToSupplier ($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCCs)
{
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
//    $mail->Host       = "172.31.1.240"; // SMTP server
//    $mail->Username   = "srinindito@denco.co.id";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = trim($mailFromName);

    $to = $mailTos;
	$mailTo = explode(',', $to);
	foreach ($mailTo as $mailTos) {
            //$to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($mailTos);
        //$mail->AddAddress($to);
    }
    
    
    $mailCc = explode(',', $mailCCs);
    foreach ($mailCc as $value) {
        $cc = $value;
        $mail->AddCC($cc);
    }
    
    $mail->Subject  = $mailSubject;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . "<font face='Trebuchet MS' size='-1'>".$mailMessage."</font>";
    
    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
    }
}


?>
