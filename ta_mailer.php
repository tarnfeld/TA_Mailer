<?php

/*
*
*	TA_Mailer - PHP Email Class
*	(c) 2010  - Don't steal my code!
*
*	Created By: Tom Arnfeld (tarnfeld.com)
*
*/

class TA_Mailer
{

	/**
	*
	*	Class Properties : Array
	*
	**/
	private $_to				=	array();	// [ Email, Email, Email, etc ]
	private $_cc				=	array();	// [ Email, Email, Email, etc ]
	private $_bcc				=	array();	// [ Email, Email, Email, etc ]
	private $_from				=	array();	// [ Email, Name ]
	private $_replyTo			=	array();	// [ Email, Name ]
	private $_headers			=	array();	// [ Header, Header, Header, etc ]
	private $_customHeaders		=	array();	// [ Header, Header, Header, etc ]
	private $_attachments		=	array();	// { [ Location, Name, Disposition ], [ Location, Name, Disposition ], etc }
	
	/**
	*
	*	Class Properties : Booleans
	*
	**/
	private $_hasBeenSent		=	false;		// Mailer Status
	
	/**
	*
	*	Class Properties : Strings
	*
	**/
	private $_contentType		=	'plain';	// Email Content Type
	private $_charset			=	'utf-8';	// Email Character Set
	private $_mimeVersion		=	'1.0';		// Email MIME Version
	private $_newline			=	'\r\n';		// New Line for Headers
	protected  $_subject		=	'';			// Email Subject
	protected  $_body			=	'';			// Email Body
	
	/**
	*
	*	Add a TO recipient
	*
	**/
	public function addTo($email)
	{
		$this->_to[] = $email;
	}
	
	/**
	*
	*	Add a CC Recipient
	*
	**/
	public function addCc($email)
	{
		$this->_cc[] = $email;
	}
	
	/**
	*
	*	Add a BCC Recipient
	*
	**/
	public function addBcc($email)
	{
		$this->_bcc[] = $email;
	}
	
	/**
	*
	*	Add a Custom Header
	*
	**/
	public function addCustomHeader($header)
	{
		$this->_customHeaders[] = $header;
	}
	
	/**
	*
	*	Add an Attachment
	*
	**/
	public function addAttachment($file_location, $name = false, $disposition = 'attachment')
	{
		$this->_attachments[] = array('location' => $file_location, 'name' => $name, 'disposition' => $disposition);
	}
	
	/**
	*
	*	Set the FROM
	*
	**/
	public function setFrom($email, $name = false)
	{
		$this->_from = array($email, $name);
	}
	
	/**
	*
	*	Set the REPLY TO
	*
	**/
	public function setReplyTo($email, $name = false)
	{
		$this->_replyTo = array($email, $name);
	}
	
