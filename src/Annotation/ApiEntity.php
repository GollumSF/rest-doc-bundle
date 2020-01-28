<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ApiEntity
{
	/** @var string */
	public $description;

	/** @var string */
	public $url;

	/** @var string */
	public $docDescription;

	/**
	 * @param string $class
	 */
	public function __construct ($param) {
		$this->description    = isset ($param['description'])    ? $param['description']    : null;
		$this->url            = isset ($param['url'])            ? $param['url']            : null;
		$this->docDescription = isset ($param['docDescription']) ? $param['docDescription'] : null;
	}
}