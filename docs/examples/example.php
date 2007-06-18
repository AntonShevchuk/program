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

echo "<pre>\n";

echo "get information about media file\n";
$res = $mplayer -> getInformation();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}

echo "capture the frames\n";
$res = $mplayer -> catchFrames();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}

echo "convert to FLV\n";
$res = $mencoder -> convertToFLV();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}

echo "update FLV data\n";
$res = $flvtool2 -> updateMetaTags();

if (PEAR::isError($res)) {
    echo $res->getMessage();
} else {
    print_r($res);
}

echo "</pre>\n";
?>