<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Describe
{
	/** @var string */
	public $entity;
	
	/** @var bool */
	public $collection;

	/**
	 * @param string $class
	 */
	public function __construct ($param) {
		$this->entity     = isset ($param["entity"]) ? $param["entity"] : null;
		$this->collection = isset ($param["collection"]) ? $param["collection"] : null;
	}
}