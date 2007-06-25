<?php

define('PROGRAM_MENCODER_AUDIO_CODEC_MP3',  'mp3lame');
define('PROGRAM_MENCODER_AUDIO_CODEC_PCM',  'pcm');      // WAV PCM

define('PROGRAM_MENCODER_AUDIO_RATE_LIGHT', 4000);       // for WAV PCM
define('PROGRAM_MENCODER_AUDIO_RATE_LOW',   22050);
define('PROGRAM_MENCODER_AUDIO_RATE_VCD',   44100);
define('PROGRAM_MENCODER_AUDIO_RATE_DVD',   48000);


define('PROGRAM_MENCODER_LAVCOPTS_QUALITY_VCD',  'vcodec=mpeg1video:vrc_buf_size=327:vrc_minrate=1152:vrc_maxrate=1152:vbitrate=1152:keyint=15:acodec=mp2');
define('PROGRAM_MENCODER_LAVCOPTS_QUALITY_SVCD', 'vcodec=mpeg2video:vrc_buf_size=917:vrc_maxrate=2500:vbitrate=1800:keyint=15:acodec=mp2');
define('PROGRAM_MENCODER_LAVCOPTS_QUALITY_DVD',  'vcodec=mpeg2video:vrc_buf_size=1835:vrc_maxrate=9800:vbitrate=5000:keyint=15:vstrict=0:acodec=ac3');





// {{{ class Program_mencoder extends Program_Common
/**
 * Class Program_mencoder
 *
 * Program_mencoder
 *
 * @category   Program
 * @package    Program
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @created  Fri Jun 15 15:26:54 EEST 2007
 */
class Program_mencoder extends Program_Common
{
    // {{{ Variables (Properties)
    
    /**
     * full path to binary
     *
     * @var string
     * @access protected
     */
    var $bin = 'mencoder';
    
    // }}}
    // {{{ function setScale($aWitdh, $aHeight)
    
    /**
     * setScale
     *
     * set scale for video output file
     *
     * @access  public
     * @param   integer     $aWidth   video width in px
     * @param   integer     $aHeight  video height in px
     * @return  void  
     */
    function setScale($aWidth, $aHeight) 
    {
    	if (is_int($aWidth) && is_int($aHeight)) {
    	    $this -> options['scale']   = true;
    	    $this -> options['scale_x'] = $aWidth;
    	    $this -> options['scale_y'] = $aHeight;
    	    return true;
    	} else {
    	    return PEAR::raiseError('Video width and height should be integer value');
    	}
    }
    
    // }}}
    // {{{ function convertToFLV()
    
    /**
     * convertToFLV
     *
     * @access  public
     * @return  void
     */
    function convertToFLV() 
    {
        $iFile = $this->getInputFile();
        if (PEAR::isError($iFile)) {
            return $iFile;
        }
        
        $oFile = $this->getOutputFile();
        if (PEAR::isError($oFile)) {
            return $oFile;
        }
        
        $srate    = $this->getOption('srate')    ? $this->getOption('srate')    : 22050;
        $vbitrate = $this->getOption('vbitrate') ? $this->getOption('vbitrate') : 800;
        
        $this->clearParams();
    	$this->addParam($iFile);
    	$this->addParam('-o',        $oFile);
    	$this->addParam('-of',       'lavf');
    	$this->addParam('-oac',      'mp3lame');
    	$this->addParam('-lameopts', 'abr:br=56');
    	$this->addParam('-srate',    $srate);
    	$this->addParam('-ovc',      'lavc');
    	$this->addParam('-lavcopts', 'vcodec=flv:vbitrate='.$vbitrate.':mbd=2:mv0:trell:v4mv:cbp:last_pred=3');
    	$this->addParam('-lavfopts', 'i_certify_that_my_video_stream_does_not_use_b_frames');
    	
    	if ($this->getOption('scale')) {
    	    $this->addParam('-vop', 'scale='.$this->getOption('scale_x').':'.$this->getOption('scale_y'));
    	}
    	
    	return $this->exec();
    }
    
    // }}}
    
}
// }}}
/*
setOutputAudioCodec
setOutputVideoCodec
convert
*/
?>