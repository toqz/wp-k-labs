<?php

/*
 * class for holding and validating data captured from the contact form
*/

class cscf_Contact
{
    var $Name;
    var $Email;
    var $ConfirmEmail;
    var $Message;
    var $ErrorMessage;
    var $RecaptchaPublicKey;
    var $RecaptchaPrivateKey;
    var $Errors;
    
    function __construct() 
    {
        $this->Errors = array();
        
        if (cscf_PluginSettings::UseRecaptcha()) 
        {
            $this->RecaptchaPublicKey = cscf_PluginSettings::PublicKey();
            $this->RecaptchaPrivateKey = cscf_PluginSettings::PrivateKey();
        }
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cscf']) ) 
        {
            $cscf = $_POST['cscf'];
            $this->Name = filter_var($cscf['name'], FILTER_SANITIZE_STRING);
            $this->Email = filter_var($cscf['email'], FILTER_SANITIZE_EMAIL);
            $this->ConfirmEmail = filter_var($cscf['confirm-email'], FILTER_SANITIZE_EMAIL);
            $this->Message = filter_var($cscf['message'], FILTER_SANITIZE_STRING);
            unset($_POST['cscf']);
        }
    }
    
    function IsValid() 
    {
        $this->Errors = array();
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        return false;

        //check nonce
        
        if (!wp_verify_nonce($_POST['cscf_nonce'], 'cscf_contact')) 
        return false;

        // email and confirm email are the same
        
        if ($this->Email != $this->ConfirmEmail) $this->Errors['confirm-email'] = __('Sorry the email addresses do not match.','cleanandsimple');

        //email
        
        if (strlen($this->Email) == 0) $this->Errors['email'] = __('Please give your email address.','cleanandsimple');

        //confirm email
        
        if (strlen($this->ConfirmEmail) == 0) $this->Errors['confirm-email'] = __('Please confirm your email address.','cleanandsimple');

        //name
        
        if (strlen($this->Name) == 0) $this->Errors['name'] = __('Please give your name.','cleanandsimple');

        //message
        
        if (strlen($this->Message) == 0) $this->Errors['message'] = __('Please enter a message.','cleanandsimple');

        //email invalid address
        
        if (strlen($this->Email) > 0 && !filter_var($this->Email, FILTER_VALIDATE_EMAIL)) $this->Errors['email'] = __('Please enter a valid email address.','cleanandsimple');

        //check recaptcha but only if we have keys
        
        if ($this->RecaptchaPublicKey <> '' && $this->RecaptchaPrivateKey <> '') 
        {
            $resp = recaptcha_check_answer($this->RecaptchaPrivateKey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            
            if (!$resp->is_valid) $this->Errors['recaptcha'] = __('Sorry the code wasn\'t entered correctly please try again.','cleanandsimple');
        }
        
        return count($this->Errors) == 0;
    }
}

