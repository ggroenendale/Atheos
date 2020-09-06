<?php

//////////////////////////////////////////////////////////////////////////////80
// Helper trait
//////////////////////////////////////////////////////////////////////////////80
// Copyright (c) 2020 Liam Siira (liam@siira.io), distributed as-is and without
// warranty under the MIT License. See [root]/license.md for more.
// This information must remain intact.
//////////////////////////////////////////////////////////////////////////////80
// Authors: Codiad Team, @Fluidbyte, Atheos Team, @hlsiira
//////////////////////////////////////////////////////////////////////////////80

trait Helpers {

	//////////////////////////////////////////////////////////////////////////80
	// Clean Username
	//////////////////////////////////////////////////////////////////////////80
	public static function cleanUsername($username) {
		return strtolower(preg_replace('#[^A-Za-z0-9\-\_\@\.]#', '', $username));
	}

	function compareVersions($v1, $v2) {
		// Src: https://helloacm.com/the-javascript-function-to-compare-version-number-strings/
		if (!is_string($v1) || !is_string($v2)) {
			return false;
		}

		$v1 = explode(".", $v1);
		$v2 = explode(".", $v2);

		$k = min(count($v1), count($v2));

		for ($i = 0; $i < $k; $i++) {
			$v1[$i] = (int)$v1[$i];
			$v2[$i] = (int)$v1[$i];
			if ($v1[$i] > $v2[$i]) {
				return 1;
			}
			if ($v1[$i] < $v2[$i]) {
				return -1;
			}
		}
		return count($v1) === count($v2) ? 0 : (count($v1) < count($v2) ? -1 : 1);
	}

	//////////////////////////////////////////////////////////////////////////80
	// Read Post/Get/Server/Session Data
	//////////////////////////////////////////////////////////////////////////80
	public static function data($key = false, $type = false, $val = null) {
		if (!$key || !$type) return $val;

		if (!empty($val) && $type === "SESSION") {
			$_SESSION[$key] = $val;
		}

		if ($type === "SESSION") {
			$val = array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
		} elseif ($type === "POST") {
			$val = array_key_exists($key, $_POST) ? $_POST[$key] : null;
		}

		return $val;
	}

	//////////////////////////////////////////////////////////////////////////80
	// Read Content of directory
	//////////////////////////////////////////////////////////////////////////80
	public static function readDirectory($foldername) {
		$tmp = array();
		$allFiles = scandir($foldername);
		foreach ($allFiles as $fname) {
			if ($fname === '.' || $fname === '..') {
				continue;
			}

			$length = strlen(".disabled");
			if (substr($fname, -$length) === ".disabled") {
				continue;
			}

			if (is_dir($foldername.'/'.$fname)) {
				$tmp[] = $fname;
			}
		}
		return $tmp;
	}

	//////////////////////////////////////////////////////////////////////////80
	// Log Action
	//////////////////////////////////////////////////////////////////////////80
	public static function log($text, $name = "global") {
		$path = DATA . "/log/";
		$path = preg_replace('#/+#', '/', $path);

		if (!is_dir($path)) mkdir($path);

		$file = "$name.log";
		$text = $text . PHP_EOL;

		if (file_exists($path . $file)) {
			$lines = file($path . $file);
			if (sizeof($lines) > 100) {
				unset($lines[0]);
			}
			$lines[] = $text;

			$write = fopen($path . $file, 'w');
			fwrite($write, implode('', $lines));
			fclose($write);
		} else {
			$write = fopen($path . $file, 'w');
			fwrite($write, $text);
			fclose($write);
		}
	}

	//////////////////////////////////////////////////////////////////////////80
	// Check If WIN based system
	//////////////////////////////////////////////////////////////////////////80
	public static function isWINOS() {
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	//////////////////////////////////////////////////////////////////////////80
	// Check Function Availability
	//////////////////////////////////////////////////////////////////////////80
	public static function isAvailable($func) {
		if (ini_get('safe_mode')) return false;
		$disabled = ini_get('disable_functions');
		if ($disabled) {
			$disabled = explode(',', $disabled);
			$disabled = array_map('trim', $disabled);
			return !in_array($func, $disabled);
		}
		return true;
	}

	function rDelete($target) {
		// Unnecessary, but rather be safe that sorry.
		if ($target === "." || $target === "..") {
			return;
		}
		if (is_dir($target)) {
			$files = glob($target . "{*,.[!.]*,..?*}", GLOB_BRACE|GLOB_MARK); //GLOB_MARK adds a slash to directories returned

			foreach ($files as $file) {
				Common::rDelete($file);
			}
			if (file_exists($target)) {
				rmdir($target);
			}
		} elseif (is_file($target)) {
			unlink($target);
		}
	}
}