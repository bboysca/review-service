<?php

use util\DbUtil;

require 'vendor/autoload.php';
require_once 'boot.php';

$connection = DbUtil::getConnection();
setupMobileTable($connection);
setupLatestReviewTable($connection);

function setupMobileTable($connection) {
	// Mobile(*mobileId, name)
	$mobileTableStatement = $connection->prepare('CREATE TABLE IF NOT EXISTS Mobile ('
			. 'mobileID INT NOT NULL AUTO_INCREMENT, '
			. 'name VARCHAR(255) NOT NULL, '
			. 'PRIMARY KEY(mobileId))');
	executeCreateTable($mobileTableStatement);
	$insertStatement = $connection->prepare('INSERT INTO Mobile (mobileID, name) VALUES (:id0, :name0), (:id1, :name1)');
	$insertStatement->execute([
			':id0' => 1,
			':name0' => "IPHONE",
			':id1' => 2,
			':name1' => "ANDROID",
	]);
}

function setupLatestReviewTable($connection) {
	// LatestReview(*companyId, *appId, mobileId, reviewId)
	$latestReviewStatement = $connection->prepare('CREATE TABLE IF NOT EXISTS LatestReview ('
			. 'companyID INT NOT NULL, '
			. 'appID VARCHAR(255) NOT NULL, '
			. 'mobileID INT NOT NULL, '
			. 'reviewID VARCHAR(255), '
			. 'FOREIGN KEY(mobileID) REFERENCES Mobile(mobileID), '
			. 'PRIMARY KEY(companyID, appID))');
	executeCreateTable($latestReviewStatement);
	
	$sql = 'INSERT INTO LatestReview (companyID, appID, mobileId, reviewID) VALUES (:companyID, :appID, :mobileID, :reviewID)';
	$insertStatement = $connection->prepare($sql);
	$insertStatement->execute([
			':companyID' => getenv('COMPANY_ID'),
			':appID' => getenv('IOS_APP_ID'),
			':mobileID' => 1,
			':reviewID' => "",
	]);
	$insertStatement = $connection->prepare($sql);
	$insertStatement->execute([
			':companyID' => getenv('COMPANY_ID'),
			':appID' => getenv('PLAY_APP_ID'),
			':mobileID' => 2,
			':reviewID' => "",
	]);
}

function executeCreateTable($statement) {
	$result = $statement->execute();
	if (false === $result) {
		//TODO run rollback script to drop tables created so far
		throw new Exception('Unable to create table: ' . $statement->errorInfo()[2]);
	}
}
