<?php
$remote = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
if (!in_array($remote, array('127.0.0.1', '::1'), true)) {
    http_response_code(403);
    exit;
}
require dirname(__DIR__, 3) . '/wp-load.php';
wp_set_current_user(6);
$_GET['trophy'] = '5k';
$preview = do_shortcode('[rts_single_trophy]');
?><!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="<?php echo esc_url(RTS_PLUGIN_URL . 'assets/css/single-trophy.css?v=' . filemtime(RTS_PLUGIN_PATH . 'assets/css/single-trophy.css')); ?>">
<style>html,body{margin:0;background:#020911}</style></head><body>
<?php echo $preview; ?>
<script type="module" src="<?php echo esc_url(RTS_PLUGIN_URL . 'assets/js/single-trophy.js?v=' . filemtime(RTS_PLUGIN_PATH . 'assets/js/single-trophy.js')); ?>"></script>
</body></html>
