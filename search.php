<?php
require 'functions.php';

$keyword = $_GET['q'] ?? '';
$notes = loadNotes();
$results = [];

foreach ($notes as $note) {
    if (stripos($note['title'], $keyword) !== false || stripos($note['content'], $keyword) !== false) {
        $note['title'] = highlight($note['title'], $keyword);
        $note['content'] = highlight($note['content'], $keyword);
        $results[] = $note;
    }
}

function highlight($text, $word) {
    return preg_replace("/($word)/i", '<mark>$1</mark>', $text);
}

header('Content-Type: application/json');
echo json_encode($results);
