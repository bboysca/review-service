<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use model\slack as Slack;

require 'vendor/autoload.php';
require_once 'boot.php';
require_once 'ReviewHandler.php';

class IosHandler extends ReviewHandler {
	
	public function getReviewsResponse($client) {
		$appStoreReviewsUrl = getenv('ITUNES_REVIEWS_URL')
		. "/id=" . getenv('IOS_APP_ID')
		. "/sortBy=mostRecent"
				. "/json";
		$request = new Request('GET', $appStoreReviewsUrl);
		$response = $client->send($request);
		return json_decode($response->getBody(), true);
	}
	
	public function getReviews($body) {
		return $body['feed']['entry'];
	}
	
	public function buildReviewPost($review) {
		$authorName = $review['author']['name']['label'];
		$rating = $review['im:rating']['label'];
		$title = $review['title']['label'];
		$content = $review['content']['label'];
		$appVersion = $review['im:version']['label'];
		$text = $title . "\n" . $content;
	
		$fields = array();
		$attachmentField = new Slack\AttachmentField("App version", $appVersion, true);
		$fields[] = $attachmentField;
	
		return new Slack\Attachment($rating, $authorName, $text, $fields, round(microtime(true)));
	}
	
	public function getReviewId($review) {
		return $review['id']['label'];
	}
	
	public function getAppId() {
		return getenv(IOS_APP_ID);
	}
	
	public function getAppIcon() {
		return getenv('IOS_STORE_ICON');
	}
	
	public function getMobileId() {
		return 1;
	}
	
	//¯\_(ツ)_/¯ for some reason the 0th entry in the iOS response is metadata about the app, so have to start at 1
	public function getReviewInitialIndex() {
		return 1;
	}
	
	public function getPostMessage() {
		return "New App Store Review!";
	}
}
