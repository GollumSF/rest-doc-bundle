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
	public $collection = false;

	public function __construct(array $values) {
		foreach ($values as $k => $v) {
			if (!method_exists($this, $name = 'set'.$k)) {
				throw new \RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $k, \get_class($this)));
			}

			$this->$name($v);
		}
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

	/////////////
	// Setters //
	/////////////

	public function setType(?string $type): self {
		$this->type = $type;
		return $this;
	}

	public function setCollection(bool $collection): self {
		$this->collection = $collection;
		return $this;
	}
}