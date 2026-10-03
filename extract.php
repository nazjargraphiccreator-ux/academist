<?php
$content = file_get_contents('functions.php');
$lines = explode(PHP_EOL, $content);

// Function to extract a chunk and replace with require_once
function extract_chunk(&$lines, $start_line, $end_line, $filename, $folder = 'inc') {
    $chunk = array_slice($lines, $start_line - 1, $end_line - $start_line + 1);
    file_put_contents($folder . '/' . $filename, '<?php' . PHP_EOL . implode(PHP_EOL, $chunk));
    
    // Replace the extracted lines with an empty string, except the first line which becomes the require
    for ($i = $start_line; $i < $end_line; $i++) {
        $lines[$i] = '';
    }
    $lines[$start_line - 1] = "\nrequire_once get_stylesheet_directory() . '/$folder/$filename';\n";
}

// We need to map the exact line numbers dynamically to avoid drift if we do multiple.
// Actually, it's safer to do this with regex or string replacement, or just do it line by line based on search.

?>
