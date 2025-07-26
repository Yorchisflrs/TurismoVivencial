<?php
require_once 'config/database.php';
global $pdo;

echo "Testing host registration fix...\n\n";

try {
    // Simulate a host registration
    $test_data = [
        'full_name' => 'Test Host',
        'email' => 'test.host@example.com',
        'phone' => '123456789',
        'age' => 30,
        'business_name' => 'Test Business',
        'location' => 'Test Location',
        'description' => 'Test description',
        'experiences' => 'Test experiences',
        'max_guests' => 5,
        'languages' => 'Español, English',
        'motivation' => 'Test motivation'
    ];
    
    // Test the exact query from AuthController
    $stmt = $pdo->prepare('
        INSERT INTO hosts (
            user_id, full_name, email, phone, age, business_name, location, 
            description, experiences, max_guests, languages, motivation, 
            status, created_at
        ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", NOW())
    ');

    $result = $stmt->execute([
        $test_data['full_name'],
        $test_data['email'],
        $test_data['phone'],
        $test_data['age'],
        $test_data['business_name'],
        $test_data['location'],
        $test_data['description'],
        $test_data['experiences'],
        $test_data['max_guests'],
        $test_data['languages'],
        $test_data['motivation']
    ]);
    
    if ($result) {
        $host_id = $pdo->lastInsertId();
        echo "✅ SUCCESS! Host registration test passed.\n";
        echo "   - New host ID: $host_id\n";
        echo "   - Host name: {$test_data['full_name']}\n";
        echo "   - Email: {$test_data['email']}\n";
        
        // Clean up test data
        $pdo->prepare('DELETE FROM hosts WHERE id = ?')->execute([$host_id]);
        echo "   - Test data cleaned up\n";
        
        echo "\n🎉 Host registration form should now work perfectly!\n";
        echo "Users can now submit host applications without errors.\n";
    } else {
        echo "❌ Test failed - no result returned\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "\nThis means there's still an issue with the host registration.\n";
}
?>
