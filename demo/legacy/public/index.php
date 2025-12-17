<?php

echo "<!DOCTYPE html>
<html>
<head>
    <title>Legacy Laravel 5.8 Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #ff2d20; }
        .info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔧 Legacy Laravel 5.8 Demo</h1>
    <div class='warning'>
        <p><strong>⚠️ Legacy Project</strong></p>
        <p>This is a legacy project using PHP 7.4 and Laravel 5.8 for testing compatibility.</p>
    </div>
    <div class='info'>
        <p><strong>Framework:</strong> Laravel 5.8</p>
        <p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>
        <p><strong>Status:</strong> ✅ Running</p>
    </div>
    <p>This is a demo project to test the Composer Update Helper with legacy projects.</p>
</body>
</html>";

