<?php
/**
 * Plugin-level elFinder connector override for stream-safe downloads.
 *
 * Keeps vendor library untouched while providing host-compatible output
 * fallback when fpassthru is unavailable/blocked.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('class_fma_elfinder_connector')) {
	return;
}

class class_fma_elfinder_connector extends elFinderConnector
{
	/**
	 * WordPress slashes $_POST; elFinder's parent filter only stripslashes when
	 * magic_quotes_gpc is on (removed in PHP 5.4+). Apply wp_unslash on scalar
	 * values only — parent recurses arrays via $this->input_filter(), so unslashing
	 * the full array again would double-strip put content (\" becomes ").
	 *
	 * @param mixed $args Request argument or array of arguments.
	 * @return mixed
	 */
	protected function input_filter($args)
	{
		if (is_array($args)) {
			return parent::input_filter($args);
		}

		$args = parent::input_filter($args);

		return function_exists('wp_unslash') ? wp_unslash($args) : $args;
	}

	/**
	 * Host-safe passthrough fallback.
	 *
	 * @param resource $fp File pointer.
	 * @return void
	 */
	protected function fma_stream_passthru($fp)
	{
		if (function_exists('fpassthru')) {
			fpassthru($fp);
			return;
		}

		while (!feof($fp)) {
			$chunk = fread($fp, 8192);
			if ($chunk === false) {
				break;
			}
			echo $chunk;
		}
	}

	/**
	 * Override only to customize stream output fallback.
	 *
	 * @param array $data elFinder response.
	 * @return void
	 * @throws elFinderAbortException
	 */
	protected function output(array $data)
	{
		$this->elFinder->getSession()->close();
		ignore_user_abort(false);

		if ($this->header) {
			self::sendHeader($this->header);
		}

		if (isset($data['pointer'])) {
			elFinder::extendTimeLimit(0);

			if (!empty($data['header'])) {
				self::sendHeader($data['header']);
			}

			while (ob_get_level() && ob_end_clean()) {
			}

			$toEnd = true;
			$fp = $data['pointer'];
			$sendData = !($this->reqMethod === 'HEAD' || !empty($data['info']['xsendfile']));
			$psize = null;
			if (($this->reqMethod === 'GET' || !$sendData)
				&& (elFinder::isSeekableStream($fp) || elFinder::isSeekableUrl($fp))
				&& (array_search('Accept-Ranges: none', headers_list()) === false)) {
				header('Accept-Ranges: bytes');
				if (!empty($_SERVER['HTTP_RANGE'])) {
					$size = $data['info']['size'];
					$end = $size - 1;
					if (preg_match('/bytes=(\d*)-(\d*)(,?)/i', $_SERVER['HTTP_RANGE'], $matches)) {
						if (empty($matches[3])) {
							if (empty($matches[1]) && $matches[1] !== '0') {
								$start = $size - $matches[2];
							} else {
								$start = intval($matches[1]);
								if (!empty($matches[2])) {
									$end = intval($matches[2]);
									if ($end >= $size) {
										$end = $size - 1;
									}
									$toEnd = ($end == ($size - 1));
								}
							}
							$psize = $end - $start + 1;

							header('HTTP/1.1 206 Partial Content');
							header('Content-Length: ' . $psize);
							header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);

							if (isset($data['info']['xsendfile']) && strtolower($data['info']['xsendfile']) === 'x-sendfile') {
								if (function_exists('header_remove')) {
									header_remove($data['info']['xsendfile']);
								} else {
									header($data['info']['xsendfile'] . ':');
								}
								unset($data['info']['xsendfile']);
								if ($this->reqMethod !== 'HEAD') {
									$sendData = true;
								}
							}

							$sendData && !elFinder::isSeekableUrl($fp) && fseek($fp, $start);
						}
					}
				}
				if ($sendData && is_null($psize)) {
					elFinder::rewind($fp);
				}
			} else {
				header('Accept-Ranges: none');
				if (isset($data['info']) && !$data['info']['size']) {
					if (function_exists('header_remove')) {
						header_remove('Content-Length');
					} else {
						header('Content-Length:');
					}
				}
			}

			if ($sendData) {
				if ($toEnd || elFinder::isSeekableUrl($fp)) {
					if (version_compare(PHP_VERSION, '5.6', '<')) {
						file_put_contents('php://output', $fp);
					} else {
						$this->fma_stream_passthru($fp);
					}
				} else {
					$out = fopen('php://output', 'wb');
					stream_copy_to_stream($fp, $out, $psize);
					fclose($out);
				}
			}

			if (!empty($data['volume'])) {
				$data['volume']->close($fp, $data['info']['hash']);
			} else {
				fclose($fp);
			}
			exit();
		}

		self::outputJson($data);
		exit(0);
	}
}
