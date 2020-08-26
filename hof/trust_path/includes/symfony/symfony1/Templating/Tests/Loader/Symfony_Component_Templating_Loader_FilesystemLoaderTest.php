<?php

require_once __DIR__.'/../Fixtures/ProjectTemplateDebugger.php';

/**
 * Test class for Symfony_Component_Templating_Loader_FilesystemLoader.
 * Generated by PHPUnit on 2012-02-25 at 20:11:08.
 */
class Symfony_Component_Templating_Loader_FilesystemLoaderTest extends PHPUnit_Framework_TestCase
{

	static protected $fixturesPath;

    static public function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/../Fixtures/');
    }

    public function testConstructor()
    {
        $pathPattern = self::$fixturesPath.'/templates/%name%.%engine%';
        $path = self::$fixturesPath.'/templates';
        $loader = new Symfony_Component_Templating_Loader_ProjectTemplateLoader2($pathPattern);
        $this->assertEquals(array($pathPattern), $loader->getTemplatePathPatterns(), '__construct() takes a path as its second argument');
        $loader = new Symfony_Component_Templating_Loader_ProjectTemplateLoader2(array($pathPattern));
        $this->assertEquals(array($pathPattern), $loader->getTemplatePathPatterns(), '__construct() takes an array of paths as its second argument');
    }

    public function testIsAbsolutePath()
    {
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('/foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('c:\\\\foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('c:/foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('\\server\\foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('https://server/foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
        $this->assertTrue(Symfony_Component_Templating_Loader_ProjectTemplateLoader2::isAbsolutePath('phar://server/foo.xml'), '->isAbsolutePath() returns true if the path is an absolute path');
    }

    public function testLoad()
    {
        $pathPattern = self::$fixturesPath.'/templates/%name%';
        $path = self::$fixturesPath.'/templates';
        $loader = new Symfony_Component_Templating_Loader_ProjectTemplateLoader2($pathPattern);
        $storage = $loader->load(new Symfony_Component_Templating_TemplateReference($path.'/foo.php', 'php'));
        $this->assertInstanceOf('Symfony_Component_Templating_Storage_FileStorage', $storage, '->load() returns a FileStorage if you pass an absolute path');
        $this->assertEquals($path.'/foo.php', (string) $storage, '->load() returns a FileStorage pointing to the passed absolute path');

        $this->assertFalse($loader->load(new Symfony_Component_Templating_TemplateReference('bar', 'php')), '->load() returns false if the template is not found');

        $storage = $loader->load(new Symfony_Component_Templating_TemplateReference('foo.php', 'php'));
        $this->assertInstanceOf('Symfony_Component_Templating_Storage_FileStorage', $storage, '->load() returns a FileStorage if you pass a relative template that exists');
        $this->assertEquals($path.'/foo.php', (string) $storage, '->load() returns a FileStorage pointing to the absolute path of the template');

        $loader = new Symfony_Component_Templating_Loader_ProjectTemplateLoader2($pathPattern);
        $loader->setDebugger($debugger = new ProjectTemplateDebugger());
        $this->assertFalse($loader->load(new Symfony_Component_Templating_TemplateReference('foo.xml', 'php')), '->load() returns false if the template does not exists for the given engine');
        $this->assertTrue($debugger->hasMessage('Failed loading template'), '->load() logs a "Failed loading template" message if the template is not found');

        $loader = new Symfony_Component_Templating_Loader_ProjectTemplateLoader2(array(self::$fixturesPath.'/null/%name%', $pathPattern));
        $loader->setDebugger($debugger = new ProjectTemplateDebugger());
        $loader->load(new Symfony_Component_Templating_TemplateReference('foo.php', 'php'));
        $this->assertTrue($debugger->hasMessage('Loaded template file'), '->load() logs a "Loaded template file" message if the template is found');
    }
}

class Symfony_Component_Templating_Loader_ProjectTemplateLoader2 extends Symfony_Component_Templating_Loader_FilesystemLoader
{
    public function getTemplatePathPatterns()
    {
        return $this->templatePathPatterns;
    }

    static public function isAbsolutePath($path)
    {
        return parent::isAbsolutePath($path);
    }
}

?>
