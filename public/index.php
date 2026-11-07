<?php

declare(strict_types=1);

/**
 * ==============================================================================
 * MESALY — Front Controller
 * ==============================================================================
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new \App\Core\App();
$app->run();
