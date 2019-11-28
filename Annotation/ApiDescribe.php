<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ApiDescribe {
	
	/** @var string */
	public $entity;

	/** @var bool */
	public $collection;

	/** @var string[] */
	public $serializeGroups;

	/** @var string[] */
	public $unserializeGroups;

	/** @var string[] */
	public $requestBodyProperties;

	/** @var string[] */
	public $responseBodyProperties;

	/**
	 * @param string $class
	 */
	public function __construct ($param) {
		$this->entity            = isset ($param['entity'])            ? $param['entity']            : null;
		$this->collection        = isset ($param['collection'])        ? $param['collection']        : null;
		$this->serializeGroups   = isset ($param['serializeGroups'])   ? $param['serializeGroups']   : [];
		$this->unserializeGroups = isset ($param['unserializeGroups']) ? $param['unserializeGroups'] : [];
		$this->requestBodyProperties  = isset ($param['requestBodyProperties'])  ? $param['requestBodyProperties']  : [];
		$this->responseBodyProperties = isset ($param['responseBodyProperties']) ? $param['responseBodyProperties'] : [];
		if (!$this->serializeGroups) {
			$this->serializeGroups = [];
		}
		if (!$this->unserializeGroups) {
			$this->unserializeGroups = [];
		}
		if (!is_array($this->serializeGroups)) {
			$this->serializeGroups = [ $this->serializeGroups ];
		}
		if (!is_array($this->unserializeGroups)) {
			$this->unserializeGroups = [ $this->unserializeGroups ];
		}
	}
}