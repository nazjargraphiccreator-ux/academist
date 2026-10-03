<?php
$file_path = 'functions.php';
$content = file_get_contents($file_path);

// Normalize line endings
$content = str_replace("\r\n", "\n", $content);

// Regex to find sections that start with /* ========...
$pattern = '/\/\* ={60}\n\s*(.*?)\n\s*={60} \*\//s';
preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

if (empty($matches[0])) {
    die("No sections found.\n");
}

$new_functions_php = "";
$last_offset = 0;

$includes = [];

foreach ($matches[0] as $index => $match_data) {
    $full_match = $match_data[0];
    $offset = $match_data[1];
    
    // Get the title
    $title = trim($matches[1][$index][0]);
    
    // Generate a filename based on the title
    $filename = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
    $filename = trim($filename, '-');
    $filename = preg_replace('/^[0-9\-]+/', '', $filename); // Remove leading numbers like "1-" or "0-2-"
    $filename = 'inc/sec-' . $filename . '.php';
    
    // Find the end of this section (the start of the next section, or end of file)
    $next_offset = isset($matches[0][$index + 1]) ? $matches[0][$index + 1][1] : strlen($content);
    
    // Extract the content for this section
    $section_content = substr($content, $offset, $next_offset - $offset);
    
    // Save to file
    file_put_contents($filename, "<?php\n" . $section_content);
    
    // Append the text before this section to our new functions.php
    $new_functions_php .= substr($content, $last_offset, $offset - $last_offset);
    
    // Append the require statement
    $new_functions_php .= "\nrequire_once get_stylesheet_directory() . '/" . $filename . "';\n\n";
    
    $last_offset = $next_offset;
}

// Append any remaining content
$new_functions_php .= substr($content, $last_offset);

// Backup the original
copy($file_path, '_backups/functions_backup_' . time() . '.php');

// Write the new functions.php
file_put_contents($file_path, $new_functions_php);

echo "Successfully extracted " . count($matches[0]) . " sections!\n";
?>
