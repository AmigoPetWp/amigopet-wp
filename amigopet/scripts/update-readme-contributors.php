<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Script to update contributors in readme.txt
 */

$repo = "AmigoPetWp/amigopet-wp";
$readme_path = __DIR__ . '/../readme.txt';
$token = getenv('GITHUB_TOKEN');

echo "Fetching contributors for $repo...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/$repo/contributors");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "PHP Script");
if ($token) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: token $token"]);
}

$response = curl_exec($ch);
curl_close($ch);

$contributors = json_decode($response, true);

if (!is_array($contributors)) {
    echo "Error fetching contributors: " . print_r($response, true) . "\n";
    exit(1);
}

$names = [];
foreach ($contributors as $contributor) {
    if ($contributor['type'] === 'User') {
        $names[] = $contributor['login'];
    }
}

$contributors_text = implode(", ", $names);
echo "Found contributors: $contributors_text\n";

if (!file_exists($readme_path)) {
    echo "Readme file not found at $readme_path\n";
    exit(1);
}

$content = file_get_contents($readme_path);

// Define the section marker
$marker_start = "== Contributors ==";
$marker_end = "== Description =="; // Assume Description follows Contributors or just append

if (strpos($content, $marker_start) !== false) {
    // Replace existing section
    $pattern = "/== Contributors ==.*?(?===|$)/s";
    $replacement = "== Contributors ==\n\nSupporting this project: " . $contributors_text . "\n\n";
    $content = preg_replace($pattern, $replacement, $content);
} else {
    // Insert after the title header (typically first line)
    $lines = explode("\n", $content);
    $new_content = $lines[0] . "\n" . $marker_start . "\n\nSupporting this project: " . $contributors_text . "\n\n";
    for ($i = 1; $i < count($lines); $i++) {
        $new_content .= $lines[$i] . "\n";
    }
    $content = $new_content;
}

file_put_contents($readme_path, $content);
echo "Readme updated successfully.\n";