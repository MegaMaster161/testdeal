<?php


namespace Lazev;


use Lazev\CommandCreater\Creater;
use Lazev\InputParser\InputInterface;
use Lazev\InputParser\ParserArgv;
use Lazev\OutputHandler\OutputConsole;
use Lazev\OutputHandler\OutputInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class LibHandler
{

    public ?string $path = './Command/';
    public ?InputInterface $input;
    public ?OutputInterface $output;
    public string $buffer = '';

    /**
     * LibHandler constructor.
     */
    public function __construct(ParserArgv $input, OutputConsole $output)
    {
        $this->input = $input;
        $this->output = $output;
    }



    public function getMappingCommand()
    {
        $mapping = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path)) as $filename) {
            // пропускаем каталоги .. и .
            if ($filename->isDir()) continue;
            $forNamespace = str_replace('.php', '', $filename->getFilename());
            $nameCommand = str_replace('Command', '', $forNamespace);
            $mapping = array_merge($mapping, [$nameCommand => "App\\Command\\".$forNamespace]) ;

        }

        return $mapping;

    }

    public function getCommandHandlerByCommandName($commandName){

        $mapping = $this->getMappingCommand();

        $className = $mapping[$commandName];

        return new $className();

    }

    public function createCommand(){

        $commandName = $this->input->command;

        $creater = new Creater();

        $creater->addCommand($this->input, $this->path.'/Command'.$commandName.'.php');

        $this->outputParserInfo();
    }

    public function isCommandCreated($commandName)
    {
        return array_key_exists($commandName,$this->getMappingCommand());
    }

    public function isArgumentHelp()
    {
        return in_array('help', $this->input->arguments);
    }

    public function getCommandDescription($commandName){
        if($this->isCommandCreated($commandName)){
            $commandClass = $this->getCommandHandlerByCommandName($commandName);
            $description =  $commandClass->description;
            return 'Command name:'.$commandName."\n"."\t Description:".$description."\n";
        }
    }

    public function getAllCommandDescription(){

        $mapping = $this->getMappingCommand();

        $output = '';

        foreach ($mapping as $command => $nameSpace){

           $description = $this->getCommandDescription($command);

           $output.= $description;
        }

        return $output;

    }

    public function isInputNull(){
        return (!isset($this->input->command));
    }

    public function getOutputHelp(){

        if(!$this->isCommandCreated($this->input->command)){
            throw new \Exception("Вы пытаетесь получить справку о не зарегестрированной команде. Аргумент help зарезервирован.\n");

        }

        if($this->isArgumentHelp()){

            $description = $this->getCommandDescription($this->input->command);
            $this->output->setBuffer($description);
            $this->output->output();

        }
    }

    public function getOutputDescAllCommands()
    {

        $outputForBuffer = $this->getAllCommandDescription();
        $this->output->setBuffer($outputForBuffer);
        return $this->output->output();

    }

    public function setPath($path)
    {
        $this->path = $path;
    }


    public function buildCommandMessage()
    {
        $str = 'Called command: '.$this->input->command.PHP_EOL;
        $this->buffer.= $str;
    }

    public function buildArguments()
    {
        $str = "Arguments:".PHP_EOL;
        foreach ($this->input->arguments as $argument)
        {
            $str.="\t -".$argument.PHP_EOL;
        }
        $this->buffer.= $str;
    }

    public function buildOptionWithArgs()
    {
        $str = "Options:".PHP_EOL;
        foreach ($this->input->optionsWithArgs as $option => $arrArgs){
            $str.= "\t - ". $option.PHP_EOL;
            foreach ($arrArgs as $arg){
                $str .= "\t\t -".$arg.PHP_EOL;
            }
        }
        $this->buffer.= $str;
    }

    public function outputParserInfo(){
        $this->buildCommandMessage();
        $this->buildArguments();
        $this->buildOptionWithArgs();
        $this->output->setBuffer($this->buffer);
        return $this->output->output();
    }
}