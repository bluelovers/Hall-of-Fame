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
 * sfServiceContainerLoader is the abstract class used by all built-in loaders that are file based.
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
abstract class sfServiceContainerLoaderFile extends sfServiceContainerLoader
{
  protected
    $paths = array();

  /**
   * Constructor.
   *
   * @param sfServiceContainerBuilder $container A sfServiceContainerBuilder instance
   * @param string|array              $paths     A path or an array of paths where to look for resources
   */
  public function __construct(sfServiceContainerBuilder $container = null, $paths = array())
  {
    parent::__construct($container);

    if (!is_array($paths))
    {
      $paths = array($paths);
    }

    $this->paths = $paths;
  }

  /**
   * Loads a resource.
   *
   * A resource is a file or an array of files.
   *
   * The concrete classes always have access to an array of files
   * as this method converts single file argument to an array.
   *
   * @param mixed $resource The resource path
   */
  public function load($resource)
  {
    if (!is_array($resource))
    {
      $resource = array($resource);
    }

    return parent::load($resource);
  }

  protected function getAbsolutePath($file, $currentPath = null)
  {
    if (self::isAbsolutePath($file))
    {
      return $file;
    }
    else if (null !== $currentPath && file_exists($currentPath.DIRECTORY_SEPARATOR.$file))
    {
      return $currentPath.DIRECTORY_SEPARATOR.$file;
    }
    else
    {
      foreach ($this->paths as $path)
      {
        if (file_exists($path.DIRECTORY_SEPARATOR.$file))
        {
          return $path.DIRECTORY_SEPARATOR.$file;
        }
      }
    }

    return $file;
  }

  static protected function isAbsolutePath($file)
  {
    if ($file[0] == '/' || $file[0] == '\\' ||
        (strlen($file) > 3 && ctype_alpha($file[0]) &&
         $file[1] == ':' &&
         ($file[2] == '\\' || $file[2] == '/')
        )
       )
    {
      return true;
    }

    return false;
  }
}
