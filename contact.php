<?php

require 'vendor/autoload.php';

if(!$_POST) exit;

// Email address verification, do not edit.
function isEmail($email) {
	return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));
}

if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");

$name     = $_POST['name'];
$email    = $_POST['email'];
$comments = $_POST['comments'];
$verify   = $_POST['verify'];

if(trim($name) == '') {
	echo '<div class="error_message">Attention! You must enter your name.</div>';
	exit();
} else if(trim($email) == '') {
	echo '<div class="error_message">Attention! Please enter a valid email address.</div>';
	exit();
} else if(!isEmail($email)) {
	echo '<div class="error_message">Attention! You have enter an invalid e-mail address, try again.</div>';
	exit();
}else if(trim($comments) == '') {
	echo '<div class="error_message">Attention! Please enter your message.</div>';
	exit();
}else if(!isset($verify) || trim($verify) == '') {
	echo '<div class="error_message">Attention! Please enter the verification number.</div>';
	exit();
} else if(trim($verify) != '4') {
	echo '<div class="error_message">Attention! The verification number you entered is incorrect.</div>';
	exit();
}


$apiKey = getenv('SENDGRID_API_KEY'); // Remplacez par votre clé API SendGrid
$sendgrid = new \SendGrid($apiKey);

$comments = htmlspecialchars($comments);


$address = "contact@nuit709.com";
$e_subject = 'You\'ve been contacted by ' . $name . '.';
$e_body = "You have been contacted by $name, their additional message is as follows." . PHP_EOL . PHP_EOL;
$e_content = "\"$comments\"" . PHP_EOL . PHP_EOL;
$e_reply = "You can contact $name via email, $email";
$msg = wordwrap($e_body . $e_content . $e_reply, 70);

$email = new \SendGrid\Mail\Mail();
$email->setFrom($address, $name);
$email->setSubject($e_subject);
$email->addTo($address);
$email->addContent("text/plain", $msg);

// Envoyer l'e-mail
$response = $sendgrid->send($email);

if ($response->statusCode() == 202) {
    // E-mail envoyé avec succès
    echo "<fieldset>";
    echo "<div id='success_page'>";
    echo "<h3>Email Sent Successfully.</h3>";
    echo "<p>Thank you <strong>$name</strong>, your message has been submitted to us.</p>";
    echo "</div>";
    echo "</fieldset>";
} else {
    // Erreur lors de l'envoi de l'e-mail
    echo 'ERROR!';
}


?>