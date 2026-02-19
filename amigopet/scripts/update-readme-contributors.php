<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('wp_remote_get')) {
    echo "This script must be run in WordPress context (e.g. load wp-load.php first).\n";
    exit(1);
}

$repo = 'AmigoPetWp/amigopet-wp';
$url = 'https://api.github.com/repos/' . $repo . '/contributors';
$token = getenv('GITHUB_TOKEN');

$args = [
    'headers' => [
        'User-Agent' => 'WordPress Plugin',
    ],
];
if ($token) {
    $args['headers']['Authorization'] = 'token ' . $token;
}

$response = wp_remote_get($url, $args);
if (is_wp_error($response)) {
    echo 'Error: ' . esc_html($response->get_error_message()) . "\n";
    exit(1);
}

$body = wp_remote_retrieve_body($response);
$contributors = json_decode($body, true);

if (!is_array($contributors)) {
    echo 'Error fetching contributors.' . "\n";
    exit(1);
}

$names = [];
foreach ($contributors as $contributor) {
    if (isset($contributor['type']) && $contributor['type'] === 'User' && isset($contributor['login'])) {
        $names[] = sanitize_text_field($contributor['login']);
    }
}

$contributors_text = implode(', ', $names);
echo 'Contributors: ' . esc_html($contributors_text) . "\n";
echo 'Add the WordPress.org usernames to the Contributors field in readme.txt (e.g. jacksonsa).' . "\n";
echo 'This script does not write to the plugin directory. Update readme.txt manually if needed.' . "\n";
