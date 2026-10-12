<?php
// restart.php

// --- CONFIG ---
$secret_token = "kamal123dfsdfdfsdsf"; // Change this to something long and random

// --- SECURITY CHECK ---
if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    http_response_code(403);
    die("Unauthorized access.");
}

// --- RUN RESTART COMMAND ---
$output = shell_exec('sudo /sbin/reboot 2>&1');

// --- RESPONSE ---
if ($output === null) {
    echo "Restart initiated successfully.";
} else {
    echo "Command output:\n" . htmlspecialchars($output);
}
?>
