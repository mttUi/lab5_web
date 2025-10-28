<?php
require 'db.php';

try {
    // Создаем таблицу conferences
    $sql = "CREATE TABLE IF NOT EXISTS conferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        birth_year INT NOT NULL,
        section VARCHAR(100) NOT NULL,
        need_certificate TINYINT(1) DEFAULT 0,
        participation_form VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Таблица 'conferences' успешно создана или уже существует";
    
} catch (\PDOException $e) {
    echo "❌ Ошибка при создании таблицы: " . $e->getMessage();
}
?>