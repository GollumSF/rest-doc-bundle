<?php

namespace GollumSF\RestDocBundle\Metadata\Handler;

interface HandlerInterface
{
	public function getMetadataCollection(): array;
}