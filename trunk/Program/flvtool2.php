<?php
// {{{ class Program_flvtool2 extends Program_Common
/**
 * Class Program_flvtool2
 *
 * Program_flvtool2
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @created  Fri Jun 18 15:07:13 EEST 2007
 */
class Program_flvtool2 extends Program_Common
{
    // {{{ Variables (Properties)
    
    /**
     * full path to binary
     *
     * @var string
     * @access protected
     */
    var $bin = 'flvtool2';
    
    // }}}
    // {{{ function addMetaTags() 
    
    /**
     * updateMetaTags
     *
     * Updates FLV with an onMetaTag event
     *
     * @access  public
     * @return  void
     */
    function updateMetaTags() 
    {
        $iFile = $this->getInputFile();
        if (PEAR::isError($iFile)) {
            return $iFile;
        }
        
        $oFile = $this->getOutputFile();
        if (PEAR::isError($oFile)) {
            return $oFile;
        }
        
        $this -> clearParams();
        $this -> addParam('-U', $iFile);
        // input and output file is equal for more projects
        $this -> addParam($oFile);
        
        return $this -> exec();
    }
    
    // }}}
}
// }}}
?>