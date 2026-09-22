<?php
/**
 * One-shot UpdraftPlus Backup Now (files+DB). CLI only.
 * Writes status JSON to wp-content/uploads/ong-updraft-once-20260922j.json
 */
if (PHP_SAPI !== 'cli') { fwrite(STDERR, "cli only\n"); exit(1); }
@set_time_limit(0);
@ini_set('memory_limit', '768M');

$root = '/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$uploads = $root . '/wp-content/uploads';
$lock = $uploads . '/ong-updraft-once.lock';
$done = $uploads . '/ong-updraft-once-20260922j.json';
@unlink($done);
@unlink($lock);

$logf = $uploads . '/ong-updraft-cli-log.txt';

$log = function ($m) use ($logf) {
	$line = gmdate('c') . ' ' . $m . "\n";
	@file_put_contents($logf, $line, FILE_APPEND);
	echo $line;
};

if (file_exists($done)) {
	$prev = @json_decode(@file_get_contents($done), true);
	if (is_array($prev) && !empty($prev['ok'])) {
		$log('already complete, skip');
		exit(0);
	}
}
if (file_exists($lock)) {
	$age = time() - (int)@filemtime($lock);
	if ($age < 3600) {
		$log('lock present age=' . $age . 's, skip');
		exit(0);
	}
	@unlink($lock);
}
@file_put_contents($lock, (string)getmypid());

define('DOING_CRON', true);
require $root . '/wp-load.php';

$tz = new DateTimeZone('America/New_York');
$out = array(
	'ts_start_utc' => gmdate('c'),
	'ts_start_et'  => (new DateTime('now', $tz))->format('Y-m-d H:i:s T'),
	'ok' => false,
	'nonce' => null,
	'backup_time_et' => null,
	'files' => array(),
	'errors' => array(),
	'drive' => null,
	'purpose' => 'pre-deploy-beta-fixes-score5-20260922j',
);

$log('boot_backup start beta-fixes-score5');
global $updraftplus;
if (!is_object($updraftplus) || !method_exists($updraftplus, 'boot_backup')) {
	$out['errors'][] = 'no updraftplus->boot_backup';
	file_put_contents($done, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	@unlink($lock);
	$log('ERROR no boot_backup');
	exit(2);
}

try {
	$updraftplus->boot_backup(true, true);
	$log('boot_backup returned');
} catch (Throwable $e) {
	$out['errors'][] = $e->getMessage();
	$log('EXCEPTION ' . $e->getMessage());
}

$nonce = null;
$backup_ts = null;
if (is_object($updraftplus) && method_exists($updraftplus, 'jobdata_get')) {
	$n = $updraftplus->jobdata_get('nonce');
	if ($n) $nonce = $n;
}
$hist = get_option('updraft_backup_history', array());
if (is_array($hist) && $hist) {
	krsort($hist, SORT_NUMERIC);
	foreach ($hist as $ts => $entry) {
		$backup_ts = (int)$ts;
		if (is_array($entry) && !empty($entry['nonce'])) $nonce = $entry['nonce'];
		break;
	}
}
$udir = WP_CONTENT_DIR . '/updraft';
$newest = array();
if (is_dir($udir)) {
	foreach (glob($udir . '/backup_*.zip') ?: array() as $f) {
		$newest[] = array('name' => basename($f), 'mtime' => filemtime($f), 'size' => filesize($f));
	}
	foreach (glob($udir . '/backup_*.gz') ?: array() as $f) {
		$newest[] = array('name' => basename($f), 'mtime' => filemtime($f), 'size' => filesize($f));
	}
	usort($newest, function ($a, $b) { return $b['mtime'] <=> $a['mtime']; });
	$out['files'] = array_slice($newest, 0, 12);
	if (!$nonce && $newest) {
		if (preg_match('/backup_\d{4}-\d{2}-\d{2}-\d{4}_[^_]+_([a-f0-9]{12})-/', $newest[0]['name'], $m)) {
			$nonce = $m[1];
		}
	}
	if (!$backup_ts && $newest) $backup_ts = $newest[0]['mtime'];
}

$svc = get_option('updraft_service');
$out['drive'] = is_array($svc) ? $svc : $svc;
$out['nonce'] = $nonce;
if ($backup_ts) {
	$d = new DateTime('@' . $backup_ts);
	$d->setTimezone($tz);
	$out['backup_time_et'] = $d->format('Y-m-d H:i:s T');
	$out['backup_unix'] = $backup_ts;
}
$out['ts_end_utc'] = gmdate('c');
$out['ts_end_et'] = (new DateTime('now', $tz))->format('Y-m-d H:i:s T');
$out['ok'] = (bool)$nonce && empty($out['errors']);
$last = get_option('updraft_last_backup');
if (is_array($last)) {
	unset($last['auth'], $last['token'], $last['secret']);
	$out['last_backup_option'] = $last;
}

file_put_contents($done, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
@unlink($lock);
$log('done ok=' . ($out['ok'] ? '1' : '0') . ' nonce=' . ($nonce ?: 'null'));
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
exit($out['ok'] ? 0 : 3);
