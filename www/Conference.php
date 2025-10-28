<?php
class Conference {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($name, $birth_year, $section, $need_certificate, $participation_form) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO conferences (name, birth_year, section, need_certificate, participation_form) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $birth_year, $section, $need_certificate, $participation_form]);
        return $this->pdo->lastInsertId();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM conferences ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM conferences WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getStats() {
        $stats = [];
        
        try {
            // Общее количество участников
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM conferences");
            $stats['total'] = $stmt->fetch()['total'] ?? 0;
            
            // Количество участников по секциям
            $stmt = $this->pdo->query("SELECT section, COUNT(*) as count FROM conferences GROUP BY section");
            $stats['by_section'] = $stmt->fetchAll();
            
            // Количество участников по форме участия
            $stmt = $this->pdo->query("SELECT participation_form, COUNT(*) as count FROM conferences GROUP BY participation_form");
            $stats['by_form'] = $stmt->fetchAll();
            
            // Количество нуждающихся в сертификате
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM conferences WHERE need_certificate = 1");
            $stats['need_certificate'] = $stmt->fetch()['count'] ?? 0;
            
            // Количество уникальных секций
            $stats['sections_count'] = count($stats['by_section']);
            
            // Количество уникальных форм участия
            $stats['forms_count'] = count($stats['by_form']);
            
        } catch (Exception $e) {
            // Если произошла ошибка, возвращаем значения по умолчанию
            $stats['total'] = 0;
            $stats['by_section'] = [];
            $stats['by_form'] = [];
            $stats['need_certificate'] = 0;
            $stats['sections_count'] = 0;
            $stats['forms_count'] = 0;
        }
        
        return $stats;
    }
}
?>