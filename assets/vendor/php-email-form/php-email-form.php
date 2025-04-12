<?php

class PHP_Email_Form {
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $ajax = false;
    public $smtp = array();
    
    private $messages = array();
    
    // Validation error messages
    public $invalid_to_email = 'Email to (receiving email address) is empty or invalid!';
    public $invalid_from_name = 'From Name is empty!';
    public $invalid_from_email = 'Email from: is empty or invalid!';
    public $invalid_subject = 'Subject is too short or empty!';
    public $short = 'is too short or empty!'; // Use this for validation of message lengths
    public $ajax_error = 'Sorry, the request should be an Ajax POST';

    // Method to add message
    public function add_message($message_text, $label, $length_check = 0) {
        if (strlen($message_text) < $length_check) {
            return $this->short . ' ' . $label;
        }
        $this->messages[] = $label . ': ' . $message_text;
        return true;
    }
    
    // Validate email addresses
    private function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Send function
    public function send() {
        // Validate required fields
        if (empty($this->to) || !$this->is_valid_email($this->to)) {
            return $this->invalid_to_email;
        }
        if (empty($this->from_name)) {
            return $this->invalid_from_name;
        }
        if (empty($this->from_email) || !$this->is_valid_email($this->from_email)) {
            return $this->invalid_from_email;
        }
        if (empty($this->subject)) {
            return $this->invalid_subject;
        }

        // Prepare the email message
        $message = implode("\n", $this->messages);
        $headers = "From: $this->from_name <$this->from_email>\r\n";
        $headers .= "Reply-To: $this->from_email\r\n";

        // Send the email using PHP's mail function
        if (mail($this->to, $this->subject, $message, $headers)) {
            return 'OK';
        } else {
            return 'Mail could not be sent. Please check your PHP mail configuration.';
        }
    }
}

?>