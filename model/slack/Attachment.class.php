<?php

namespace model\slack;
use JsonSerializable;

class Attachment implements JsonSerializable {
	private $rating;
	private $authorName;
	private $text;
	private $fields;
	private $timestamp;

	public function __construct($rating, $authorName, $text, $fields, $timestamp) {
		$this->rating = $rating;
		$this->authorName = $authorName;
		$this->text = $text;
		$this->fields = $fields;
		$this->timestamp = $timestamp;
	}
	
	public function jsonSerialize() {
		return [
			'color' => $this->getColor(),
			'author_name' => $this->authorName,
			'title' => $this->getTitle(),
			'text' => $this->text,
			'fields' => $this->fields,
			'mrkdwn_in' => ["text"],
			'ts' => $this->timestamp
		];
	}
	
	private function getColor() {
		switch($this->rating) {
			case 1:
			case 2:
				return "danger";
			case 3:
				return "warning";
			default:
				return "good";
		}
	}
	
	private function getTitle() {
		$out = "Rating: ";
		for ($i = 0; $i < $this->rating; $i++) {
			$out .= ":star:";
		}
		return $out;
	}
}
