<?php
// 设置允许跨域访问
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
 
// 如果是OPTIONS请求，直接返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
 
// 获取请求参数
$model = isset($_GET['model']) ? $_GET['model'] : '';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
$api_token = isset($_GET['api_token']) ? $_GET['api_token'] : '';
 
// 验证必要参数
if (empty($model) || empty($account_id) || empty($api_token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => ['Missing required parameters']]);
    exit;
}
 
// 获取请求体
$request_body = file_get_contents('php://input');
 
// 构建请求URL
$url = "https://api.cloudflare.com/client/v4/accounts/{$account_id}/ai/run/{$model}";
 
// 初始化cURL
$ch = curl_init($url);
 
// 设置cURL选项
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_token,
    'Content-Type: application/json'
]);
 
// 执行请求
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
 
// 检查是否有错误
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['cURL error: ' . curl_error($ch)]]);
    exit;
}
 
// 关闭cURL
curl_close($ch);
 
// 检查是否为图像响应
if (strpos($content_type, 'image/') === 0) {
    // 如果是图像，直接传递图像数据和内容类型
    header('Content-Type: ' . $content_type);
    http_response_code($http_code);
    echo $response;
} else {
    // 否则作为JSON响应处理
    header('Content-Type: application/json');
    http_response_code($http_code);
    echo $response;
} 


ref(APA): tkttn0714.天空天堂鸟的博客.https://071400.xyz. Retrieved 2025/6/14.