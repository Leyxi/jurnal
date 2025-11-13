<?php
require_once 'config.php';

// Destroy session
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit();
?>
