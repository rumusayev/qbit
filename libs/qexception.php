<?php

/**
 * @package    debug
 *
 * @copyright  Copyright 2014 Rinat Gazikhanov, Vusal Khalilov, BITEP LLC. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
interface IException
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message 
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace
    
    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct($message = null, $code = 0);
}

class QException extends Exception implements IException
{
    protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code = 0;  	                      // User-defined exception code
    protected $error_code = '';  	              // Debugger exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown
    protected $output = 0;                        // 0 - browser or 1 - file?
	
    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
		if (is_array($message))	// Debugger mode
		{
			$error_code = array_shift($message);
			$message = Debugger::gi()->getError($error_code, $message);
			$this->error_code = $error_code;			
		}
		
        parent::__construct($message, $code);
    }
    
    public function __toString()
    {
        $out = get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
                                . "{$this->getTraceAsString()}";
		return $out;
    }
	
	public function getErrorCode()
	{
		return $this->error_code;
	}
}