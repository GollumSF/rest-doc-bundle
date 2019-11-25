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

	/**
	 * @param string $class
	 */
	public function __construct ($param) {
		$this->entity            = isset ($param['entity'])            ? $param['entity']            : null;
		$this->collection        = isset ($param['collection'])        ? $param['collection']        : null;
		$this->serializeGroups   = isset ($param['serializeGroups'])   ? $param['serializeGroups']   : null;
		$this->unserializeGroups = isset ($param['unserializeGroups']) ? $param['unserializeGroups'] : null;
		if (!is_array($this->serializeGroups)) {
			$this->serializeGroups = [ $this->serializeGroups ];
		}
		if (!is_array($this->unserializeGroups)) {
			$this->unserializeGroups = [ $this->unserializeGroups ];
		}
	}
}