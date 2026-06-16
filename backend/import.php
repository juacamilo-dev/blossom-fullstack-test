<?php
require_once 'config.php';

$csvFile = $argv[1] ?? 'transactions.csv';

if (!file_exists($csvFile)) {
    echo "Error: File $csvFile not found\n";
    exit(1);
}

$startTime = microtime(true);
$batchSize = 1000;
$batch = [];
$totalInserted = 0;
$lineCount = 0;

echo "Starting import from $csvFile...\n";

$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle);

$pdo->exec('SET autocommit=0');
$pdo->exec('SET unique_checks=0');
$pdo->exec('SET foreign_key_checks=0');

$stmt = $pdo->prepare("
    INSERT INTO transactions 
    (accountNumberFrom, accountNumberTypeFrom, accountNumberTo, 
     accountNumberTypeTo, traceNumber, amount, type, description, reference)
    VALUES 
    (:accountNumberFrom, :accountNumberTypeFrom, :accountNumberTo,
     :accountNumberTypeTo, :traceNumber, :amount, :type, :description, :reference)
");

while (($row = fgetcsv($handle)) !== false) {
    $lineCount++;
    
    $stmt->execute([
        ':accountNumberFrom' => $row[0],
        ':accountNumberTypeFrom' => $row[1],
        ':accountNumberTo' => $row[2],
        ':accountNumberTypeTo' => $row[3],
        ':traceNumber' => $row[4],
        ':amount' => $row[5],
        ':type' => $row[6],
        ':description' => $row[7] ?? null,
        ':reference' => $row[8] ?? null
    ]);

    $totalInserted++;

    if ($totalInserted % $batchSize === 0) {
        $pdo->exec('COMMIT');
        $elapsed = round(microtime(true) - $startTime, 2);
        echo "Inserted $totalInserted rows... ({$elapsed}s)\n";
        $pdo->exec('START TRANSACTION');
    }
}

$pdo->exec('COMMIT');
$pdo->exec('SET autocommit=1');
$pdo->exec('SET unique_checks=1');
$pdo->exec('SET foreign_key_checks=1');

fclose($handle);

$totalTime = round(microtime(true) - $startTime, 2);
$rowsPerSecond = round($totalInserted / $totalTime);

echo "\n=== Import Complete ===\n";
echo "Total rows inserted: $totalInserted\n";
echo "Total time: {$totalTime}s\n";
echo "Speed: {$rowsPerSecond} rows/second\n";