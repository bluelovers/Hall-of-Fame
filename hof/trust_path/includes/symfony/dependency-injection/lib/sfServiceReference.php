<?php

/*
 * This file is part of the symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * sfServiceReference represents a service reference.
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfServiceReference.php 267 2009-03-26 19:56:18Z fabien $
 */
class sfServiceReference
{
  protected
    $id = null;

  /**
   * Constructor.
   *
   * @param string $id The service identifier
   */
  public function __construct($id)
  {
    $this->id = $id;
  }

  /**
   * __toString.
   *
   * @return string The service identifier
   */
  public function __toString()
  {
    return (string) $this->id;
  }
}
