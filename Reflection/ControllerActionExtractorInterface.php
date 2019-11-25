<?php

namespace GollumSF\RestDocBundle\Reflection;

use Symfony\Component\Routing\Route;

interface ControllerActionExtractorInterface
{
	public function extractFromRoute(Route $router): ?ControllerAction;
}