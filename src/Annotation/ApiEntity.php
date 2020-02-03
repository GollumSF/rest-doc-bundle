<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ApiEntity {
	
	/** @var string */
	private $description;

	/** @var string */
	private $url;

	/** @var string */
	private $docDescription;

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

	public function getDescription(): ?string {
		return $this->description;
	}

	public function getUrl(): ?string {
		return $this->url;
	}

	public function getDocDescription(): ?string {
		return $this->docDescription;
	}

	/////////////
	// Setters //
	/////////////

	public function setDescription(?string $description): self {
		$this->description = $description;
		return $this;
	}

	public function setUrl(?string $url): self {
		$this->url = $url;
		return $this;
	}

	public function setDocDescription(?string $docDescription): self {
		$this->docDescription = $docDescription;
		return $this;
	}
}