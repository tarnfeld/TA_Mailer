<?php

/*
*
*	TA_Mailer - Taking the stress out of templated emails
*	(c) 2010  - Don't steal my code!
*
*	URL: tarnfeld.com
*
*/

class TA_Mailer
{

	/**
	*
	*	Public instance vars
	*
	*	@scope public
	*
	**/
	public $_repipients = array();
	public $_cc = array();
	public $_bcc = array();
	
	public $_from = array();
	public $_replyto;
	
	public $_subject;

	/**
	*
	*	Protected instance vars
	*
	*	@scope protected
	*
	**/
	protected $_data = array();
	protected $_customHeaders = array();
	protected $_format = 'html';
	
	/**
	*
	*	Private instance vars
	*
	*	@scope private
	*
	**/
	private $_templateFile = 'default.html';
	private $_templateDir = 'email_templates';
	private $_hasBeenSent = false;
	
	/**
	*
	*	Method to set the message format
	*
	*	@param string | $format | Format [html, text]
	*
	**/
	public function setFormat($format)
	{
		$this->_format = $format;
	}
	
	/**
	*
	*	Method to add a recipient
	*
	*	@param string | $email | Email of recipient
	*
	**/
	public function addTo($email)
	{
		$this->_recipients[] = $email;
	}
	
	/**
	*
	*	Method to add a CC recipient
	*
	*	@param string | $name  | Name of recipient
	*	@param string | $email | Email of recipient
	*
	**/
	public function addCc($email, $name = null)
	{
		$array = array();
		$array['name'] = $name;
		$array['email'] = $email;
		$this->_cc[] = $array;
	}
	
	/**
	*
	*	Method to add a BCC recipient
	*
	*	@param string | $name  | Name of recipient
	*	@param string | $email | Email of recipient
	*
	**/
	public function addBcc($email, $name = null)
	{
		$array = array();
		$array['name'] = $name;
		$array['email'] = $email;
		$this->_bcc[] = $array;
	}
	
	/**
	*
	*	Method to set from
	*
	*	@param string | $name  | Name of from
	*	@param string | $email | Email of from
	*
	**/
	public function setFrom($email, $name = null)
	{
		$array = array();
		$array['name'] = $name;
		$array['email'] = $email;
		$this->_from = $array;
	}
	
	/**
	*
	*	Method to set reply to
	*
	*	@param string | $email | Email to reply to
	*
	**/
	public function setReplyTo($email)
	{
		$this->_replyto = $email;
	}
	
	/**
	*
	*	Method to add your own custom headers
	*
	*	@param string | $header | Header you want to add
	*
	**/
	public function setCustomHeaders($header)
	{
		$this->_customHeaders[] = $header;
	}
	
	/**
	*
	*	Method to set email template dir
	*
	*	@param string | $template_dir | Template file directory
	*	@default application/views/email_templates
	*	
	*		-- !!WITHOUT TRAILING SLASH!! --
	*
	**/
	public function setTemplateDirectory($template_dir)
	{
		if(is_dir(dirname(__FILE__) . '/' . $template_dir))
		{
			$this->_templateDir = dirname(__FILE__) . '/' . $template_dir;
		}
		else
		{
			die('<b>Mailer Error: </b><i>' . get_class($this) . '</i> Email template directory must exist.');
		}
	}
	
	/**
	*
	*	Method to set email template file name
	*
	*	@param string | $template_file | Template file name to use in "application/views/$this->_templateDir"
	*
	**/
	public function setTemplateFile($template_file)
	{
		if(file_exists($this->_templateDir . '/' . $template_file))
		{
			$this->_templateFile = $template_file;
		}
		else
		{
			die('<b>Mailer Error: </b><i>' . get_class($this) . '</i> Email template file must exist.');
		}
	}
	
	/**
	*
	*	Method to set body data array
	*
	*	@param array | $data | Associative array of the data to be replaced in the email template
	*
	**/
	public function setData($data)
	{
		$this->_data = $data;
	}
	
	/**
	*
	*	Method to append body data to current data
	*
	*	@param string | $key   | Key of data
	*	@param string | $value | Value of data
	*
	**/
	public function addData($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	/**
	*
	*	Method to set the email subject
	*
	*	@param string | $subject | Subject for the email
	*
	**/
	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}
	
	/**
	*
	*	Method to prepare the paramaters to send to the sendEmail() method
	*
	**/
	public function prepareEmail()
	{
	
		// Get email body
		$body = $this->getBody();
		
		// Replace tags
		$body = $this->replaceTags($body, $this->_data);
		
		// Build headers
		$headers = $this->generateHeaders();
		
		// Build Recipients
		if(count($this->_recipients) > 0)
		{
			$recipients = implode(",", $this->_recipients);
		}
		
		return $this->sendEmail($recipients, $this->_subject, $body, $headers);
		
	}
	
	/**
	*
	*	Method to send email
	*
	*	Seperating out the mail() command gives you the chance, when extending, to alter the information that is sent to this function by extending the prepairEmail() method above
	*
	**/
	private function sendEmail($recipients, $subject, $body, $headers)
	{
		$mail = mail($recipients, $subject, $body, $headers);
		if($mail)
		{
			$this->_hasBeenSent = true;
			return true;
		}
		return false;
	}
	
	/**
	*
	*	Method to send
	*
	**/
	public function send()
	{
		return $this->prepareEmail();
	}
	
	/**
	*
	*	Method to replace tags in email body
	*
	*	@param string | $body | Body to replace
	*	@param string | $tags | Associative array of tags/values to replace
	*
	**/
	private function replaceTags($body, $tags)
	{
		foreach($tags as $tag=>$value)
		{
			$body = str_replace('{' . $tag . '}', $value, $body);
		}
		return $body;
	}
	
	/**
	*
	*	Method to get body
	*
	**/
	private function getBody()
	{
		if($body = file_get_contents(dirname(__FILE__) . '/' . $this->_templateDir . '/' . $this->_templateFile))
		{
			return $body;
		}
		else
		{
			die('<b>Mailer Error: </b><i>' . get_class($this) . '</i> Could not read email body from template.');
		}
	}
	
	/**
	*
	*	Method to generate headers into a mail() format
	*
	**/
	private function generateHeaders()
	{
	
		$headers = '';
	
		// Format
		if($this->_format == 'html')
		{
			$headers .= "MIME-Version: 1.0 \r\n";
			$headers .= "Content-type:text/html;charset=utf-8 \r\n";
		}
	
		// From
		if(count($this->_from) == 2)
		{
			$headers .= "From: {$this->_from['name']} <{$this->_from['email']}> \r\n";
		}
		else
		{
			die('<b>Mailer Error: </b><i>' . get_class($this) . '</i> Invalid "from" set.');
		}
		
		// Cc
		if(count($this->_cc) > 0)
		{
			$cc = implode(",", $this->_cc);
			$headers .= "Cc: {$cc} \r\n";
		}
		
		// Bcc
		if(count($this->_bcc) > 0)
		{
			$bcc = implode(",", $this->_bcc);
			$headers .= "Bcc: {$bcc} \r\n";
		}
		
		// Reply To
		if(null != $this->_replyto)
		{
			$headers .= "Reply-To: {$this->_replyto} \r\n";
		}
		
		return $headers;
		
	}

}
