<?php

$pharFile = 'ethical.phar';

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}

if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

// create phar
$phar = new Phar($pharFile);

$phar->startBuffering();

// Get the default stub. You can create your own if you have specific needs
$defaultStub = $phar->createDefaultStub('src/App.php');
$phar->buildFromDirectory(__DIR__ . '/src', '/.php$/');
$phar->buildFromDirectory(__DIR__ . '/vendor', '/.php$/');
$phar->buildFromDirectory(__DIR__, '/.php$/');

// Customize the stub to add the shebang
$stub = "#!/usr/bin/php \n" . $defaultStub;

// Add the stub
$phar->setStub($stub);

$phar->stopBuffering();
// plus - compressing it into gzip
$phar->compressFiles(Phar::GZ);

# Make the file executable
chmod(__DIR__ . '/' . $pharFile, 0770);
echo "$pharFile successfully created" . PHP_EOL;