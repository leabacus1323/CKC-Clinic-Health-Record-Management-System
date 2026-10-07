<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('users')->where('email', 'admin@ckc.edu')->update(['user_type' => 0]);
echo "✓ Admin user_type updated to 0\n";
echo "Login now redirects to admin dashboard.\n";
