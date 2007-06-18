<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Anton Shevchuk                                    |
// +----------------------------------------------------------------------+
// | This source file is subject to the New BSD license, That is bundled  |
// | with this package in the file LICENSE, and is available through      |
// | the world-wide-web at                                                |
// | http://www.opensource.org/licenses/bsd-license.php                   |
// | If you did not receive a copy of the new BSDlicense and are unable   |
// | to obtain it through the world-wide-web, please send a note to       |
// | pajoye@php.net so we can mail you a copy immediately.                |
// +----------------------------------------------------------------------+
// | Author: Anton Shevchuk <AntonShevchuk@gmail.com>                     |
// +----------------------------------------------------------------------+
//

require_once 'PEAR.php';

define('PROGRAM_OK', true);

define('PROGRAM_ERROR',             -1);
define('PROGRAM_ERROR_NOT_FOUND',   -2);
define('PROGRAM_ERROR_NOT_INIT',    -3);


define('PROGRAM_ERROR_NOT_SET_INPUT_FILE',      -10);
define('PROGRAM_ERROR_NOT_EXIST_INPUT_FILE',    -11);
define('PROGRAM_ERROR_NOT_SET_OUTPUT_FILE',     -12);

/* TODO:
Program_Common
 - addDefaultParam
 - addDefaultParams
 - addDefaultParamsString
 - getProcessList
 - getProcessPID

*/

// {{{ class Program
/**
 * Program class
 *
 * Package to manage various programs.
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @copyright  2007 Anton Shevchuk
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @created    Fri Jun 15 09:07:33 EEST 2007
 */
class Program 
{
    // {{{ function &factory($aProgram, $aOptions = false)

    /**
     * Create a new Program object for the specified program name
     *
     * For example:
     *     $program =& Program::factory($program_name);
     *          ^^
     * And not:
     *     $program = Program::factory($program_name);
     *
     * @param   mixed   'program name'
     * @param   array   An associative array of option names and
     *                            their values.
     *
     * @return  Program_Common $program  a newly created Program_Common object, or false on error
     *
     * @access  public
     */
    function &factory($aProgram, $aOptions = false)
    {
        $class_name = 'Program_'.$aProgram;

        $debug = (!empty($aOptions['debug']));
        
        $err = Program::loadClass($class_name, $debug);
        if (PEAR::isError($err)) {
            return $err;
        }
        
        $program =& new $class_name($aOptions);
        
        if (!empty($aOptions['binary'])) {
            $program -> setBinary($aOptions['binary']);
        }
        
        return $program;
    }

    // }}}
    // {{{ function fileExists($aFile)
    /**
     * Checks if a file exists in the include path
     *
     * @param   string  filename
     *
     * @return  bool    true success and false on error
     *
     * @access  public
     */
    function fileExists($aFile)
    {
        // safe_mode does notwork with is_readable()
        if (!@ini_get('safe_mode')) {
             $dirs = explode(PATH_SEPARATOR, ini_get('include_path'));
             foreach ($dirs as $dir) {
                 if (is_readable($dir . DIRECTORY_SEPARATOR . $aFile)) {
                     return true;
                 }
            }
        } else {
            $fp = @fopen($aFile, 'r', true);
            if (is_resource($fp)) {
                @fclose($fp);
                return true;
            }
        }
        return false;
    }
    // }}}
    // {{{ function classExists($aClassname)
    /**
     * Checks if a class exists without triggering __autoload
     *
     * @param   string  $aClassname
     *
     * @return  bool    true success and false on error
     * @static
     * @access  public
     */
    function classExists($aClassname)
    {
        if (version_compare(phpversion(), "5.0", ">=")) {
            return class_exists($aClassname, false);
        }
        return class_exists($aClassname);
    }

    // }}}
    // {{{ function loadClass($class_name, $debug)

