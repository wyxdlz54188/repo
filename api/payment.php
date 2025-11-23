<?php
// 示例：使用Stripe处理支付
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('your_stripe_secret_key');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Sileo Package: ' . $input['package_id']],
                    'unit_amount' => $input['amount'] * 100, // 转换为分
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://yourrepo.com/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://yourrepo.com/cancel',
        ]);
        
        echo json_encode(['session_id' => $session->id]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
