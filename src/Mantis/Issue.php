<?php

namespace M2G\Mantis;

use M2G\Mantis\Contracts\BaseAbstract;

class Issue extends BaseAbstract {

	public function __construct($rawIssue = null) {
		if (is_object($rawIssue)) {
			$this->raw = $rawIssue;
		} else {
			$this->raw = new stdClass();
		}
	}

	public function __call($method, $params = array()) {
		if (count($params)) {
			$this->raw->$method = $params[0];
		}

		return isset($this->raw->$method) ? $this->raw->$method : null;
	}

	public function customFields($asArray = false) {
		$customFields = array();
		foreach($this->raw->custom_fields as $raw) {
			$customField = new CustomField($raw);
			if ($asArray) {
				$customField = $customField->toArray();
				$customFields[$customField['name']] = $customField['value'];
			} else {
				$customFields[] = $customField;
			}
		}
		return $customFields;
	}

	public function notes($asArray = false) {
		$notes = array();

		if (!empty($this->raw->notes)) {
			foreach($this->raw->notes as $raw) {
				$customField = new Note($raw);
				if ($asArray) {
					$notes[] = $customField->toArray();
				} else {
					$notes[] = $customField;
				}
			}
		}

		return $notes;
	}

	public function dateSubmitted() {
		return new \DateTime($this->raw->date_submitted);
	}

	public function dueDate() {
		return $this->raw->due_date ? new \DateTime($this->raw->due_date) : null;
	}

	public function toArray() {
		return array(
			'id' => $this->id(),
			'summary' => $this->summary(),
			'description' => $this->description(),
			'steps_to_reproduce' => $this->steps_to_reproduce(),
			'additional_information' => $this->additional_information(),
			'view_state' => $this->view_state() ? $this->view_state()->name : 'public',
			'reporter' => $this->reporter() ? $this->reporter()->name : null,
			'handler' => $this->handler() ? $this->handler()->name : null,
			'severity' => $this->severity() ? $this->severity()->name : null,
			'category' => $this->category() ? $this->category() : null,
			'reproducibility' => $this->reproducibility() ? $this->reproducibility()->name : null,
			'projection' => $this->projection() ? $this->projection()->name : null,
			'eta' => $this->eta() ? $this->eta()->name : null,
			'resolution' => $this->resolution() ? $this->resolution()->name : null,
			'date_submitted' => $this->dateSubmitted() ? $this->dateSubmitted() : null,
			'due_date' => $this->dueDate() ? $this->dueDate() : null,
			'status' => $this->status() ? $this->status()->name : null,
			'notes' => $this->notes(true),
			'custom_fields' => $this->customFields(true),
			'tags' => $this->tags(),
		);
	}
}