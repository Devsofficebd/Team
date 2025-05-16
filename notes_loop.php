<?php
if (!isset($notes)) {
    session_start();
    $is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    $notes_file = 'notes.json';
    $notes = json_decode(file_get_contents($notes_file), true);
}

$search_query = $_GET['q'] ?? '';
$highlighted_notes = [];

foreach ($notes as $index => $note) {
    $match = false;
    $title = $note['title'];
    $content = $note['content'];
    $thumbnail = $note['thumbnail'] ?? '';
    
    if ($search_query) {
        $pattern = '/' . preg_quote($search_query, '/') . '/i';
        $match = preg_match($pattern, $title) || preg_match($pattern, $content);
        $title = preg_replace($pattern, '<mark>$0</mark>', $title);
        $content = preg_replace($pattern, '<mark>$0</mark>', $content);
    }

    if ($search_query && !$match) continue;

    echo '<div class="card note-card mb-3">
        <div class="card-body">
            ' . ($thumbnail ? "<img src='$thumbnail' class='img-thumbnail float-end ms-3' width='100'>" : '') . '
            <h5 class="card-title">' . $title . '</h5>
            <p class="card-text">' . substr(strip_tags($content), 0, 30) . '...</p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal' . $index . '">Read More</button>';

    if ($is_logged_in) {
        echo '
            <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="' . $index . '">
                <button class="btn btn-danger btn-sm" name="delete_note">Delete</button>
            </form>
            <button class="btn btn-warning btn-sm" data-bs-toggle="collapse" data-bs-target="#edit' . $index . '">Edit</button>
            <div id="edit' . $index . '" class="collapse mt-2">
                <form method="POST">
                    <input type="hidden" name="id" value="' . $index . '">
                    <input type="text" name="title" value="' . htmlspecialchars($note['title']) . '" class="form-control mb-2">
                    <textarea name="content" rows="5" class="form-control mb-2">' . htmlspecialchars($note['content']) . '</textarea>
                    <input type="text" name="thumbnail" value="' . htmlspecialchars($thumbnail) . '" class="form-control mb-2" placeholder="Thumbnail URL">
                    <button class="btn btn-primary" name="edit_note">Save Changes</button>
                </form>
            </div>';
    }

    echo '</div></div>';

    echo '<div class="modal fade" id="modal' . $index . '" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">' . strip_tags($title) . '</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">' . $content . '</div>
            </div>
        </div>
    </div>';
}
?>
