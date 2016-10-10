<?php

namespace M2G;

use SoapClient;

class Gitlab {

	protected $configuration = array();

	public function __construct(array $configuration) {
		$this->configuration = $configuration;
	}

	public function config($config = null) {
		$data = $this->configuration;

		if (!is_null($config) && isset($data[$config])) {
			$data = $data[$config];
		}

		return $data;
	}

	public function __call($method, $params) {
		$subName = __NAMESPACE__ . '\Gitlab\\' . ucfirst($method);
		if (class_exists($subName, true)) {
			$instance = new $subName(array_shift($params));
			$instance->gitlab($this);
			return $instance;
		}
		
		throw new \Exception('Method ' . $className . ' does not exist');
	}

}