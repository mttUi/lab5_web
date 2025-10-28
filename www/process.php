<?php
// Включаем буферизацию вывода
ob_start();

require 'db.php';
require 'Conference.php';

// Создаем экземпляр класса Conference
$conference = new Conference($pdo);

// Получаем и проверяем данные формы
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$birth_year = intval($_POST['birth_year'] ?? 0);
$section = htmlspecialchars(trim($_POST['section'] ?? ''));
$need_certificate = isset($_POST['need_certificate']) ? 1 : 0;
$participation_form = htmlspecialchars(trim($_POST['participation_form'] ?? ''));

// Валидация данных
$errors = [];

if (empty($name)) {
    $errors[] = "ФИО обязательно для заполнения";
}

if ($birth_year < 1900 || $birth_year > (date('Y') - 16)) {
    $errors[] = "Некорректный год рождения";
}

if (empty($section)) {
    $errors[] = "Необходимо выбрать секцию";
}

if (empty($participation_form)) {
    $errors[] = "Необходимо выбрать форму участия";
}

// Если есть ошибки, показываем их
if (!empty($errors)) {
    session_start();
    $_SESSION['errors'] = $errors;
    header("Location: form.html");
    exit();
}

// Сохраняем данные в базу
try {
    $conference_id = $conference->add($name, $birth_year, $section, $need_certificate, $participation_form);
    
    // Сохраняем успешное сообщение в сессии
    session_start();
    $_SESSION['success'] = "Регистрация успешно завершена! ID вашей заявки: " . $conference_id;
    
} catch (Exception $e) {
    session_start();
    $_SESSION['errors'] = ["Ошибка при сохранении данных: " . $e->getMessage()];
}

// Очищаем буфер и перенаправляем
ob_end_clean();
header("Location: index.php");
exit();
?>