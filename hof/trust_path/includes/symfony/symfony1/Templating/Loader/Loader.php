<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Symfony\Component\Templating\Loader;

//use Symfony\Component\Templating\DebuggerInterface;

/**
 * Loader is the base class for all template loader classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Symfony_Component_Templating_Loader_Loader implements Symfony_Component_Templating_Loader_LoaderInterface
{
    protected $debugger;

    /**
     * Sets the debugger to use for this loader.
     *
     * @param DebuggerInterface $debugger A debugger instance
     */
    public function setDebugger(Symfony_Component_Templating_DebuggerInterface $debugger)
    {
        $this->debugger = $debugger;
    }
}
