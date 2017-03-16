<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use util\DbUtil;
use model\slack as Slack;

require 'vendor/autoload.php';
require_once 'boot.php';
require_once 'AndroidHandler.php';
require_once 'IosHandler.php';

if ($_SERVER ['REQUEST_METHOD'] === 'POST') {
	$handler;
	switch ($_POST['type']) {
		case 'ios':
			$handler = new IosHandler();
			break;
		case 'android':
			$handler = new AndroidHandler();
			break;
		default:
			http_response_code(500);
			echo 'types supported: ios, android';
			return;
	}
	//get list of recent reviews
	$client = new Client();
	$body = $handler->getReviewsResponse($client);

	//find the latest previously stored reviewID, we don't want to notify about duplicates!
	$connection = DbUtil::getConnection();
	$sql = 'SELECT reviewID FROM LatestReview WHERE companyID = :companyID AND appID = :appID AND mobileID = :mobileID';
	$statement = $connection->prepare($sql);
	$statement->execute([':companyID' => getenv(COMPANY_ID),
			':appID' => $handler->getAppId(),
			':mobileID' => $handler->getMobileId(),
	]);
	$row = $statement->fetch();
	$postLimit = $handler->getPostLimit();
	$latestReviewID = $row['reviewID'];
	//initial setup scenario where no review has been processed yet
	if (empty($latestReviewID)) {
		$postLimit = $handler->getReviewInitialIndex();
	}
	
	//iterate through reviews response to see if any reviews found, if so massage it to post to Slack
	$reviews = $handler->getReviews($body);
	$reviewsToPost = array();
	foreach($reviews as $key=>$review) {
		$reviewId = $handler->getReviewId($review);
		if ($key == $handler->getReviewInitialIndex() && (empty($latestReviewID) || $reviewId != $latestReviewID)) {
			updateLatestReviewDB($connection, $reviewId, $handler);
		}
		if ($key >= $handler->getReviewInitialIndex() && $key <= $postLimit) {
			//we keep adding reviews to the list to post until we find the review that matches the previously stored one
			if ($reviewId == $latestReviewID) {
				break;
			}
			$slackAttach  = $handler->buildReviewPost($review);
			$reviewsToPost[] = $slackAttach;
		}
	}

	if (!empty($reviewsToPost)) {
		$slackResponse = postToSlack($client, $handler, $reviewsToPost);
		$slackBody = json_decode($slackResponse->getBody(), true);
	}

} else {
	http_response_code(404);
}

function updateLatestReviewDB($connection, $reviewID, $reviewHandler) {
	$updateSql = 'UPDATE LatestReview SET reviewID = :newID WHERE companyID = :companyID AND appID = :appID AND mobileID = :mobileID';
	$udpateStatement = $connection->prepare($updateSql);
	$udpateStatement->execute([':newID' => $reviewID,
			':companyID' => getenv(COMPANY_ID),
			':appID' => $reviewHandler->getAppId(),
			':mobileID' => $reviewHandler->getMobileId(),
	]);
}

function postToSlack($client, $handler, $reviews) {
	$slackMessageBody = new Slack\Post($handler->getPostMessage(), getenv('BOT_NAME'), $handler->getAppIcon(), $reviews);
	$slackMessageRequest = new Request('POST', getenv('SLACK_CHANNEL_URL'), [], json_encode($slackMessageBody));
	return $client->send($slackMessageRequest);
}