<?php
/**
 * Offline regression checks for the Template Images certificate flow.
 * Run: php tests/certificate-template-regression.php
 * Also: php -d disable_functions=imagepng tests/certificate-template-regression.php
 * Uses WordPress's real HTML parser/escaping with in-memory records only.
 * Does not bootstrap WordPress, connect to a database, or send mail.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ABSPATH', str_replace('\\', '/', dirname(__DIR__, 4)) . '/');
define('WPINC', 'wp-includes');
define('RTS_PLUGIN_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/');
define('RTS_PLUGIN_URL', 'https://example.test/wp-content/plugins/run-the-seas-survey/');
define('WP_DEBUG', false);
require ABSPATH . WPINC . '/compat-utf8.php';
require ABSPATH . WPINC . '/compat.php';
require ABSPATH . WPINC . '/utf8.php';
require ABSPATH . WPINC . '/plugin.php';
require ABSPATH . WPINC . '/formatting.php';
require ABSPATH . WPINC . '/kses.php';
require ABSPATH . WPINC . '/http.php';
require ABSPATH . WPINC . '/class-wp-token-map.php';
foreach (array('html5-named-character-references', 'class-wp-html-attribute-token', 'class-wp-html-span',
    'class-wp-html-doctype-info', 'class-wp-html-text-replacement', 'class-wp-html-decoder', 'class-wp-html-tag-processor') as $file) {
    require ABSPATH . WPINC . '/html-api/' . $file . '.php';
}

$rts_test_dir = str_replace('\\', '/', sys_get_temp_dir()) . '/rts-certificate-test-' . bin2hex(random_bytes(6));
$rts_test_options = array('blog_charset' => 'UTF-8');
function get_option($key, $default = false) { return $GLOBALS['rts_test_options'][$key] ?? $default; }
function is_utf8_charset($charset = null) { return true; }
function wp_allowed_protocols() { return array('http', 'https', 'mailto'); }
function __($text, $domain = '') { return $text; }
function get_bloginfo($key) { return 'Run The Seas Test'; }
function home_url($path = '/') { return 'https://example.test' . $path; }
function absint($value) { return abs((int) $value); }
function wp_json_encode($value) { return json_encode($value); }
function wp_normalize_path($path) { return str_replace('\\', '/', $path); }
function wp_date($format, $timestamp = null) { return gmdate($format, $timestamp ?? strtotime('2026-09-07')); }
function wp_mkdir_p($path) { return is_dir($path) || mkdir($path, 0777, true); }
function wp_upload_dir() { return array('basedir' => $GLOBALS['rts_test_dir'], 'baseurl' => 'https://example.test/uploads', 'error' => $GLOBALS['rts_test_upload_error'] ?? ''); }
function attachment_url_to_postid($url) { return 0; }
function _doing_it_wrong($function, $message, $version) { throw new RuntimeException($function . ': ' . $message); }
function rts_init() { return (object) array('registration' => $GLOBALS['rts_test_registration']); }
class RTS_DB { public static function table($name) { return 'wp_rts_' . $name; } }
class RTS_Certificate_Test_DB {
    public $prefix = 'wp_';
    public $template;
    public $participant;
    public function prepare($sql, ...$args) { return $sql; }
    public function esc_like($text) { return $text; }
    public function get_var($sql) { return strpos($sql, 'SHOW TABLES') !== false ? 'wp_rts_email_templates' : 'action_key'; }
    public function get_row($sql) { return clone (strpos($sql, 'rts_participants') !== false ? $this->participant : $this->template); }
    public function __call($name, $args) { throw new RuntimeException('Unexpected database call: ' . $name); }
}
function rts_test($condition, $label) {
    if (!$condition) { throw new RuntimeException('FAIL: ' . $label); }
    echo 'PASS: ' . $label . PHP_EOL;
}
function rts_test_private($class, $method, ...$args) {
    $reflection = new ReflectionMethod($class, $method);
    $reflection->setAccessible(true);
    return $reflection->invokeArgs(is_object($class) ? $class : null, $args);
}
function rts_test_certificate_tag($html) {
    $tags = new WP_HTML_Tag_Processor($html);
    while ($tags->next_tag('IMG')) {
        if ($tags->get_attribute('alt') === 'Preview of your Founding Runner Cruise Credit') {
            return $tags;
        }
    }
    return null;
}

require RTS_PLUGIN_PATH . 'includes/rts-email-template-integration.php';
require RTS_PLUGIN_PATH . 'includes/class-rts-registration.php';
require dirname(RTS_PLUGIN_PATH) . '/rts-admin-platform/includes/class-rts-admin-menu-2.php';
$rts_test_registration = (new ReflectionClass('RTS_Registration'))->newInstanceWithoutConstructor();
$wpdb = new RTS_Certificate_Test_DB();
$wpdb->participant = (object) array('id' => 123, 'first_name' => 'Avery', 'last_name' => 'Morgan',
    'certificate_number' => 'RTS-CERT-2026-TEST01', 'email_verified' => 0);

wp_mkdir_p($rts_test_dir . '/2026/09');
$backplate = 'https://example.test/uploads/2026/09/RunTheSeas_Certificate_Backplate_v3.jpg';
copy(RTS_PLUGIN_PATH . 'assets/certificate-template.jpg', $rts_test_dir . '/2026/09/RunTheSeas_Certificate_Backplate_v3.jpg');
$rts_test_options['rts_verification_email_design_assets'] = array('certificate_preview_image' => RTS_PLUGIN_URL . 'assets/certificate-template.jpg');
$fixture = '<table><tr><td><img src="https://example.test/header.png" alt="Run The Seas"></td></tr><tr><td>'
    . '<img class="wp-image-999" src="' . $backplate . '" srcset="' . $backplate . ' 1076w" sizes="100vw" width="490" height="366">'
    . '</td></tr><tr><td><img src="https://example.test/download-certificate-button.png" alt="Download certificate"></td></tr></table>';
$wpdb->template = (object) array('id' => 2, 'action_key' => 'email_verification', 'subject' => 'Confirm your email', 'html_body' => $fixture);

$normalized = rts_normalize_email_certificate_template($fixture, 'email_verification');
$tag = rts_test_certificate_tag($normalized['html_body']);
rts_test($tag && $tag->get_attribute('src') === '{certificate_preview_url}', 'Uploaded filename without alt becomes dynamic');
rts_test($tag->get_attribute('srcset') === null && $tag->get_attribute('sizes') === null, 'Raw responsive image alternatives removed');
rts_test($normalized['backplate_url'] === $backplate, 'Template artwork preserved independently of shared settings');
rts_test(strpos($normalized['html_body'], 'src="https://example.test/header.png"') !== false
    && strpos($normalized['html_body'], 'src="https://example.test/download-certificate-button.png"') !== false, 'Header and certificate button remain untouched');
rts_test(rts_normalize_email_certificate_template($fixture, 'marketing')['html_body'] === $fixture, 'Other email actions unchanged');
rts_test(rts_normalize_email_certificate_template($normalized['html_body'], 'email_verification')['html_body'] === $normalized['html_body'], 'Normalization is idempotent');
rts_test(rts_is_email_certificate_image($backplate . '?v=3&cache=1', '', 'email_verification'), 'Cache-busting URL recognized');
rts_test(rts_is_email_certificate_image(str_replace('.jpg', '-490x366.jpg', $backplate), '', 'email_verification'), 'WordPress thumbnail recognized');
$linked = rts_normalize_email_certificate_template('<a href="' . $backplate . '"><img src="' . $backplate . '"></a>', 'founding_runner_certificate');
rts_test(strpos($linked['html_body'], 'href="{certificate_preview_url}"') !== false, 'Certificate link follows the personalized image');
$arbitrary = rts_normalize_email_certificate_template('<img src="https://example.test/custom.jpg" alt="Preview of your Founding Runner Cruise Credit">', 'email_verification');
rts_test($arbitrary['backplate_url'] === 'https://example.test/custom.jpg', 'Existing certificate slot retains arbitrary replacement artwork');
$stale = rts_normalize_email_certificate_template('<img src="https://example.test/uploads/rts-certificate-previews/verification-preview-999-abcdef.png">', 'email_verification');
rts_test($stale['backplate_url'] === '' && strpos($stale['html_body'], 'src="{certificate_preview_url}"') !== false, 'Old recipient preview cannot become a reusable backplate');

$editor_template = clone $wpdb->template;
$editor_template->html_body = $normalized['html_body'];
$editor_body = rts_test_private('RTS_Admin_Menu_2', 'template_editor_preview_body', $editor_template, $editor_template->html_body);
$saved = rts_test_private('RTS_Admin_Menu_2', 'restore_template_image_merge_fields', 2, wp_kses_post($editor_body));
$saved = rts_test_private('RTS_Admin_Menu_2', 'apply_template_image_replacements', 2, $saved, array(0 => 'https://example.test/header.png', 1 => $backplate));
$tag = rts_test_certificate_tag($saved);
rts_test($tag->get_attribute('src') === '{certificate_preview_url}', 'Save with unchanged Media Library URL keeps dynamic src');
rts_test($tag->get_attribute('data-rts-certificate-backplate') === $backplate, 'Visual editor and sanitizer preserve selected backplate');
$saved_replacement = rts_test_private('RTS_Admin_Menu_2', 'apply_template_image_replacements', 2, $saved, array(1 => 'https://example.test/uploads/new-artwork.jpg'));
rts_test(rts_test_certificate_tag($saved_replacement)->get_attribute('data-rts-certificate-backplate') === 'https://example.test/uploads/new-artwork.jpg', 'Media Library replacement changes only template backplate');
rts_test(get_option('rts_verification_email_design_assets')['certificate_preview_image'] === RTS_PLUGIN_URL . 'assets/certificate-template.jpg', 'Editor replacement does not change shared certificate settings');

$context = array('first_name' => 'Avery', 'last_name' => 'Morgan', 'certificate_number' => 'RTS-CERT-2026-TEST01',
    'founding_runner_number' => '#0000123', 'certificate_status' => 'PENDING', 'certificate_issued_date' => 'Pending verification',
    'certificate_preview_url' => 'https://example.test/uploads/initial-preview.png', '_certificate_participant' => $wpdb->participant);
foreach (array('legacy' => $fixture, 'saved' => $saved) as $label => $body) {
    $wpdb->template->html_body = $body;
    $resolved = rts_resolve_transactional_email_template('email_verification', 'Default', '', $context);
    $tag = rts_test_certificate_tag($resolved['html_body']);
    if (function_exists('imagepng')) {
        rts_test($tag && strpos($tag->get_attribute('src'), '/rts-certificate-previews/verification-preview-123-') !== false, $label . ': sent image is generated for the actual recipient');
        $rendered_path = str_replace('https://example.test/uploads', $rts_test_dir, $tag->get_attribute('src'));
        $rendered = imagecreatefrompng($rendered_path);
        $blank = imagecreatefromjpeg(RTS_PLUGIN_PATH . 'assets/certificate-template.jpg');
        foreach (array('name' => array(250, 315, 800, 350), 'details' => array(65, 555, 445, 655)) as $field => $area) {
            $changed = 0;
            for ($y = $area[1]; $y < $area[3]; $y++) {
                for ($x = $area[0]; $x < $area[2]; $x++) {
                    if (imagecolorat($blank, $x, $y) !== imagecolorat($rendered, $x, $y)) { $changed++; }
                }
            }
            rts_test($changed > 50, $label . ': ' . $field . ' painted onto certificate pixels');
        }
        imagedestroy($blank);
        imagedestroy($rendered);
    } else {
        rts_test($tag === null && strpos($resolved['html_body'], '<v:rect') !== false, $label . ': no-GD send replaces blank image with fallback');
        foreach (array('Avery Morgan', '#0000123', 'RTS-CERT-2026-TEST01', 'PENDING', 'Pending verification') as $value) {
            rts_test(strpos($resolved['html_body'], $value) !== false, $label . ': fallback contains ' . $value);
        }
    }
    file_put_contents($rts_test_dir . '/' . $label . '-email.html', $resolved['html_body']);
}
$rts_test_upload_error = 'Simulated read-only uploads directory';
$wpdb->template->html_body = $fixture;
$resolved = rts_resolve_transactional_email_template('email_verification', 'Default', '', $context);
rts_test(strpos($resolved['html_body'], '<v:rect') !== false && strpos($resolved['html_body'], 'Avery Morgan') !== false,
    'Unwritable uploads still produce participant details');
rts_test(strpos($resolved['html_body'], 'background="' . $backplate . '"') !== false,
    'Unwritable uploads keep the template-specific artwork');
echo 'Artifacts: ' . $rts_test_dir . PHP_EOL;
