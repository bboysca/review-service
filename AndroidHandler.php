<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use model\slack as Slack;

require 'vendor/autoload.php';
require_once 'boot.php';
require_once 'ReviewHandler.php';

class AndroidHandler extends ReviewHandler {
	
	public function getReviewsResponse($client) {
		//google requires an access token as retrieving reviews in an authenticated request
		$data = ['form_params' => ['grant_type' => 'refresh_token',
				'refresh_token' => getenv('PLAY_REFRESH_TOKEN'),
				'client_id' => getenv('PLAY_CLIENT_ID'),
				'client_secret' => getenv('PLAY_CLIENT_SECRET')]];
		$response = $client->request('POST', getenv('PLAY_TOKEN_URL'), $data);
		$tokenJson = json_decode($response->getBody(), true);
		$accessToken = $tokenJson['access_token'];
		
		$playStoreReviewsUrl = getenv('PLAY_REVIEWS_URL') . getenv('PLAY_APP_ID')
		. "/reviews?access_token=" . $accessToken;
		$response = $client->request('GET', $playStoreReviewsUrl);
		return json_decode($response->getBody(), true);
	}
	
	public function getReviews($body) {
		return $body['reviews'];
	}
	
	public function buildReviewPost($review) {
		$authorName = $review['authorName'];
		$userComment = $review['comments'][0]['userComment'];
		$rating = $userComment['starRating'];
		$text = $userComment['text'];
		$appVersion = $userComment['appVersionName'];
		$date = $userComment['lastModified']['seconds'];
	
		$fields = array();
		$attachmentField = new Slack\AttachmentField("App version", $appVersion, true);
		$fields[] = $attachmentField;
	
		return new Slack\Attachment($rating, $authorName, $text, $fields, $date);
	}
	
	public function getReviewId($review) {
		return $review['reviewId'];
	}
	
	public function getAppId() {
		return getenv(PLAY_APP_ID);
	}
	
	public function getAppIcon() {
		return getenv('PLAY_STORE_ICON');
	}
	
	public function getMobileId() {
		return 2;
	}
	
	public function getReviewInitialIndex() {
		return 0;
	}
	
	public function getPostMessage() {
		return "New Play Store Review!";
	}
}

