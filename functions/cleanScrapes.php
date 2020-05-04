<?php
//quick script to clear out blank pages (should have checked status code)
$directory = "../notes/scrapped_pages";
$files = glob($directory.'/*');

foreach ($files as $file) {
    if (empty(trim(file_get_contents($file)))) {
        echo "Unlinking $file\n";
        unlink($file);
    }
}