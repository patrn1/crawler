<?php
error_reporting(0);

// Dependencies
require_once 'vendor/autoload.php';

// Database connection
require_once 'app/db.php';

// Cache
require_once 'app/response_cache.php';
$ResponseCache = new \App\ResponseCache();

// Utilities
require_once 'app/utils.php';

// Models
require_once 'app/models/Domain.php';
require_once 'app/models/Element.php';
require_once 'app/models/Request.php';
require_once 'app/models/Path.php';

// Routes
require_once 'app/routes.php';
