<?php

function resetPasswordMail ($userMail, $senderMail, $senderMailName, $userName, $userId, $newPassword){
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

    $mail->AddReplyTo($senderMail,"First Last");

    $mail->From       = trim($senderMail);
    $mail->FromName   = $senderMailName;

    $to = $userMail;
    //$to = "muh.iqbal@taci.toyota-industries.com";
    
    $mail->AddAddress($to);

    $mail->Subject  = "EPS password reset" ;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "<font face=Calibri size=-1>Dear ".$userName.",<br><br>";
    $body = $body ."Your EPS password for User ID ".$userId." was reset using the email address <font color=blue>".$to."</font>.";
    $body = $body ."<br><br>User ID: ".$userId;
    $body = $body ."<br>New password: ".$newPassword;
    $body = $body ."<br><br><br><br>Thanks,<br>EPS Administator</font>" ; 

    //$body= $body . "<a href='http://".$_SERVER['HTTP_HOST']."/extjs-project/procurement-PP/db/dologin.php?".$getparm. "'>"."Click to open the Document"."</a>";

    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
        //echo '{success:true, msg:'.json_encode(array('message'=>'yesmail')).'}';
    }
}

function manualReportMail($mailTo,$mailFrom,$mailFromName,$mailSubject,$mailMessage)
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
    $to = "muh.iqbal@taci.toyota-industries.com";
    $trimTo = trim($to);
    $mail->AddAddress($trimTo);

    $mail->Subject  = $mailSubject ;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = $mailMessage ; 

    //$body= $body . "<a href='http://".$_SERVER['HTTP_HOST']."/extjs-project/procurement-PP/db/dologin.php?".$getparm. "'>"."Click to open the Document"."</a>";

    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
        //echo '{success:true, msg:'.json_encode(array('message'=>'yesmail')).'}';
    }
}
?>
