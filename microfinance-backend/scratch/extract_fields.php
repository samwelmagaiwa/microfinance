<?php
$binafsi = file_get_contents('c:/xampp/htdocs/microfinance/microfinance-front/src/app/features/borrowers/pages/mkopo-binafsi/mkopo-binafsi.component.ts');
$kikundi = file_get_contents('c:/xampp/htdocs/microfinance/microfinance-front/src/app/features/borrowers/pages/mkopo-kikundi/mkopo-kikundi.component.ts');

function extractFields($content) {
    preg_match_all('/ngModel\)\]="formData\.([a-zA-Z0-9_.]+)"/', $content, $matches);
    $fields = array_unique($matches[1]);
    sort($fields);
    return $fields;
}

echo "--- BINAFI FIELDS ---\n";
print_r(extractFields($binafsi));

echo "\n--- KIKUNDI FIELDS ---\n";
print_r(extractFields($kikundi));
