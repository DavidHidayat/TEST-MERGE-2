<?php
function sendMail ($prNo, $mailApprover, $requesterMail, $requesterMailName, $getParamApprover, $mailMessage){
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    //$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = false;                  // enable SMTP authentication
    $mail->Port       = 25;                    // set the SMTP server port
//    $mail->Host       = "172.31.1.240"; // SMTP server
//    $mail->Username   = "srinindito@denco.co.id";     // SMTP server username
    $mail->Host       = "gw117223.fortimail.com"; // SMTP server
    $mail->Username   = "https://e-mail.ap.toyota-industries.com";     // SMTP server username
    //$mail->Password   = "password";            // SMTP server password

    //$mail->IsSendmail();  // tell the class to use Sendmail

    $mail->AddReplyTo($requesterMail,"First Last");

    $mail->From       = $requesterMail;
    $mail->FromName   = $requesterMailName;

    $to = $mailApprover;
    
    $mail->AddAddress($to);
	// Not allowed Lotus Notes to forward
    $mail->addCustomHeader("Sensitivity:1");
            
    $mail->Subject  = "PR No: [".$prNo."] ".$mailMessage;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "";
    $body = $body . "<font face=Century Gothic size=-1>Purchase Requisition No: [".$prNo."] ".$mailMessage;
    $body = $body . "<br>Please follow the link below :<br>";  
    $body = $body . "<a href='http://".$_SERVER['HTTP_HOST']."/EPS/db/Login/Mail_Login.php?".$getParamApprover."'>"."Click to open the Document"."</a></font>";
    $body = $body . "<br><font face='Trebuchet MS' size='-2'><i> (** This link contains your user id and password to EPS, please do not forward this email. If you want to forward this email, please remove this link first before send)</i></font>";
    
    $mail->MsgHTML($body);

    $mail->IsHTML(true); // send as HTML

    $mail->Send();
    if(!$mail){
        //echo 'Mailer error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Message has been sent.';
    }
}
function sendMailNewPassword ($userMail, $senderMail, $senderMailName, $userName, $userId, $newPassword){
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

    $mail->From       = $senderMail;
    $mail->FromName   = $senderMailName;

    $to = $userMail;

    $mail->AddAddress($to);
    $mail->addCustomHeader("Sensitivity:1");

    $mail->Subject  = "EPS password reset" ;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 80; // set word wrap

    $body = "<font face=Calibri size=-1>Dear ".$userName.",<br><br>";
    $body = $body ."Your EPS password for user id ".$userId." was reset using the email address <font color=blue>".$to."</font>.";
    $body = $body ."<br><br>User Id: ".$userId;
    $body = $body ."<br>New password: ".$newPassword;
    $body = $body ."<br><br><br><br>Thanks,<br>Administator EPS</font>" ; 

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
