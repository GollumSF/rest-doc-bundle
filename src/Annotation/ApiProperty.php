<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class ApiProperty
{
	/** @var string */
	public $type;
	
	/** @var boolean */
	public $collection;
	
	/**
	 * @param string $type
	 * @param boolean $collection
	 */
	public function __construct(
		$type = null,
		$collection = false
	) {
		if (is_array($type)) {
			if (function_exists('trigger_deprecation')) {
				// @codeCoverageIgnoreStart
				trigger_deprecation('gollumsf/rest__doc_bundle', '2.8', 'Use native php attributes for %s', __CLASS__);
				// @codeCoverageIgnoreEnd
			}
			$this->type = isset($type['type']) ? $type['type'] : null;
			$this->collection = isset($type['collection']) ? $type['collection'] : $collection;
			return;
		}
		$this->type = $type;
		$this->collection = $collection;
	}

	/////////////
	// Getters //
	/////////////

	public function getType(): ?string {
		return $this->type;
	}

	public function isCollection(): bool {
		return $this->collection;
	}
}
