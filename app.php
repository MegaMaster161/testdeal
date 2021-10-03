<?php

include 'vendor/autoload.php';

use Lazev\InputParser\ParserArgv;
use Lazev\LibHandler;
use Lazev\OutputHandler\OutputConsole;

try{

    $input = new ParserArgv();
    $output = new OutputConsole($input);

    $lib = new LibHandler($input, $output);

    if($lib->isInputNull()){
        return $lib->getOutputDescAllCommands();
    }

    if($lib->isArgumentHelp()){
        return $lib->getOutputHelp();
    }

    if(!$lib->isInputNull() && !$lib->isArgumentHelp()){
        $lib->createCommand();
    }

} catch (Exception $exception){

    $output->setBuffer($exception->getMessage());
    $output->output();

}


