<?php
// 安装脚本 - 创建数据库表
require_once 'config.php';

try {
    $pdo = getDB();
    
    // 创建分类表
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        parent_id INT NULL,
        is_password_enabled TINYINT(1) DEFAULT 0,
        password VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // 创建网站表
    $pdo->exec("CREATE TABLE IF NOT EXISTS sites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        url VARCHAR(191) NOT NULL UNIQUE,
        description TEXT,
        icon TEXT,
        sort_order INT DEFAULT 0,
        category_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "✓ 数据库表创建成功！<br>";
    echo "<a href='index.php'>前往首页</a> | <a href='admin.php'>前往后台</a>";
    
} catch(PDOException $e) {
    die("安装失败: " . $e->getMessage());
}
?>
