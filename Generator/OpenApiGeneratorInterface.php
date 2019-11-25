<?php

namespace GollumSF\RestDocBundle\Generator;

interface OpenApiGeneratorInterface
{
	public function generate(): array;
}