    /**
     * Loads a PEAR class.
     *
     * @param   string  $aClassName classname to load
     * @param   bool    $aDebug if errors should be suppressed
     *
     * @return  mixed   true success or PEAR_Error on failure
     *
     * @access  public
     */
    function loadClass($aClassName, $aDebug)
    {
        if (!Program::classExists($aClassName)) {
            $aFileName = str_replace('_', DIRECTORY_SEPARATOR, $aClassName).'.php';
            
            if ($aDebug) {
                $include = include_once($aFileName);
            } else {
                $include = @include_once($aFileName);
            }
            
            if (!$include) {
                if (!Program::fileExists($file_name)) {
                    $msg = "unable to find package '$aClassName' file '$aFileName'";
                } else {
                    $msg = "unable to load class '$aClassName' from file '$aFileName'";
                }
                $err =& Program::raiseError(PROGRAM_ERROR_NOT_FOUND, null, null, $msg);
                return $err;
            }
        }
        return PROGRAM_OK;
    }

    // }}}
    // {{{ function &raiseError($aCode = null, $aMode = null, $aOptions = null, $aUserinfo = null)

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for PEAR::raiseError
     * without the message string.
     *
     * @param   mixed  int error code
     *
     * @param   int    error mode, see PEAR_Error docs
     *
     * @param   mixed  If error mode is PEAR_ERROR_TRIGGER, this is the
     *                 error level (E_USER_NOTICE etc).  If error mode is
     *                 PEAR_ERROR_CALLBACK, this is the callback function,
     *                 either as a function name, or as an array of an
     *                 object and method name.  For other error modes this
     *                 parameter is ignored.
     *
     * @param   string Extra debug information.  Defaults to the last
     *                 query and native error code.
     *
     * @return PEAR_Error instance of a PEAR Error object
     *
     * @access  private
     * @see     PEAR_Error
     */
    function &raiseError($aCode = null, $aMode = null, $aOptions = null, $aUserinfo = null)
    {
        $err =& PEAR::raiseError(null, $aCode, $aMode, $aOptions, $aUserinfo, 'Program_Error', true);
        return $err;
    }

    // }}}
    // {{{ function errorMessage($value = null)

    /**
     * Return a textual error message for a Program error code
     *
     * @param   int|array   integer error code,
                                null to get the current error code-message map,
                                or an array with a new error code-message map
     *
     * @return  string  error message, or false if the error code was
     *                  not recognized
     *
     * @access  public
     */
    function errorMessage($value = null)
    {
        static $errorMessages;

        if (is_array($value)) {
            $errorMessages = $value;
            return PROGRAM_OK;
        }

        if (!isset($errorMessages)) {
            $errorMessages = array(
                PROGRAM_OK                         => 'no error',
                PROGRAM_ERROR                      => 'unknown error',
                PROGRAM_ERROR_NOT_FOUND            => 'not found',
                PROGRAM_ERROR_NOT_INIT             => 'binary file not seted',
                PROGRAM_ERROR_NOT_EXIST_INPUT_FILE => 'input file is not exist',
                PROGRAM_ERROR_NOT_SET_OUTPUT_FILE  => 'output file is not configured',
            );
        }

        if (is_null($value)) {
            return $errorMessages;
        }

        if (PEAR::isError($value)) {
            $value = $value->getCode();
        }

        return isset($errorMessages[$value]) ?
           $errorMessages[$value] : $errorMessages[PROGRAM_ERROR];
    }

    // }}}
}
// }}}
    
// {{{ class Program_Error extends PEAR_Error

/**
 * Program_Error implements a class for reporting error messages.
 *
 * @package     Program
 * @category    Program
 * @author      Anton Shevchuk <AntonShevchuk@gmail.com>
 */
class Program_Error extends PEAR_Error
{
    // {{{ constructor: function Program_Error($code = PROGRAM_ERROR, $mode = PEAR_ERROR_RETURN, $level = E_USER_NOTICE, $debuginfo = null)

    /**
     * Program_Error constructor.
     *
     * @param   mixed   Program error code, or string with error message.
     * @param   int     what 'error mode' to operate in
     * @param   int     what error level to use for $mode & PEAR_ERROR_TRIGGER
     * @param   smixed   additional debug info, such as the last query
     */
    function Program_Error(
                            $code  = PROGRAM_ERROR,
                            $mode  = PEAR_ERROR_RETURN,
                            $level = E_USER_NOTICE,
                            $debuginfo = null)
    {
        if (is_null($code)) {
            $code = PROGRAM_ERROR;
        }
        $this->PEAR_Error('Program Error: '.Program::errorMessage($code), $code,
            $mode, $level, $debuginfo);
    }

