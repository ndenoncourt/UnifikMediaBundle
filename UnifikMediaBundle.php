<?php

namespace Unifik\MediaBundle;

use Unifik\MediaBundle\DependencyInjection\Compiler\MediaParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UnifikMediaBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new MediaParserCompilerPass());
	}
}
