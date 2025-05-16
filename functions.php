<?php
function loadNotes() {
    $data = file_get_contents('notes.json');
    return json_decode($data, true) ?? [];
}

function saveNotes($notes) {
    file_put_contents('notes.json', json_encode($notes, JSON_PRETTY_PRINT));
}

function findNoteById($id) {
    $notes = loadNotes();
    foreach ($notes as $note) {
        if ($note['id'] == $id) {
            return $note;
        }
    }
    return null;
}