    // }}}
}

// }}}
// {{{ class Program_Common extends PEAR
/**
 * Class Program_Common
 *
 * Program_Common: Base class that is extended by each program
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @created  Fri Jun 15 15:26:54 EEST 2007
 */
class Program_Common extends PEAR
{
    // {{{ Variables (Properties)
    /**
     * full path to binary
     *
     * @var string
     * @access protected
     */
    var $bin;
    
    /**
     * options
     *
     * @var array
     * @access protected
     */
    var $options = array();
    
    /**
     * params for run program
     *
     * @var array
     * @access protected
     */
    var $params = array();
    
    /**
     * params for run program (default for every execute)
     *
     * @var array
     * @access protected
     */
    var $paramsDefault = array();
    
    /**
     * process list
     *
     * @var array
     * @access protected
     */
    var $process = array();
    
    /**
     * output
     *
     * @var array
     * @access protected
     */
    var $output = array();
    
    /**
     * input media file
     *
     * @var string
     * @access private
     */
    var $inputFile;
        
    /**
     * output media file
     *
     * @var string
     * @access private
     */
    var $outputFile;
    
    // }}}
    // {{{ constructor: function __construct()
    
    /**
     * __constructor
     *
     * PHP5 Constructor
     * 
     * 
     * @param   array   An associative array of option names and
     *                            their values.
     */
    function __constructor($options = false) 
    {
        if (!empty($options['inputFile'])) {
            $this->setInputFile($options['inputFile']);
        }
        
        if (!empty($options['outputFile'])) {
            $this->setOutputFile($options['outputFile']);
        }
        
        $this->options = $options;
    }
    
    // }}}
    // {{{ function Program_Common()
    
    /**
     * Program_Common
     * 
     * PHP4 Constructor
     * 
     * @param   array   An associative array of option names and
     *                            their values.
     */
    function Program_Common($options = false) 
    {
        $this->__constructor($options);
    }
    
    // }}}
    // {{{ function exec()
    
    /**
     * exec
     *
     * execute the program
     *
     * @access  public
     * @return  rettype  return
     */
    function exec() 
    {
        $bin = $this->getBinary();
        if (PEAR::isError($bin)) {
            return false;
        }
        
        $res = exec($bin . $this->getExecString(), $this->output);
        return $res;
    }
    
    // }}}
    // {{{ function execInBackground()
    
    /**
     * exec
     *
     * execute the program
     *
     * @access  public
     * @return  rettype  return
     */
    function execInBackground() 
    {
        $bin = $this->getBinary();
        if (PEAR::isError($bin)) {
            return false;
        }
        
        $res = exec($bin . $this->getExecString() . ' > /dev/null &');
        return $res;
    }
    
    // }}}
    // {{{ function setOption($aOption, $aValue)
    
    /**
     * setOption
     *
     * @access  public
     * @param   string     $aOption  option title
     * @param   string     $aValue   option value
     * @return  rettype  return
     */
    function setOption($aOption, $aValue) 
    {
        $this->options[$aOption] = $aValue;
    }
    
    // }}}
    // {{{ function setOptions($aOptions)
    
    /**
     * setOptions
     *
     * @access  public
     * @param   array     $aOptions 
     * @return  rettype  return
     */
    function setOptions($aOptions) 
    {
        $this->options[$aOption] = array_merge($this->options, $aOptions);
    }
    
    // }}}
    // {{{ function getOption($aOption)
    
    /**
     * getOption
     *
     * @access  public
     * @param   string     getOption  option title
     * @return  rettype  return
     */
    function getOption($aOption) 
    {
        if (isset($this->options[$aOption])) {
            return $this->options[$aOption];
        } else {
            return null;
        }
    }
    
    // }}}
    // {{{ function getOptions()
    
    /**
     * getOptions
     *
     * @access  public 
     * @return  rettype  return
     */
    function getOptions($aOptions) 
    {
        return $this->options;
    }
    
    // }}}
    // {{{ function addParam($aParamTitle, $aParamValue)
    
