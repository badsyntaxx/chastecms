<?php 

/**
 * Mail Library
 *
 * Mail library requires the phpmailer library and is used to send emails
 * from the site.
 */
class Mail
{
    /**
     * Send email using phpmailer.
     * 
     * @param array - $email - To and from data to send the email.
     * @return bool - Return true if email is sent and false if not.             
     */
    public function sendMail($email)
    {
        require_once PLUGINS_DIR . '/phpmailer/PHPMailerAutoload.php';
        $phpmailer = new PHPMailer();
        $settings = new SettingsModel();
        $mail = $settings->getMailSettings();

        $phpmailer->SMTPDebug = 2;                       // Enable verbose debug output
        $phpmailer->isSMTP();                            // Set mailer to use SMTP
        $phpmailer->Host = $mail['host'];                // Specify main and backup SMTP servers
        $phpmailer->SMTPAuth = true;                     // Enable SMTP authentication
        $phpmailer->Username = $mail['username'];        // SMTP username
        $phpmailer->Password = $mail['password'];        // SMTP password
        $phpmailer->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
        $phpmailer->Port = $mail['port'];                // TCP port to connect to
        $phpmailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $phpmailer->SetFrom($mail['username'], $settings->getSetting('sitename'));
        $phpmailer->AddAddress($email['to']);             // Add a recipient
        $phpmailer->addReplyTo($email['from']);
        $phpmailer->isHTML(true);                         // Set email format to HTML
        $phpmailer->Subject = $email['subject'];
        $phpmailer->Body = $email['body'];
        $phpmailer->AltBody = $email['message'];

        if ($phpmailer->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a template file to use when sending email.
     * 
     * @param string - $file - The name of the template file to get.
     * @return string - The text from the template file.
     */
    public function getTemplate($file)
    {
        $file = ROOT_DIR . '/storage/templates/email/' . $file . '.txt';
        ob_start();
        require ($file);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}