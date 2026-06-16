<?php
$rows = isset($argv[1]) ? (int)$argv[1] : 100000;
$file = 'transactions.csv';

$handle = fopen($file, 'w');
fputcsv($handle, [
    'accountNumberFrom', 'accountNumberTypeFrom', 
    'accountNumberTo', 'accountNumberTypeTo',
    'traceNumber', 'amount', 'type', 'description', 'reference'
]);

$types = ['credit', 'debit'];
$accountTypes = ['savings', 'checking'];

echo "Generating $rows rows...\n";

for ($i = 1; $i <= $rows; $i++) {
    $traceNumber = strtoupper(bin2hex(random_bytes(6)));
    fputcsv($handle, [
        rand(1000000000, 9999999999),
        $accountTypes[array_rand($accountTypes)],
        rand(1000000000, 9999999999),
        $accountTypes[array_rand($accountTypes)],
        $traceNumber,
        round(rand(100, 1000000) / 100, 2),
        $types[array_rand($types)],
        "Transaction $i",
        "REF" . str_pad($i, 8, '0', STR_PAD_LEFT)
    ]);

    if ($i % 10000 === 0) {
        echo "Generated $i rows...\n";
    }
}

fclose($handle);
echo "CSV generated: $file\n";