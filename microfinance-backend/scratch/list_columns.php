<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\DB::select('DESCRIBE borrowers');
foreach($columns as $col) {
    echo $col->Field . "\n";
}
