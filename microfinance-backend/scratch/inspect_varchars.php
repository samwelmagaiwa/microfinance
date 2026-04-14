<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM borrowers');
foreach($columns as $col) {
    if(strpos($col->Type, 'varchar') !== false) {
        echo "{$col->Field}: {$col->Type}\n";
    }
}
