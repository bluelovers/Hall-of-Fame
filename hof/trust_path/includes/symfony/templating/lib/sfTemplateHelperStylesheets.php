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
 * sfTemplateHelperStylesheets is a helper that manages stylesheets.
 *
 * Usage:
 *
 * <code>
 *   $this->stylesheets->add('foo.css', array('media' => 'print'));
 *   echo $this->stylesheets;
 * </code>
 *
 * @package    symfony
 * @subpackage templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTemplateHelperStylesheets extends sfTemplateHelper
{
  protected
    $stylesheets = array();

  /**
   * Adds a stylesheets file.
   *
   * @param string $stylesheet A stylesheet file path
   * @param array  $attributes An array of attributes
   */
  public function add($stylesheet, $attributes = array())
  {
    $this->stylesheets[$this->helperSet->get('assets')->getUrl($stylesheet)] = $attributes;
  }

  /**
   * Returns all stylesheet files.
   *
   * @param array An array of stylesheet files to include
   */
  public function get()
  {
    return $this->stylesheets;
  }

  /**
   * Returns a string representation of this helper as HTML.
   *
   * @return string The HTML representation of the stylesheets
   */
  public function __toString()
  {
    $html = array();
    foreach ($this->stylesheets as $path => $attributes)
    {
      $atts = array();
      foreach ($attributes as $key => $value)
      {
        $atts[] = sprintf('%s="%s"', $key, $this->helperSet->getEngine()->escape($value));
      }

      $html[] = sprintf('<link href="%s" rel="stylesheet" type="text/css" %s />', $path, implode(' ', $atts));
    }

    return implode("\n", $html);
  }

  /**
   * Returns the canonical name of this helper.
   *
   * @return string The canonical name
   */
  public function getName()
  {
    return 'stylesheets';
  }
}
