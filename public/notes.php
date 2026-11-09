<?php
$config = require __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erreur de connexion à la base.');
}

// Écriture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['contenu'] ?? ''))) {
    $stmt = $pdo->prepare('INSERT INTO notes (contenu) VALUES (:c)');
    $stmt->execute([':c' => trim($_POST['contenu'])]);
    header('Location: notes.php?ok=1');
    exit;
}

// Lecture
$notes = $pdo->query('SELECT id, contenu, created_at FROM notes ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Notes</title></head>
<body>
  <h1>Notes</h1>
  <?php if (isset($_GET['ok'])): ?><p><strong>Note ajoutée avec succès.</strong></p><?php endif; ?>

  <form method="post">
    <input type="text" name="contenu" placeholder="Nouvelle note" required>
    <button type="submit">Ajouter</button>
  </form>

  <ul>
    <?php foreach ($notes as $n): ?>
      <li>#<?= (int)$n['id'] ?> : <?= htmlspecialchars($n['contenu']) ?> (<?= $n['created_at'] ?>)</li>
    <?php endforeach; ?>
  </ul>
  <p><a href="index.html">Retour</a></p>
</body>
</html>
