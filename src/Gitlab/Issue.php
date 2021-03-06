<?php

namespace M2G\Gitlab;

use M2G\Gitlab\Contracts\BaseAbstract;
use M2G\Utils\ArrayCollection;

class Issue extends BaseAbstract {

	protected $endpoint = '/api/v3/projects/:project_id/issues/:id';
	protected $project;

	public function __construct($project, $raw = null) {
		$this->project($project);

		if (!is_null($raw)) {
			$this->raw = $raw;
		}
	}

	public function project($project = null) {
		if (!is_null($project)) {
			$this->project = $project;
			$this->gitlab($project->gitlab());
			$this->params['project_id'] = urlencode($project->id());
		}

		return $this->project;
	}

	public function newNote(array $raw) {
		$note = new Note($this, $raw);
		return $note->save();
	}

	public function id() {
		$id = $this->raw('id');
		return is_array($id) ? $id['id'] : $id; // sanity check, fixing it elsewhere
	}

	public function notes() {
		return (new Note($this, null))->all();
	}

	public function close() {
		$this->params['id'] = $this->id();
		$return = $this->put(array(
			'state_event' => 'close'
		));

		if (!empty($return['message'])) {
			throw new \Exception($return['message']);
		}

		return $this;
	}
}
