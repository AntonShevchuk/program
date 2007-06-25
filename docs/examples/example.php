<?php
include_once 'Program.php';

// set your paths
$mplayer  =& Program::factory('mplayer',  array('debug' => true, 'binary' => '/usr/bin/mplayer'));
$mencoder =& Program::factory('mencoder', array('debug' => true, 'binary' => '/usr/bin/mencoder'));
$flvtool2 =& Program::factory('flvtool2', array('debug' => true, 'binary' => '/usr/bin/flvtool2'));

// set input/output file
$inputFile  = dirname(__FILE__) . DIRECTORY_SEPARATOR .'input.3gp';
$outputDir  = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR;
$outputFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR .'output.flv';

/*@var $mencoder Program_mencoder */
$mencoder -> setInputFile($inputFile);
$mencoder -> setOutputFile($outputFile);

/*@var $flvtool2 Program_flvtool2 */
$flvtool2 -> setInputFile($outputFile);
$flvtool2 -> setOutputFile($outputFile);

/*@var $mplayer Program_mplayer */
$mplayer -> setInputFile($inputFile);
$mplayer -> setOutputDir($outputDir);
?>
<html>
    <head>
        <title>Program Package</title>
    </head>
    <style>
    pre.code {
        width:98%;
        border: 1px dotted #000;
        background-color: #eee;
        padding: 8px 4px 8px 4px;
        
    }

    pre.result {
        width:98%;
        border: 1px dotted #000;
        background-color: #ffe;
        padding: 8px 4px 8px 4px;
        
    }
    </style>
    <body>
    <h1>Program package:</h1>
    <h2>Introduction:</h2>
    <p>
        The package "Program" is to provide outer programs managing capabilities - in other words this is enhanced console
    </p>
    <h2>Examples:</h2>
    <h3>Initialization:</h3>
    <pre class="code">
    include_once 'Program/Program.php';

    // set your paths
    $mplayer  =& Program::factory('mplayer',  array('debug' => true, 'binary' => '/usr/bin/mplayer'));
    $mencoder =& Program::factory('mencoder', array('debug' => true, 'binary' => '/usr/bin/mencoder'));
    $flvtool2 =& Program::factory('flvtool2', array('debug' => true, 'binary' => '/usr/bin/flvtool2'));
    
    // set input/output file
    $inputFile  = dirname(__FILE__) . DIRECTORY_SEPARATOR .'input.3gp';
    $outputDir  = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR;
    $outputFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR .'output.flv';
    
    /*@var $mencoder Program_mencoder */
    $mencoder -> setInputFile($inputFile);
    $mencoder -> setOutputFile($outputFile);
    
    /*@var $flvtool2 Program_flvtool2 */
    $flvtool2 -> setInputFile($outputFile);
    $flvtool2 -> setOutputFile($outputFile);
    
    /*@var $mplayer Program_mplayer */
    $mplayer -> setInputFile($inputFile);
    $mplayer -> setOutputDir($outputDir);
    </pre>
    <h3>Get information about media file:</h3>
    <pre class="code">
    $res = $mplayer -> getInformation();
    </pre>
    <b>Result:</b>
    <pre class="result">
<?php
$res = $mplayer -> getInformation();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}
?> 
    </pre>
    <h3>Capture the frames from video file:</h3>
    <pre class="code">
    $res = $mplayer -> catchFrames();
    </pre>
    <b>Result:</b>
    <pre class="result">
<?php
$res = $mplayer -> catchFrames();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}  
?>  
    </pre>
    <h3>Convert video file to FLV:</h3>
    <pre class="code">
    $res = $mencoder -> convertToFLV();

    if (PEAR::isError($res)) {
        echo $res->getMessage();
    } else {
        print_r($res);
    }
    
    // Update FLV data
    $res = $flvtool2 -> updateMetaTags();
    
    if (PEAR::isError($res)) {
        echo $res->getMessage();
    } else {
        print_r($res);
    }
    </pre>
    <b>Result:</b>
    <pre class="result">
<?php
$res = $mencoder -> convertToFLV();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}

// Update FLV data
$res = $flvtool2 -> updateMetaTags();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}
?>
    </pre>

<p>Copyright&nbsp;&copy;&nbsp;<a href="http://anton.shevchuk.name">Anton Shevchuk</a> 2007</p>
</body>
</html>