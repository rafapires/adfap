<?php
class messengerCsp extends moduleCsp {
    public function init() {
        // Add filters to replace the mail from name and emailaddress
        add_filter('wp_mail_from',array($this,'mailSendFrom'));
        add_filter('wp_mail_from_name',array($this,'mailSendFromName'));
        add_filter('wp_mail_content_type', array($this, 'mailContentType'));
        add_action('phpmailer_init',array($this,'phpmailerInit'));
        parent::init();
    }
    public function mailContentType($content_type) {
        $content_type = 'text/html';
        return $content_type;
    }
    /**
     *
     * @param object $phpmailer
     * @return object 
     */
    public function phpmailerInit($phpmailer) {
        $items = $this->getParams();
        $params = $items[0];
        if (empty($params)) {
            return;
        }
        // Check that mailer is not blank, and if mailer=smtp, host is not blank
        if ( ! $params->mailer || ( $params->mailer == 'smtp' && ! $params->smtp_host ) ) {
                return;
        }

        // Set the mailer type as per config above, this overrides the already called isMail method
        $phpmailer->Mailer = $params->mailer;

        // Set the Sender (return-path) if required
        if ($params->mail_set_return_path)
                $phpmailer->Sender = $phpmailer->From;

        // Set the SMTPSecure value, if set to none, leave this blank
        $phpmailer->SMTPSecure = $params->smtp_ssl == 'none' ? '' : $params->smtp_ssl;

        // If we're sending via SMTP, set the host
        if ($params->mailer == "smtp") {

                // Set the SMTPSecure value, if set to none, leave this blank
                $phpmailer->SMTPSecure = $params->smtp_ssl == 'none' ? '' : $params->smtp_ssl;

                // Set the other options
                $phpmailer->Host = $params->smtp_host;
                $phpmailer->Port = $params->smtp_port;

                // If we're using smtp auth, set the username & password
                if ($params->smtp_auth) {
                        $phpmailer->SMTPAuth = TRUE;
                        $phpmailer->Username = $params->smtp_user;
                        $phpmailer->Password = $params->smtp_pass;
                }
        }
        
      // die(print_r($phpmailer));
        //return $phpmailer;
    }
    /**
     * Change the Send From Address
     * @return string
     */
    public function mailSendFrom($orig) {
       	$items = $this->getParams();
        $params = $items[0];
        // Get the site domain and get rid of www.
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	$default_from = 'wordpress@' . $sitename;
	
	// If the from email is not the default, return it unchanged
	if ( $orig != $default_from ) {
		return $orig;
	}
	
	if (defined('WPMS_ON') && WPMS_ON)
		return WPMS_MAIL_FROM;
	elseif (is_email($params->mail_from, false))
		return $params->mail_from;
	
	// If in doubt, return the original value
	return $orig; 
    }
    /**
     * Change the Send From Name
     * @param string $orig
     * @return string 
     */
    public function mailSendFromName($orig) {
       	$items = $this->getParams();
        $params = $items[0];
        // Only filter if the from name is the default
	if ($orig == 'WordPress') {
		if (defined('WPMS_ON') && WPMS_ON)
			return WPMS_MAIL_FROM_NAME;
		elseif ( $params->mail_from_name != "" && is_string($params->mail_from_name) )
			return $params->mail_from_name;
	}
	
	// If in doubt, return the original value
	return $orig;
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tabCsp(langCsp::_('Notifications'), $this->getCode());
        $tab->setView('messengerTab');
        $tab->setSortOrder(10);
        $tabs[] = $tab;
        return $tabs;
    }
	
	public function send($to, $from, $fromName, $module, $template, $variables) {
		$template = frameCsp::_()->getModule('messenger')->getModel('email_templates')->getTemplate($module, $template);
        
        $template->renderContent($variables);

        $subject = $template->getSubject();
        $message = $template->getMessage();
		
		$headers = '';
        if(!empty($fromName) && !empty($from)) {
            $headers = 'From: '. $fromName. ' <'. $from. '>' . "\r\n";
        }
		if(!function_exists('wp_mail'))
			frameCsp::_()->loadPlugins();
        $result = wp_mail($to, $subject, $message, $headers);
        frameCsp::_()->getModule('log')->getModel()->post(array(
            'type' => 'email',
            'data' => array(
                'to' => $to,
                'subject' => $subject,
                'headers' => htmlspecialchars($headers),
                'message' => $message,
                'result' => $result ? CSP_SUCCESS : CSP_FAILED,
            ),
        ));
	}
}

