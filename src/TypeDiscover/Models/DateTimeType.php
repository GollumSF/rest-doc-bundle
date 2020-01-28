<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

use Symfony\Component\Validator\Constraints\Date;

class DateTimeType implements TypeInterface {
	
	public function getType(): string {
		return 'string';
	}

	public function toJson(array $groups = null): array {
		return [
			'type' => $this->getType(),
			'example' => (new \DateTime())->format(\DateTime::RFC3339)
		];
	}
}