<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class ApiProperty
{
	/** @var string */
	public $type;

	/** @var boolean */
	public $collection;

	/**
	 * @param string $class
	 */
	public function __construct ($param) {
		$this->type       = isset($param['type'])       ? $param['type']       : null;
		$this->collection = isset($param['collection']) ? $param['collection'] : false;
	}
}