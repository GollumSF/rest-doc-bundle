<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

interface HandlerInterface
{
	public function getMetadataCollection(): array;
}