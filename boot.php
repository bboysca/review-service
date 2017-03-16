<?php

require 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
	$ds = DIRECTORY_SEPARATOR;
	$className = str_replace('\\', $ds, $class_name);
	$file = __DIR__ . "{$ds}{$className}.class.php";
	if (is_readable($file)) {
		require_once $file;
	}
});
