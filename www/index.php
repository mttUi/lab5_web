<?php
require 'db.php';
require 'Conference.php';

// Создаем экземпляр класса Conference
$conference = new Conference($pdo);

// Получаем все записи
$registrations = $conference->getAll();

// Получаем статистику
$stats = $conference->getStats();

// Проверяем сообщения
session_start();
$success = $_SESSION['success'] ?? '';
$errors = $_SESSION['errors'] ?? [];

// Очищаем сообщения после показа
unset($_SESSION['success']);
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация на конференцию - Главная</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .nav a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .nav a:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .stats-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #48cae4;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .registrations-section {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .registrations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .registrations-table th,
        .registrations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .registrations-table th {
            background: #667eea;
            color: white;
        }
        
        .registrations-table tr:hover {
            background: #f5f5f5;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .certificate-yes {
            color: #28a745;
            font-weight: bold;
        }
        
        .certificate-no {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Система регистрации на конференцию</h1>
        
        <div class="nav">
            <a href="form.html">📝 Новая регистрация</a>
            <a href="init.php" onclick="return confirm('Это служебная страница для инициализации БД. Перейти?')">🛠️ Инициализация БД</a>
            <a href="http://localhost:8081" target="_blank">📊 Adminer (управление БД)</a>
        </div>

        <!-- Сообщения об успехе/ошибках -->
        <?php if (!empty($success)): ?>
            <div class="success-message">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                ❌ <strong>Ошибки:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Статистика -->
        <div class="stats-section">
            <h3>📊 Статистика регистраций</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div>Всего участников</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['need_certificate']; ?></div>
                    <div>Нужен сертификат</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($stats['by_section']); ?></div>
                    <div>Секций</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($stats['by_form']); ?></div>
                    <div>Форм участия</div>
                </div>
            </div>
        </div>

        <!-- Список регистраций -->
        <div class="registrations-section">
            <h3>📋 Зарегистрированные участники</h3>
            
            <?php if (empty($registrations)): ?>
                <div class="empty-state">
                    <p>😔 Пока нет зарегистрированных участников</p>
                    <p><a href="form.html" style="color: #667eea;">Будьте первым!</a></p>
                </div>
            <?php else: ?>
                <table class="registrations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Год рождения</th>
                            <th>Секция</th>
                            <th>Сертификат</th>
                            <th>Форма участия</th>
                            <th>Дата регистрации</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($reg['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($reg['birth_year']); ?></td>
                                <td><?php echo htmlspecialchars($reg['section']); ?></td>
                                <td class="<?php echo $reg['need_certificate'] ? 'certificate-yes' : 'certificate-no'; ?>">
                                    <?php echo $reg['need_certificate'] ? '✅ Да' : '❌ Нет'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($reg['participation_form']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($reg['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Информация о БД -->
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
            <p>💾 Данные хранятся в MySQL | 🐳 Работает на Docker | 🔗 <a href="http://localhost:8081" target="_blank">Adminer: localhost:8081</a></p>
        </div>
    </div>
</body>
</html>