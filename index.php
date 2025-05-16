<?php
session_start();
$notes_file = 'notes.json';

if (!file_exists($notes_file)) {
    file_put_contents($notes_file, json_encode([]));
}

$categories = ['Personal', 'Work', 'Ideas', 'Misc']; // predefined categories

if (isset($_POST['login'])) {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'pass') {
        $_SESSION['logged_in'] = true;
    } else {
        $error = 'Invalid credentials.';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Add/Edit/Delete Notes
$notes = json_decode(file_get_contents($notes_file), true);

if ($_SESSION['logged_in'] ?? false) {
    if (isset($_POST['add_note'])) {
        $notes[] = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'tags' => explode(',', $_POST['tags']),
            'category' => $_POST['category'],
            'thumbnail' => $_POST['thumbnail'],
        ];
        file_put_contents($notes_file, json_encode($notes));
    }

    if (isset($_POST['edit_note'])) {
        $id = (int)$_POST['id'];
        $notes[$id] = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'tags' => explode(',', $_POST['tags']),
            'category' => $_POST['category'],
            'thumbnail' => $_POST['thumbnail'],
        ];
        file_put_contents($notes_file, json_encode($notes));
    }

    if (isset($_POST['delete_note'])) {
        $id = (int)$_POST['id'];
        array_splice($notes, $id, 1);
        file_put_contents($notes_file, json_encode($notes));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Notes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .note-card { margin-bottom: 20px; }
        .modal-lg { max-width: 90%; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        .tag-badge { margin-right: 5px; }
    </style>
      <link rel="stylesheet" href="style.css">
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/bh022e1z9boauypxqoaiqil6mowj7u5z35aaupskq58u2jum/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
                                         <div class="container mt-4">
    <?php if (!($_SESSION['logged_in'] ?? false)): ?>
        <div class="row justify-content-center">
                <details>
                    <summary>Admin Login</summary>

            <div class="box-overlay">
                         
                                    <h4>Admin Login</h4>
                <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <input name="username" class="form-control mb-2" placeholder="Username">
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password">
                    <button class="btn btn-primary w-100" name="login">Login</button>
                </form>
                
            </div>

                </details>
                
        </div>
    <?php else: ?>

    <details>
        <summary>Backend</summary>
        <div class="Backend-wrap">
            

            
                    <form method="POST">
            <button name="logout" class="btn btn-danger mb-3">Logout</button>
        </form>

        <h4>Add New Note</h4>
        <form method="POST">
            <input name="title" class="form-control mb-2" placeholder="Title" required>
            <textarea name="content" class="form-control mb-2 tinymce" placeholder="Content"></textarea>
            <input name="tags" class="form-control mb-2" placeholder="Tags (comma separated)">
            <select name="category" class="form-control mb-2" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>"><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
            <input name="thumbnail" class="form-control mb-2" placeholder="Image URL (optional)">
            <button name="add_note" class="btn btn-success">Add Note</button>
        </form>
        <hr>
        </div>
    </details>
    

    <?php endif; ?>

    <input type="text" id="search-input" class="form-control my-3" placeholder="Search notes...">

    <div id="notes-container">
        <?php foreach ($notes as $index => $note): ?>
            <div class="card note-card">
                <div class="card-body">
                    <?php if (!empty($note['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($note['thumbnail']) ?>" class="img-thumbnail mb-2" width="100">
                    <?php endif; ?>
                    <h5><?= htmlspecialchars($note['title']) ?></h5>
                    <div>
                        <?php foreach ($note['tags'] as $tag): ?>
                            <span class="badge bg-secondary tag-badge"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <p><?= strip_tags(substr($note['content'], 0, 30)) ?>...</p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($note['category'] ?? 'None') ?></p>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?= $index ?>">Read More</button>
                    <a href="note.php?note=<?= urlencode($note['slug']) ?>" class="btn btn-sm btn-outline-dark">Share</a>


                    <?php if ($_SESSION['logged_in'] ?? false): ?>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#edit<?= $index ?>">Edit</button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $index ?>">
                            <button name="delete_note" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <div id="edit<?= $index ?>" class="collapse mt-2">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $index ?>">
                                <input name="title" value="<?= htmlspecialchars($note['title']) ?>" class="form-control mb-2">
                                <textarea name="content" class="form-control mb-2 tinymce"><?= htmlspecialchars($note['content']) ?></textarea>
                                <input name="tags" value="<?= implode(',', $note['tags']) ?>" class="form-control mb-2">
                                <select name="category" class="form-control mb-2">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= $note['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input name="thumbnail" value="<?= htmlspecialchars($note['thumbnail'] ?? '') ?>" class="form-control mb-2">
                                <button name="edit_note" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="modal fade" id="modal<?= $index ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($note['title']) ?></h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"><?= $note['content'] ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
<script>
    tinymce.init({
        selector: '.tinymce',
        menubar: false,
        plugins: 'link code image preview',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image | code preview',
        branding: false
    });
</script>
</body>
</html>
