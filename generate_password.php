<?php
// Script to generate password hashes for testing

$passwords = [
    'admin123' => 'Admin Guru (admin@guru.com)',
    'pembimbing123' => 'Pembimbing A (pembimbing@example.com)',
    'andi123' => 'Siswa Andi (andi@siswa.com)',
    'budi123' => 'Siswa Budi (budi@siswa.com)'
];

echo "Password hashes untuk testing:\n\n";

foreach ($passwords as $plain => $desc) {
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    echo "$desc:\n";
    echo "Password: $plain\n";
    echo "Hash: $hash\n\n";
}
?>
