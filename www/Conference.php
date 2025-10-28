<?php
class Conference {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Добавление новой регистрации
    public function add($name, $birth_year, $section, $need_certificate, $participation_form) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO conferences (name, birth_year, section, need_certificate, participation_form) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $birth_year, $section, $need_certificate, $participation_form]);
        return $this->pdo->lastInsertId();
    }

    // Получение всех записей
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM conferences ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Получение записи по ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM conferences WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Обновление записи
    public function update($id, $name, $birth_year, $section, $need_certificate, $participation_form) {
        $stmt = $this->pdo->prepare(
            "UPDATE conferences SET name=?, birth_year=?, section=?, need_certificate=?, participation_form=? WHERE id=?"
        );
        $stmt->execute([$name, $birth_year, $section, $need_certificate, $participation_form, $id]);
        return $stmt->rowCount();
    }

    // Удаление записи
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM conferences WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // Получение статистики
    public function getStats() {
        $stats = [];
        
        // Общее количество участников
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM conferences");
        $stats['total'] = $stmt->fetch()['total'];
        
        // Количество участников по секциям
        $stmt = $this->pdo->query("SELECT section, COUNT(*) as count FROM conferences GROUP BY section");
        $stats['by_section'] = $stmt->fetchAll();
        
        // Количество участников по форме участия
        $stmt = $this->pdo->query("SELECT participation_form, COUNT(*) as count FROM conferences GROUP BY participation_form");
        $stats['by_form'] = $stmt->fetchAll();
        
        // Количество нуждающихся в сертификате
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM conferences WHERE need_certificate = 1");
        $stats['need_certificate'] = $stmt->fetch()['count'];
        
        return $stats;
    }
}
?>