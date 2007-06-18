<?php

define('PROGRAM_MPLAYER_ERROR_NOT_SET_OUTPUT_DIR',       -1);
define('PROGRAM_MPLAYER_ERROR_NOT_FOUND_OUTPUT_DIR',     -2);
define('PROGRAM_MPLAYER_ERROR_NOT_WRITABLE_OUTPUT_DIR',  -3);

// {{{ class Program_mplayer extends Program_Common
/**
 * Class Program_mplayer
 *
 * Program_mplayer
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @created  Fri Jun 15 15:26:54 EEST 2007
 */
class Program_mplayer extends Program_Common
{
    // {{{ Variables (Properties)
    
    /**
     * input media file data
     *
     * @var array
     * @access private
     */
    var $inputFileInformation = array();
    
    /**
     * output directory for images 
     *
     * @var string
     * @access private
     */
    var $outputDir;
    
    // }}}
    // {{{ function setOutputDir($aDir)
    
    /**
     * setOutputDir
     *
     * @access  public
     * @param   string     $aDir  full path to output directory
     */
    function setOutputDir($aDir) 
    {
    	$this->outputDir = $aDir;
    }
    
    // }}}
    // {{{ function getOutputDir()
    
    /**
     * getOutputDir
     *
     * @access  public
     * @return  void
     */
    function getOutputDir() 
    {
        if (!empty($this->outputDir)) {
//            if (is_dir($this->outputDir) && is_writable($this->outputDir)) {
            if (is_dir($this->outputDir)) {
                return $this->outputDir;
            } elseif (is_dir($this->outputDir) && !is_writable($this->outputDir)) {
                return PEAR::raiseError('Output directory is not writable', PROGRAM_MPLAYER_ERROR_NOT_WRITABLE_OUTPUT_DIR);
            } else {
                return PEAR::raiseError('Output directory not exist', PROGRAM_MPLAYER_ERROR_NOT_FOUND_OUTPUT_DIR);
            }
        } else {
            return PEAR::raiseError('Output directory not seted', PROGRAM_MPLAYER_ERROR_NOT_SET_OUTPUT_DIR);
        }
    }
    
    // }}}
    // {{{ function getInformation()
    
    /**
     * getInformation
     *
     * @access  public
     */
    function getInformation() 
    {
        if (!empty($this->inputFileInformation)) {
            return $this->inputFileInformation;
        }        
        
        $iFile = $this->getInputFile();
        if (PEAR::isError($iFile)) {
            return $iFile;
        }
        
        $this -> clearParams();
        $this->addParam('-vo', 'null');
        $this->addParam('-ao', 'null');
        $this->addParam('-frames', '0');
        $this->addParam('-identify');
        $this->addParam($iFile);
        
        if ($this->exec()) {
            for ($size = sizeof($this->output), $i = 0; $i<$size; $i++) {
                if (strstr($this->output[$i], 'ID_')) {
                    list($a, $b) = split('=', $this->output[$i]);
                    $this->inputFileInformation[$a] = $b;
                }
            }
        }
        return $this->inputFileInformation;
    }
    
    // }}}
    // {{{ function catchFrame()
    
    /**
     * catchFrames
     *
     * @access  public
     * @param   integer $aStep
     * @param   integer $aFrames
     */
    function catchFrames($aStep = 15, $aFrames = 3) 
    {
        $iFile = $this->getInputFile();
        if (PEAR::isError($iFile)) {
            return $iFile;
        }
        
        $oDir = $this->getOutputDir();
        if (PEAR::isError($oDir)) {
            return $oDir;
        }
        $this -> clearParams();
        $this -> addParam($iFile);
        $this -> addParam('-sstep', $aStep);
        $this -> addParam('-nosound');
        $this -> addParam('-vo', 'jpeg:outdir=' . $oDir);
        $this -> addParam('-frames', $aFrames); 
        
        return $this->exec();
    }
    
    // }}}
}
// }}}

/*
getDuration
catchFrame

*/
?>