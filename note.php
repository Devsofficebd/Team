<?php
$notes = json_decode(file_get_contents('notes.json'), true);
$slug = $_GET['note'] ?? '';

$note = null;
foreach ($notes as $n) {
    if ($n['slug'] === $slug) {
        $note = $n;
        break;
    }
}

if (!$note) {
    echo "<h2>Note not found</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($note['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background: #f1f1f1;
            padding: 10px;
            overflow-x: auto;
        }
    </style>
</head>
<body class="container py-4">

    <a href="index.php" class="btn btn-secondary mb-3">&larr; Back to All Notes</a>

    <h1><?= htmlspecialchars($note['title']) ?></h1>
    <?php if (!empty($note['thumbnail'])): ?>
        <img src="<?= htmlspecialchars($note['thumbnail']) ?>" alt="Thumbnail" class="img-fluid mb-3">
    <?php endif; ?>

    <div>
        <?= $note['content'] ?>
    </div>

    <hr>
    <p><strong>Category:</strong> <?= htmlspecialchars($note['category']) ?></p>
    <p>
        <?php foreach ($note['tags'] as $tag): ?>
            <span class="badge bg-info text-dark me-1"><?= htmlspecialchars($tag) ?></span>
        <?php endforeach; ?>
    </p>

</body>
</html>
