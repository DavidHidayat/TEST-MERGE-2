<?php
function cnSendMailToSupplier ($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCCs, $filename, $fileLocation){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = $mailFromName;

    //$to = $mailTo;
    $mailTo = explode(',', $mailTos);
    foreach ($mailTo as $value) {
        $to = $value;
        //$to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($to);
    }
    //$to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($to);
    $mailCc = explode(',', $mailCCs);
    foreach ($mailCc as $value) {
        $cc = $value;
        $mail->AddCC($cc);
    }
    
    $path="../REPORT/CN/PDF/".$fileLocation."/".$filename.".pdf";
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

function cnSendMailToInCharge($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage, $mailCCs, $filename, $currentCnMonth){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "cluster1.us.messagelabs.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = $mailFromName;

    //$to = $mailTo;
    $mailTo = explode(',', $mailTos);
    foreach ($mailTo as $value) {
        $to = $value;
        //$to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($to);
    }
    //$to = "muh.iqbal@taci.toyota-industries.com";
        //$mail->AddAddress($to);
    $mailCc = explode(',', $mailCCs);
    foreach ($mailCc as $value) {
        $cc = $value;
        $mail->AddCC($cc);
    }
    
    $path="../REPORT/CN/EXCEL/".$currentCnMonth."/".$filename.".xlsx";
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

function cnProcessSendMail ($mailTos, $mailFrom, $mailFromName, $mailSubject, $mailMessage)
{
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
    $mail->Host       = "cluster1.us.messagelabs.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($mailFrom,"First Last");
    
    $mail->From       = trim($mailFrom);
    $mail->FromName   = $mailFromName;

    //$to = $mailTo;
    //$mailTo = explode(',', $mailTos);
//    foreach ($mailTo as $value) {
//        //$to = $value;
//        $to = "muh.iqbal@taci.toyota-industries.com";
//        $mail->AddAddress($to);
//    }
    $to = "muh.iqbal@taci.toyota-industries.com";
        $mail->AddAddress($to);
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
