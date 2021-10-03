<?php


namespace Lazev\OutputHandler;


use Lazev\InputParser\InputInterface;
use Lazev\InputParser\ParserArgv;

class OutputConsole implements OutputInterface
{

    public string $buffer = '';
    public InputInterface $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }



    public function setBuffer($buffer){
        $this->buffer = $buffer;
    }



    function output(){

        print($this->buffer);
    }

}