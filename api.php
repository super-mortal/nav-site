<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// 获取所有分类
if ($action === 'categories' && $method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order DESC, id ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// 添加分类
if ($action === 'categories' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO categories (name, sort_order, is_password_enabled, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['sortOrder'] ?? 0,
        $data['isPasswordEnabled'] ?? 0,
        $data['password'] ?? null
    ]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
    exit;
}

// 更新分类
if ($action === 'category_update' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE categories SET name=?, sort_order=?, is_password_enabled=?, password=? WHERE id=?");
    $stmt->execute([
        $data['name'],
        $data['sortOrder'] ?? 0,
        $data['isPasswordEnabled'] ?? 0,
        $data['password'] ?? null,
        $data['id']
    ]);
    echo json_encode(['success' => true]);
    exit;
}

// 删除分类
if ($action === 'category_delete' && $method === 'POST') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

// 验证分类密码
if ($action === 'verify_password' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("SELECT password FROM categories WHERE id=?");
    $stmt->execute([$data['categoryId']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $success = $category && $category['password'] === $data['password'];
    echo json_encode(['success' => $success]);
    exit;
}

// 获取网站列表
if ($action === 'sites' && $method === 'GET') {
    $categoryId = $_GET['categoryId'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM sites WHERE category_id=? ORDER BY sort_order DESC, id ASC");
    $stmt->execute([$categoryId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// 添加网站
if ($action === 'sites' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO sites (title, url, description, icon, sort_order, category_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['title'],
        $data['url'],
        $data['description'] ?? '',
        $data['icon'] ?? '',
        $data['sortOrder'] ?? 0,
        $data['categoryId']
    ]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
    exit;
}

// 更新网站
if ($action === 'site_update' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE sites SET title=?, url=?, description=?, icon=?, sort_order=? WHERE id=?");
    $stmt->execute([
        $data['title'],
        $data['url'],
        $data['description'] ?? '',
        $data['icon'] ?? '',
        $data['sortOrder'] ?? 0,
        $data['id']
    ]);
    echo json_encode(['success' => true]);
    exit;
}

// 删除网站
if ($action === 'site_delete' && $method === 'POST') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM sites WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
