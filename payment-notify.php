<?php
// payment-notify.php - Receive payment notification
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['status']) && $data['status'] == 'completed') {
    require_once 'inc/config.php';
    require_once 'inc/send-email.php';
    
    // Send payment confirmation to user
    sendPaymentConfirmation(
        $data['email'],
        $data['name'],
        $data['vin'],
        $data['package'],
        $data['amount']
    );
    
    // Send admin notification about payment
    $adminData = [
        'full_name' => $data['name'],
        'email' => $data['email'],
        'vin' => $data['vin'],
        'package' => $data['package'],
        'amount' => $data['amount']
    ];
    sendAdminNotificationSMTP($adminData);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>