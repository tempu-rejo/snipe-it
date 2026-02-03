<?php
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Login as admin
$user = App\Models\User::where('permissions->superuser', '1')->first();
if (!$user) {
    echo "No superuser found\n";
    exit(1);
}

Auth::login($user);
echo "Logged in as: " . $user->username . "\n\n";

// Test the activity API
$targetUser = App\Models\User::find(129);
if (!$targetUser) {
    echo "User 129 not found\n";
    exit(1);
}

echo "Testing activity API for user: " . $targetUser->present()->fullName() . "\n\n";

// Simulate API request
$request = Request::create('/api/v1/reports/activity?target_id=129&target_type=user', 'GET');
$request->headers->set('Accept', 'application/json');
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Response:\n";
echo $response->getContent() . "\n";
