<?php

/*
*
*	TA_Mailer - PHP Email Class
*	(c) 2010  - Don't steal my code!
*
*	Created By: Tom Arnfeld (tarnfeld.com)
*
*/

define('TA_MAILER_DIR', dirname(__FILE__));

class TA_Mailer_Exception extends Exception { }

class TA_Mailer_Recipient
{
	
	protected $_email	= null;
	protected $_name	= null;
	
	public static function factory($email, $name = null)
	{
		$r = new self();
		$r->setEmail($email);
		$r->setName($name);
		
		return $r;
	}
	
	// Use the factory method please
	protected function __construct() { }
	
	public function setEmail($email)
	{
		$this->_email = $email;
		
		/* Method Chaining */
		return $this;
	}
	
	public function setName($name)
	{
		$this->_name = $name;
		
		/* Method Chaining */
		return $this;
	}
	
	public function email()
	{
		return $this->_email;
	}
	
	public function name()
	{
		return $this->_name;
	}
}

class TA_Mailer
{
	
	const HTML				= 'html';
	const PLAINTEXT			= 'text';
	
	protected $_recipients	= array();
	protected $_cc			= array();
	protected $_bcc			= array();
	
	protected $_subject		= null;
	
	protected $_from		= null;
	protected $_reply		= null;
	
	protected $_type		= null;
	
	protected $_data		= array();
	protected $_template	= null;
	protected $_raw			= null;
	
	public function __construct(array $options = array())
	{
		
	}
	
	public function addTo(TA_Mailer_Recipient $recipient)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setTo(array $recipients)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function addCc(TA_Mailer_Recipient $recipient)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setCc(array $recipients)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function addBcc(TA_Mailer_Recipient $recipient)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setBcc(array $recipients)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setFrom(TA_Mailer_Recipient $recipient)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setReplyTo(TA_Mailer_Recipient $recipient)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setSubject($subject)
	{
		$this->_subject = $subject;
		
		/* Method Chaining */
		return $this;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
		
		/* Method Chaining */
		return $this;
	}
	
	public function clearRecipients()
	{
		$this->_recipients	= array();
		$this->_cc			= array();
		$this->_bcc			= array();
		
		/* Method Chaining */
		return $this;
	}
	
	public function setData(array $data)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function addData($key, $value)
	{
		/* Method Chaining */
		return $this;
	}
	
	public function clearData()
	{
		/* Method Chaining */
		return $this;
	}
	
	public function setTemplate($path)
	{
		// Absolute path?
		if (substr($path, 0, 1) == '/')
		{
			if (!file_exists($path))
			{
				throw new TA_Mailer_Exception('Could not find template ' . $path);
			}
			
			$this->_template = $path;
			
			/* Method Chaining */
			return $this;
		}
		
		// We'll assume its relative
		$path = TA_MAILER_DIR . '/' . $path;
		if (!file_exists($path))
		{
			throw new TA_Mailer_Exception('Could not find template ' . $path);
		}
		
		$this->_template = $path;
		
		/* Method Chaining */
		return $this;
	}
	
	public function setRaw($content)
	{
		$this->_raw = $content;
		
		/* Method Chaining */
		return $this;
	}
	
	public function send()
	{
		// Check for at least one recipient
		if (count($this->_recipients) <= 0)
		{
			throw new TM_Mailer_Exception('Cannot send mail, the mailer requires at least one recipient');
		}
		
		// Check for php mail function
		if (!function_exists('mail'))
		{
			throw new TM_Mailer_Exception('PHP mail() function does not exist');
		}
		
		// Prepare the headers and content
		$headers	= $this->_prepareHeaders();
		$content	= $this->_prepareContent();
		$recipients	= $this->_prepareRecipients();
		
		// Send the mail
		return mail($recipients, $this->_subject, $content, $headers);
	}
	
	protected function _prepareHeaders()
	{
		
	}
	
	protected function _prepareContent()
	{
		
	}
	
	protected function _prepareRecipients()
	{
	
	}
}