    /**
     * addParam
     *
     * add new param
     * @access  public
     * @param   string     $aParamTitle  param title
     * @param   string     $aParamValue  param value
     * @return  rettype  return
     */
    function addParam($aParamTitle, $aParamValue = null) 
    {
        if ($aParamValue === null) {
            $this->addParamsString($aParamTitle);
        } else {
            $this->params[$aParamTitle] = $aParamValue;
        }
    }
    
    // }}}
    // {{{ function addParams($aParamArray)
    
    /**
     * addParams
     *
     * add new params
     * @access  public
     * @param   array    $aParamArray  param title
     * @return  rettype  return
     */
    function addParams($aParamArray) 
    {
        $this->params = $aParamArray + $this->params;
    }
    
    // }}}
    // {{{ function addParamsString($aString)
    
    /**
     * addParamsString
     *
     * add new params string
     * @access  public
     * @param   array    $aString  params string
     * @return  rettype  return
     */
    function addParamsString($aString) 
    {
        array_push($this->params, $aString);
    }
    
    // }}}
    // {{{ function clearParams()
    
    /**
     * clearParams
     *
     * clear params
     * @access  public
     * @return  rettype  return
     */
    function clearParams() 
    {
        $this->params = array();
    }
    
    // }}}
    // {{{ function getParams()
    
    /**
     * getParams
     *
     * get all params
     * @access  public
     * @return  array
     */
    function getParams() 
    {
        return $this->params;
    }
    
    // }}}
    // {{{ function getExecString()
    
    /**
     * getExecString
     *
     * @access  public
     * @return  rettype  return
     */
    function getExecString() 
    {
        $string = ' ';
        
        foreach ($this->params as $aKey => $aValue) {
            if (is_int($aKey)) {
                $string .= $aValue . ' ';
            } else {
                $string .= $aKey . ' ' . $aValue . ' ';                
            }
        }
        
        return $string;
    }
    
    // }}}
    // {{{ function setBinary($aParamArray)
    
    /**
     * setBinary
     *
     * set full path to binary file
     * @access  public
     * @param   string   $aPath
     * @return  rettype  return
     */
    function setBinary($aPath) 
    {
        $this->bin = $aPath;
    }
    
    // }}}
    // {{{ function getBinary()
    
    /**
     * getBinary
     *
     * set full path to binary file
     * @access  public
     * @param   string   $aPath
     * @return  rettype  return
     */
    function getBinary() 
    {
        if (empty($this->bin)) {
            return Program::raiseError(PROGRAM_ERROR_NOT_INIT);
        } else {
            return $this->bin;
        }
    }
    
    // }}}
    // {{{ function setInputFile($aFile)
    
    /**
     * setInputFile
     *
     * @access  public
     * @param   string     $aFile  full path to input file
     */
    function setInputFile($aFile) 
    {
    	$this->inputFile = $aFile;
    }
    
    // }}}
    // {{{ function getInputFile($aFile)
    
    /**
     * getInputFile
     *
     * @access  public
     * @return  void
     */
    function getInputFile() 
    {
        if (!empty($this->inputFile)) {
            if (file_exists($this->inputFile)) {
                return $this->inputFile;
            } else {
                return Program::raiseError(PROGRAM_ERROR_NOT_EXIST_INPUT_FILE);
            }
        } else {
            return Program::raiseError(PROGRAM_ERROR_NOT_SET_INPUT_FILE);
        }
    }
    
    // }}}
    // {{{ function setOutputFile($aFile)
    
    /**
     * setOutputFile
     *
     * @access  public
     * @param   string     $aFile  full path to output file
     */
    function setOutputFile($aFile) 
    {
    	$this->outputFile = $aFile;
    }
    
    // }}}
    // {{{ function getInputFile($aFile)
    
    /**
     * getOutputFile
     *
     * @access  public
     * @return  void
     */
    function getOutputFile() 
    {
        if (!empty($this->outputFile)) {
            return $this->outputFile;
        } else {
            return Program::raiseError(PROGRAM_ERROR_NOT_SET_OUTPUT_FILE);
        }
    }
    
    // }}}
}

// }}}
?>