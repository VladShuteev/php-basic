<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor') // Исключаем папку vendor
    ->exclude('runtime') // Исключаем папку runtime
    ->name('*.php')
    ->notName('*.blade.php'); // Если используете Laravel или шаблоны Blade

return (new PhpCsFixer\Config())
    ->setRules([
                   '@PSR12' => true,
                   'array_syntax' => ['syntax' => 'short'],
                   // Добавьте другие правила по необходимости
               ])
    ->setFinder($finder);