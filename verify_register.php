<?php
// Mock $_POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['nama'] = 'Test User';
$_POST['username'] = 'testuser_' . time();
$_POST['password'] = 'testpassword123';

// Include the registration script
require 'register.php';
?>
