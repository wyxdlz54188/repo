<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 简单验证示例，实际应用需要更严格的安全措施
function verifyPurchase($package_id, $device_udid, $token) {
    // 连接数据库
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
    
    // 查询有效的购买记录
    $stmt = $pdo->prepare("SELECT * FROM purchases WHERE package_id = ? AND device_udid = ? AND license_token = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
    $stmt->execute([$package_id, $device_udid, $token]);
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($purchase) {
        return ['status' => 'valid', 'purchase_date' => $purchase['purchased_at']];
    } else {
        return ['status' => 'invalid', 'reason' => 'License not found or expired'];
    }
}

// 处理验证请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $result = verifyPurchase(
        $input['package_id'] ?? '',
        $input['udid'] ?? '', 
        $input['token'] ?? ''
    );
    echo json_encode($result);
}
?>