	/**
	*
	*	Set the SUBJECT
	*
	**/
	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}
	
	/**
	*
	*	Set the BODY
	*
	**/
	public function setBody($body)
	{
		$this->_body = $body;
	}
	
	/**
	*
	*	Set the email CONTENT TYPE
	*
	**/
	public function setContentType($content_type)
	{
		if($content_type == 'plain' || $content_type == 'html')
		{
			$this->_contentType = $content_type;
			return true;
		}
		return false;
	}
	
	/**
	*
	*	Set the email CHARSET
	*
	**/
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	/**
	*
	*	Set the email MIME Version
	*
	**/
	public function setMimeVersion($version)
	{
		$this->_mimeVersion = $version;
	}
	
	/**
	*
	*	Send the email
	*
	**/
	public function send()
	{
		$this->_buildHeaders();
	}
	
	/**
	*
	*	Send the email
	*
	**/
	private function _buildHeaders()
	{
		
		// TO Headers
		if(count($this->_to) > 0)
		{
			$this->_headers['To'] = implode(", ", $this->_to);
		} else { return false; }
		
		// CC Headers
		if(count($this->_cc) > 0)
		{
			$this->_headers['Cc'] = implode(", ", $this->_cc);
		}
		
		// BCC Headers
		if(count($this->_bcc) > 0)
		{
			$this->_headers['Bcc'] = implode(", ", $this->_bcc);
		}
		
		// FROM Headers
		if(count($this->_from) == 1)
		{
			$this->_headers['From'] = $this->_from[0];
		}
		elseif(count($this->_from) == 2)
		{
			$this->_headers['From'] = $this->_from[0] . ' <' . $this->_from[1] . '>';
		} else { return false; }
		
		// Mime Version
		$this->_headers['MIME-Version'] = $this->_mimeVersion;
		
		// Content Type
		$this->_headers['Content-type'] = 'text/' . $this->_contentType . ';charset=' . $this->_charset;
		
		foreach($this->_customHeaders as $header)
		{
			
			$parts = explode(":", $header);
			$name = trim($parts[0]);
			unset($parts[0]);
			
			$this->_headers[$name] = implode(":", $parts);
			
		}
		
		// Attachments
		$attachment_boundry = "B_ATC_" . uniqid('');
		foreach($this->_attachments as $attachment) {
			
			$content_type = '';
			$attachment_name = $attachment['filename'];
			if($attachment['name'] != false)
			{
				$attachment_name = '';
			}
			
			$content_type = $this->_mime_types(next(explode('.', basename($filename))))
			
			$h  = "--" . $attachment_boundry . $this->_newline;
			$h .= "Content-type: " . $content_type . "; ";
			$h .= "name=\"" . $attachment_name . "\"" . $this->_newline;
			$h .= "Content-Disposition: " . $attachment['disposition'] . ";" . $this->_newline;
			$h .= "Content-Transfer-Encoding: base64" . $this->_newline;
			
			$this->_headers[] = $h;
			
		}
		
	}
	
	/**
	*
	*	Method to determine the mime type of a file from the extention
	*
	**/
	private function _mime_types($ext = "")
	{
		$mimes = array(	'hqx'	=>	'application/mac-binhex40',
						'cpt'	=>	'application/mac-compactpro',
						'doc'	=>	'application/msword',
						'bin'	=>	'application/macbinary',
						'dms'	=>	'application/octet-stream',
						'lha'	=>	'application/octet-stream',
						'lzh'	=>	'application/octet-stream',
						'exe'	=>	'application/octet-stream',
						'class'	=>	'application/octet-stream',
						'psd'	=>	'application/octet-stream',
						'so'	=>	'application/octet-stream',
						'sea'	=>	'application/octet-stream',
						'dll'	=>	'application/octet-stream',
						'oda'	=>	'application/oda',
						'pdf'	=>	'application/pdf',
						'ai'	=>	'application/postscript',
						'eps'	=>	'application/postscript',
						'ps'	=>	'application/postscript',
						'smi'	=>	'application/smil',
						'smil'	=>	'application/smil',
						'mif'	=>	'application/vnd.mif',
						'xls'	=>	'application/vnd.ms-excel',
						'ppt'	=>	'application/vnd.ms-powerpoint',
						'wbxml'	=>	'application/vnd.wap.wbxml',
						'wmlc'	=>	'application/vnd.wap.wmlc',
						'dcr'	=>	'application/x-director',
						'dir'	=>	'application/x-director',
						'dxr'	=>	'application/x-director',
						'dvi'	=>	'application/x-dvi',
						'gtar'	=>	'application/x-gtar',
						'php'	=>	'application/x-httpd-php',
						'php4'	=>	'application/x-httpd-php',
						'php3'	=>	'application/x-httpd-php',
						'phtml'	=>	'application/x-httpd-php',
						'phps'	=>	'application/x-httpd-php-source',
						'js'	=>	'application/x-javascript',
						'swf'	=>	'application/x-shockwave-flash',
						'sit'	=>	'application/x-stuffit',
						'tar'	=>	'application/x-tar',
						'tgz'	=>	'application/x-tar',
						'xhtml'	=>	'application/xhtml+xml',
						'xht'	=>	'application/xhtml+xml',
						'zip'	=>	'application/zip',
						'mid'	=>	'audio/midi',
						'midi'	=>	'audio/midi',
						'mpga'	=>	'audio/mpeg',
						'mp2'	=>	'audio/mpeg',
						'mp3'	=>	'audio/mpeg',
						'aif'	=>	'audio/x-aiff',
						'aiff'	=>	'audio/x-aiff',
						'aifc'	=>	'audio/x-aiff',
						'ram'	=>	'audio/x-pn-realaudio',
						'rm'	=>	'audio/x-pn-realaudio',
						'rpm'	=>	'audio/x-pn-realaudio-plugin',
						'ra'	=>	'audio/x-realaudio',
						'rv'	=>	'video/vnd.rn-realvideo',
						'wav'	=>	'audio/x-wav',
						'bmp'	=>	'image/bmp',
						'gif'	=>	'image/gif',
						'jpeg'	=>	'image/jpeg',
						'jpg'	=>	'image/jpeg',
						'jpe'	=>	'image/jpeg',
						'png'	=>	'image/png',
						'tiff'	=>	'image/tiff',
						'tif'	=>	'image/tiff',
						'css'	=>	'text/css',
						'html'	=>	'text/html',
						'htm'	=>	'text/html',
						'shtml'	=>	'text/html',
						'txt'	=>	'text/plain',
						'text'	=>	'text/plain',
						'log'	=>	'text/plain',
						'rtx'	=>	'text/richtext',
						'rtf'	=>	'text/rtf',
						'xml'	=>	'text/xml',
						'xsl'	=>	'text/xml',
						'mpeg'	=>	'video/mpeg',
						'mpg'	=>	'video/mpeg',
						'mpe'	=>	'video/mpeg',
						'qt'	=>	'video/quicktime',
						'mov'	=>	'video/quicktime',
						'avi'	=>	'video/x-msvideo',
						'movie'	=>	'video/x-sgi-movie',
						'doc'	=>	'application/msword',
						'word'	=>	'application/msword',
						'xl'	=>	'application/excel',
						'eml'	=>	'message/rfc822'
					);

		return ( ! isset($mimes[strtolower($ext)])) ? "application/x-unknown-content-type" : $mimes[strtolower($ext)];
	}

}