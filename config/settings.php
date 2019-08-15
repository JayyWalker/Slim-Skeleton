<?php
declare(strict_types=1);

return [
    // Should be set to false in production
    'displayErrorDetails' => getenv('DEBUG_MODE') ?? false,
];
