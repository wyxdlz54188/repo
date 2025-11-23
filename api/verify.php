<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

class LicenseVerifier {
    private $pdo;
    
    public function __construct() {
        $this->connectDB();
    }
    
    private function connectDB() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    }
    
    public function verifyLicense($package_id, $device_udid, $token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* 
                FROM payments p 
                WHERE p.package_id = ? AND p.device_udid = ? AND p.license_token = ? 
                AND p.status = 'completed' 
                AND (p.expires_at IS NULL OR p.expires_at > NOW())
            ");
            
            $stmt->execute([$package_id, $device_udid, $token]);
            $license = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($license) {
                return [
                    'status' => 'valid',
                    'purchase_date' => $license['created_at'],
                    'expires_at' => $license['expires_at']
                ];
            } else {
                return ['status' => 'invalid', 'reason' => 'License not found or expired'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'reason' => 'Server error'];
        }
    }
}

// 处理验证请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $verifier = new LicenseVerifier();
    $result = $verifier->verifyLicense(
        $input['package_id'] ?? '',
        $input['udid'] ?? '',
        $input['token'] ?? ''
    );
    
    echo json_encode($result);
}
?>
