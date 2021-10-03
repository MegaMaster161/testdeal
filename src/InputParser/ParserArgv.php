<?php


namespace Lazev\InputParser;


class ParserArgv implements InputInterface
{

    public array $tokens;
    public ?string $command;
    public ?array $arguments = [];
    public ?array $optionsWithArgs = [];


    public function __construct(array $argv = null)
    {
        $this->inputData($argv);
    }

    public function getCommand()
    {
        $this->command = array_shift($this->tokens);
    }

    public function getArguments()
    {
        $arr = $this->tokens;

        foreach ($arr as $value){
            if(str_starts_with($value, '[')){
                continue;
            }

            if(str_starts_with($value, '{')){
                $value = str_replace(['{','}'], '', $value);
                $this->addArguments($value);
                continue;
            }

            $this->addArguments($value);
        }

        return $this->arguments;
    }

    public function addArguments($argument)
    {
        if(!in_array($argument, $this->arguments)){
            $this->arguments[] = $argument;
        }
    }

    public function getOptionsWithsArgs()
    {
        $arr = $this->tokens;
        array_shift($arr);
        foreach ($arr as $value){
            if(str_starts_with($value, '[')){
                $sanitaizeStr = str_replace(['[',']'], '', $value);
                $parse = explode('=', $sanitaizeStr);
                $nameOption = array_shift($parse);

                if(str_starts_with($parse[0], '{' )){
                    foreach ($parse as $arg){
                        $arg = str_replace(['{','}'], '', $arg);
                        $this->addOptionArguments($nameOption, $arg);
                        continue;
                    }

                   throw new \Exception('Unknown option format');
                }

                $this->addOptionArguments($nameOption, $parse[0] );
                continue;
            }
        }

    }

    public function addOptions($option)
    {
       if(!isset($this->optionsWithArgs[$option])){
           $this->optionsWithArgs[$option] = [];
       }
    }


    public function addOptionArguments($option, $argument)
    {
        $this->addOptions($option);
        $arr = $this->optionsWithArgs;
        if(array_key_exists($option, $arr )){
            array_push($this->optionsWithArgs[$option], $argument);
        }

    }

    public function inputData( array $argv = null ){
        $args = (isset($argv))?$argv:$_SERVER['argv'];
        array_shift($args);
        $this->tokens = $args;
        $this->getCommand();
        $this->getArguments();
        $this->getOptionsWithsArgs();
    }
}