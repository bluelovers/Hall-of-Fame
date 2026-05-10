<?php
/**
 * PHP built-in server router
 *
 * Overrides SERVER_NAME to match the actual Host header,
 * and serves static files / index.php URLs correctly.
 */

if (isset($_SERVER['HTTP_HOST']))
{
	$host = parse_url('http://' . $_SERVER['HTTP_HOST'], PHP_URL_HOST);
	if ($host)
	{
		$_SERVER['SERVER_NAME'] = $host;
	}
}

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Map to real filesystem (document root is __DIR__)
$mimeTypes = array(
	'css' => 'text/css',
	'js' => 'application/javascript',
	'png' => 'image/png',
	'jpg' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'gif' => 'image/gif',
	'ico' => 'image/x-icon',
	'svg' => 'image/svg+xml',
	'woff' => 'font/woff',
	'woff2' => 'font/woff2',
	'ttf' => 'font/ttf',
	'eot' => 'application/vnd.ms-fontobject',
	'webp' => 'image/webp',
);

/**
 * Try to serve a file as static if it exists and is not PHP
 */
function _tryServeStatic($realFile, $mimeTypes)
{
	if (is_file($realFile))
	{
		$ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
		if ($ext !== 'php')
		{
			if (isset($mimeTypes[$ext]))
			{
				header('Content-Type: ' . $mimeTypes[$ext]);
			}
			readfile($realFile);

			return true;
		}
	}

	return false;
}

// 1. Direct file: serve from document root
if (_tryServeStatic(__DIR__ . $path, $mimeTypes))
{
	return true;
}

// 2. Path contains /static/: extract trailing /static/... portion
// Also handles recursive corrupted URLs like /foo/static/image/static/image/file.png
$staticPos = strpos($path, '/static/');
if ($staticPos !== false)
{
	$staticPath = substr($path, $staticPos);
	// Try the path as-is first
	if (_tryServeStatic(__DIR__ . $staticPath, $mimeTypes))
	{
		return true;
	}
	// If that fails, strip repeated /static/xxx/static/ prefixes recursively
	// e.g. /static/image/static/image/static/image/file.png → /static/image/file.png
	$prevPath = '';
	while ($staticPath !== $prevPath)
	{
		$prevPath = $staticPath;
		$staticPath = preg_replace('#^/static/[^/]+/static/#', '/static/', $staticPath);
		if (_tryServeStatic(__DIR__ . $staticPath, $mimeTypes))
		{
			return true;
		}
	}
}

// 3. /index.php/... prefix: strip it and serve the rest as static
if (strpos($path, '/index.php/') === 0)
{
	$subPath = substr($path, strlen('/index.php'));
	if (_tryServeStatic(__DIR__ . $subPath, $mimeTypes))
	{
		return true;
	}
}

// Override PHP_SELF so that setting.dist.php always computes
// BASE_URL_ROOT = /  (not /index.php/ or /manual/ etc.)
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Fall through: let PHP built-in server route the request
return false;
