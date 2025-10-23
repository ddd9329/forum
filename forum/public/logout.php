<?php
require_once __DIR__ . '/../inc/auth.php';
logout();
header('Location: ' . base_url('index.php'));
exit;
