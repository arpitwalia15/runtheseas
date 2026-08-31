<?php
// Prevent direct access to config files on Nginx (CVE-2025-11379 fix)
if (!defined('ABSPATH')) {
  http_response_code(403);
  die('Direct access forbidden');
}