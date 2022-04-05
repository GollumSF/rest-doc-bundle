<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiEntity {
	
	/** @var string */
	private $description;

	/** @var string */
	private $url;

	/** @var string */
	private $docDescription;
	
	/**
	 * @param string $description
	 * @param string $url
	 * @param string $docDescription
	 */
    public function __construct(
		$description = null,
		$url = null,
		$docDescription = null
	) {
		if (is_array($description)) {
			if (function_exists('trigger_deprecation')) {
				// @codeCoverageIgnoreStart
				trigger_deprecation('gollumsf/rest__doc_bundle', '2.8', 'Use native php attributes for %s', __CLASS__);
				// @codeCoverageIgnoreEnd
			}
			$this->description = isset($description['description']) ? $description['description'] : null;
			$this->url = isset($description['url']) ? $description['url'] : $url;
			$this->docDescription = isset($description['docDescription']) ? $description['docDescription'] : $docDescription;
			return;
		}
		$this->description = $description;
		$this->url = $url;
		$this->docDescription = $docDescription;
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
}
