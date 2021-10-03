<?php 
namespace App\Command;
 
class Commandone 
{
	 public $description = 'Описание отсутствует';
	 public $optionWithArgs = ["four"  => ["jj", "dd", "ee", ], 
 	 	 	"five"  => ["tt", "bb", ], 
 	 	 	];
 	 public $args = ["two", "tree", "555", "111", "333", "foo", ]; 
	 public function handle(){ 
	 	 return ''; 
  	} 
 }