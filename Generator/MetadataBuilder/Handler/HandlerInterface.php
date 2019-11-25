<?php

namespace GollumSF\RestDocBundle\Generator\MetadataBuilder\Handler;

interface HandlerInterface
{
	public function getMetadataCollection(): array;
}