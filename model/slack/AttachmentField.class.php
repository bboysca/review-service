<?php

namespace model\slack;
use JsonSerializable;

class AttachmentField implements JsonSerializable {
	private $title;
	private $value;
	private $isShort;

	public function __construct($title, $value, $isShort) {
		$this->title = $title;
		$this->value = $value;
		$this->isShort = $isShort;
	}
	
	public function jsonSerialize() {
		return [
			'title' => $this->title,
			'value' => $this->value,
			'short' => $this->isShort,
		];
	}
}
