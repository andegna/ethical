<?php

namespace Andegna\Ethical;

require __DIR__ . '/../vendor/autoload.php';


use Symfony\Component\Console\Application;

$application = new Application('Andegna Ethi-Cal', '1.0.0');
$command = new DefaultCommand();

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
$application->run();
