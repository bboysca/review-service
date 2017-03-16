<?php

abstract class ReviewHandler {
	abstract public function getReviewsResponse($client);
	abstract public function getReviews($body);
	abstract public function buildReviewPost($review);
	abstract public function getReviewId($review);
	abstract public function getAppId();
	abstract public function getAppIcon();
	abstract public function getMobileId();
	abstract public function getReviewInitialIndex();
	abstract public function getPostMessage();

	public function getPostLimit() {
		return 10;
	}
}
