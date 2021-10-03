<?php


namespace App\Command;


class CommandTest
{
    public $description = 'Тестовая команда';

    public $optionsWithArgs;

    public $arguments;

    public function handle(){
        return 'Тут реализуется логика.';
    }
}