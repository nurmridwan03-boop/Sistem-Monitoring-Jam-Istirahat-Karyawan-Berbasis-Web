<?php
/**
 * Script untuk memperbaiki tabel istirahat yang corrupt
 * Jalankan file ini untuk repair tabel istirahat
 */

include 'db.php';

echo "<h3>Memperbaiki Tabel istirahat...</h3>";

// Repair tabel istirahat
$repair_query = "REPAIR TABLE istirahat";
$result = mysqli_query($conn, $repair_query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<p><strong>Status:</strong> " . htmlspecialchars($row['Msg_text']) . "</p>";
    
    if ($row['Msg_type'] == 'status') {
        echo "<p style='color: green;'><strong>✓ Tabel berhasil diperbaiki!</strong></p>";
    } else {
        echo "<p style='color: orange;'><strong>⚠ " . htmlspecialchars($row['Msg_text']) . "</strong></p>";
    }
} else {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . mysqli_error($conn) . "</p>";
    echo "<p>Mencoba metode alternatif...</p>";
    
    // Coba check table dulu
    $check_query = "CHECK TABLE istirahat";
    $check_result = mysqli_query($conn, $check_query);
    
    if ($check_result) {
        while ($row = mysqli_fetch_assoc($check_result)) {
            echo "<p><strong>Check Result:</strong> " . htmlspecialchars($row['Msg_text']) . "</p>";
        }
    }
}

echo "<hr>";
echo "<h3>Mengecek Struktur Tabel...</h3>";

// Cek struktur tabel
$structure_query = "DESCRIBE istirahat";
$structure_result = mysqli_query($conn, $structure_query);

if ($structure_result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($structure_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
    echo "<p>Tabel mungkin tidak ada atau sangat rusak. Anda mungkin perlu membuat ulang tabel.</p>";
}

mysqli_close($conn);
?>

