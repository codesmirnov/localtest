<?

class Mailer {
  
  var $from    = '';
  var $to      = '';
  var $subject = '';
  
  function send($message = '') {
    $headers =
      'From: ' . $this->from . "\r\n" .
      'Content-Type: text/html; charset=utf-8' . "\r\n";
    
    return mail($this->to, $this->subject, $message, $headers);
  }
}
	
?>