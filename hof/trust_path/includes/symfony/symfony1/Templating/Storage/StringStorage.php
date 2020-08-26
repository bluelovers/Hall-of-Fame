<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Symfony\Component\Templating\Storage;

/**
 * StringStorage represents a template stored in a string.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class Symfony_Component_Templating_Storage_StringStorage extends Symfony_Component_Templating_Storage_Storage
{
    /**
     * Returns the content of the template.
     *
     * @return string The template content
     *
     * @api
     */
    public function getContent()
    {
        return $this->template;
    }
}
