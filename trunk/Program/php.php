<?php
// {{{ class Program_php extends Program_Common
/**
 * Class Program_php
 *
 * Program_shell
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @created  Fri Jun 15 15:26:54 EEST 2007
 */
class Program_php extends Program_Common
{
    // {{{ Variables (Properties)
    
    /**
     * full path to binary
     *
     * @var string
     * @access protected
     */
    var $bin = 'php';
    
    // }}}
    // {{{ function runFile($aFile) 
    
    /**
     * runFile
     *
     * run PHP File
     *
     * @access  public
     * @param   string   $aFile  full path to php script
     * @return  rettype  return
     */
    function runFile($aFile) 
    {
        $this->addParam('-f', $aFile);
        return $this->exec();
    }
    
    // }}}
}
// }}}
?>