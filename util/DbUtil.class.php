<?php

namespace util;
use PDO;

class DbUtil {
	
	public static function getConnection() {
		$dsn = getenv('MYSQL_DSN');
		$user = getenv('MYSQL_USER');
		$password = getenv('MYSQL_PASSWORD');
		if (!isset($dsn, $user) || false === $password) {
			throw new Exception('Set MYSQL_DSN, MYSQL_USER, and MYSQL_PASSWORD environment variables');
		}
		return new PDO($dsn, $user, $password);
	}
}
