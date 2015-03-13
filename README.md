# Program
This package can be used to manage the execution of external programs from PHP.

It can build a command line string to execute an external program synchronously or in background, pass switches to define program options, and define the program input or output files.

It also supports using driver classes that can build complex command line switches to simplify the definition of program options in a simplified way.


## Initialization

For initialization Program package insert next code:

```php
include_once 'Program.php';
```

## Create Program entity

For create entity of program you should be use method factory:

```php
// set full path to program binary
$mplayer  =& Program::factory('mplayer',  array('binary' => '/usr/bin/mplayer'));
```

## Params Manipulation

You can add any params to execute string:

```php
// add param string
$mplayer -> addParam('video_in.avi');
$mplayer -> addParamsString('video_in.avi');

// add param prefix and value
$mplayer -> addParam('-sstep', 4);

// add params array
$mplayer -> addParams(array('video_in.avi', '-sstep' => 4));
```

Get params
```php
$mplayer->getParams();
```

Clear params
```php
$mplayer->clearParams();
```

## Input/Output file

For many application you can set input and output file:

* setInputFile
* getInputFile
* setOutputFile
* getOutputFile

## Execute program

For run program use method *exec* or *execInBackground* (for unix systems only)
