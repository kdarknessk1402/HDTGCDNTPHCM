<?php
/**
 * File: public/index.php
 * Entry point của ứng dụng
 */

// Start session
session_start();

// Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

// Load all controllers
foreach (glob(__DIR__ . '/../controllers/*.php') as $controller_file) {
    require_once $controller_file;
}

// Load all models
foreach (glob(__DIR__ . '/../models/*.php') as $model_file) {
    require_once $model_file;
}

// Routes
require_once __DIR__ . '/../config/routes.php';