<?php
$pdo = new PDO('mysql:host=localhost;dbname=hogartours', 'root', '');
$stmt = $pdo->query('DESCRIBE package_images');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Columns in package_images table:\n";
foreach($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
}
?>
