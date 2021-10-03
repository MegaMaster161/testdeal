<?php
/**
 * Слой отвечающий за хранение и формат команд.
 * Аналог репозитория.
 */

namespace Lazev\CommandCreater;


use Lazev\InputParser\InputInterface;
use Lazev\InputParser\ParserArgv;

class Creater
{

    public ?string $pattern = null;

    public function addCommand(InputInterface $input, $path, $description = 'Описание отсутствует')
    {

       // $input->inputData();
        $strCode = $this->pattern;
        if(!isset($this->pattern)){
            $strCode =  $this->defaultPatternFileBlock($input->command, $input->arguments,
                                                       $input->optionsWithArgs, $description);
        }

        file_put_contents($path, $strCode);
    }


    /**
     * Метод для преобразования контекста в строку для записи в файл.
     * @param string $fileBlock
     * @param array $context
     * @return string
     */
    private function interpolate(string $fileBlock, array $context = array())
    {
        // Построение массива подстановки с фигурными скобками
        // вокруг значений ключей массива context.
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // Подстановка значений в сообщение и возврат результата.
        return strtr($fileBlock, $replace);
    }

    /**
     * Метод определяющий шаблон по которому будет создаваться команда.
     * Для более гибкой интерпритации лучше использовать абстракные фабрики.
     * @param $commandName
     * @param $args
     * @param $optionWithArgs
     * @param $description
     * @return string
     */
    private function defaultPatternFileBlock($commandName, $args, $optionWithArgs, $description){

        $context = [
            'commandName' => $commandName,
            'args' => $this->buildArgsForPattern($args),
            'optionWith' => $this->buildOptionWithArgs($optionWithArgs),
            'description' => $description
        ];

        $strFileBlock = "<?php \n";
        $strFileBlock.= "namespace App\Command;\n \n";
        $strFileBlock.= "class Command{commandName} \n";
        $strFileBlock.= "{\n";
        $strFileBlock.= "\t public \$description = '{description}';\n";
        $strFileBlock.= "\t public \$optionWithArgs = [{optionWith}];\n ";
        $strFileBlock.= "\t public \$args = [{args}]; \n";
        $strFileBlock.= "\t public function handle(){ \n";
        $strFileBlock.= "\t \t return ''; \n  \t} \n }";
/*      $strFileBlock.= "?>";*/

        return $this->interpolate($strFileBlock, $context);
        
    }

   private function buildArgsForPattern($args)
    {
        $str = '';
        if(count($args) == 1){
            $str.= '"'.$args[0];
            return $str;

        }

        foreach ($args as $arg){
            $str.= '"'.$arg.'", ';
        }

        return $str;
    }

    private function buildOptionWithArgs($optionWithArgs){

        $str = '';

        foreach ($optionWithArgs as $option => $args){

            $str.= '"'.$option.'"  => [';

            if(count($args) > 1){

                foreach ($args as $arg){
                    $str.= '"'.$arg.'", ';
                }

            } else {

                $str.= '"'.$args[0].'"';

                }

            $str.= "], \n \t \t \t";
        }

        return $str;

    }

    /**
     *
     * @param $strBlockCommand
     */
    public function setPatternFileBlock($strBlockCommand)
    {
        $this->patternFile = $strBlockCommand;
    }

}