<?php
ob_start();
session_start();

require_once __DIR__ . '/../../includes/config.php';

unset($_SESSION['user']);
header('Location: ' . BASE_URL . 'portal/login');
exit;
