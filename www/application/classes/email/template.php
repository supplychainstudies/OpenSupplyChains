<?
class Email_Template {
    
    public static function send_email($to, $subject, $body) {
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: Sourcemap Team <smita@sourcemap.org>' . "\r\n";
	
	mail($to, $subject, $body, $headers);
    }

}

?>