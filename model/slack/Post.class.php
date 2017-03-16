<?php

namespace model\slack;
use JsonSerializable;

class Post implements JsonSerializable {
	private $text;
	private $username;
	private $icon;
	private $attachments;

	public function __construct($text, $username, $icon, $attachments) {
		$this->text = $text;
		$this->username = $username;
		$this->icon = $icon;
		$this->attachments = $attachments;
	}
	
	public function jsonSerialize() {
		return [
			'text' => $this->text,
			'username' => $this->username,
			'icon_url' => $this->icon,
			'as_user' => false,
			'attachments' => $this->attachments,
		];
	}
}
