<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

interface TypeInterface {
	public function getType(): string;

	public function toJson(): array;
}