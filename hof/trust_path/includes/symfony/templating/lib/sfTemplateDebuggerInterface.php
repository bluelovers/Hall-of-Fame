<?php

/*
 * This file is part of the symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTemplateLoaderDebuggerInterface is the interface you need to implement
 * to debug template loader instances.
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
interface sfTemplateDebuggerInterface
{
  /**
   * Logs a message.
   *
   * @param string $message A message to log
   */
  function log($message);
}
