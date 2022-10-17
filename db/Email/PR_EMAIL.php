<?php

function prSendMail ($prNo, $mailTo, $mailFrom, $mailFromName, $getParamLink, $mailSubject, $mailMessage){
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
    $mail->FromName   = trim($mailFromName);
//    $mail->From       = "taufik.hidayat@taci.toyota-industries.com";
//    $mail->FromName   = "TAUFIK HIDAYAT";

    $to = trim($mailTo);
    //$to = "muh.iqbal@taci.toyota-industries.com";
    //$to = "iqbal.oyonz@gmail.com";
    
    $mail->AddAddress($to);
    $mail->addCustomHeader("Sensitivity:1");
    
    $cc = "ahmadjafar@taci.toyota-industries.com";
    //$mail->AddBCC($cc);
    
    $mail->Subject  = $mailSubject;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . $mailMessage;
    $body = $body . "<font face='Trebuchet MS' size='-1'><br>Please follow the link below :<br>";  
    $body = $body . "<a href='http://".$_SERVER['HTTP_HOST']."/EPS/db/Login/Mail_Login.php?".$getParamLink."'>"."Click to open the Document"."</a></font>";
    $body = $body . "<font face='Trebuchet MS' size='-2'><i> (** This link contains your user id and password to EPS, please do not forward this email for your secure. If you want to forward this email, please remove this link first before send.)</i></font>";
    
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
