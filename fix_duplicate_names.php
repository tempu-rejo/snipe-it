<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$users = User::where('ldap_import', 1)->get();
$fixed = 0;

foreach ($users as $user) {
    $firstname = trim($user->first_name);
    $lastname = trim($user->last_name);
    
    // Check if lastname contains firstname (case insensitive)
    if (!empty($firstname) && !empty($lastname) && stripos($lastname, $firstname) !== false) {
        // Split the full name from concatenation
        $fullname = $firstname . ' ' . $lastname;
        $parts = preg_split('/\s+/', $fullname);
        
        // Remove duplicates while preserving order
        $unique_parts = [];
        $seen = [];
        foreach ($parts as $part) {
            $lower = strtolower($part);
            if (!isset($seen[$lower])) {
                $unique_parts[] = $part;
                $seen[$lower] = true;
            }
        }
        
        if (count($unique_parts) > 1) {
            $new_firstname = $unique_parts[0];
            $new_lastname = implode(' ', array_slice($unique_parts, 1));
            
            echo "Fixing: $firstname $lastname -> $new_firstname $new_lastname\n";
            
            $user->first_name = $new_firstname;
            $user->last_name = $new_lastname;
            $user->save();
            $fixed++;
        }
    }
}

echo "\nTotal fixed: $fixed users\n";
