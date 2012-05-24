<?php


/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PageBundle\Tests\Page;

use Sonata\PageBundle\CmsManager\BaseCmsPageManager;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Sonata\BlockBundle\Model\Block;
use Sonata\PageBundle\Tests\Model\Page;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\CacheBundle\Cache\CacheManagerInterface;
use Sonata\PageBundle\Model\BlockInteractorInterface;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Test abstract BaseCmsPageManager class
 */
class BaseCmsPageManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->pageManager = $this->getMock('Sonata\PageBundle\Model\PageManagerInterface');
        $this->blockInteractor = $this->getMock('Sonata\PageBundle\Model\BlockInteractorInterface');
        $this->manager = new ConcreteCmsPageManager($this->pageManager, $this->blockInteractor);
    }

    public function testCurrentPage()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());

        // WHEN
        $this->manager->setCurrentPage($page);

        // THEN
        $this->assertEquals($page, $this->manager->getCurrentPage(), 'Should return current page set');
    }

    public function testGetPageByUrl()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());
        $site = $this->getMockSite();
        $url = 'http://test.url/';

        // WHEN
        list($returnSite, $returnField, $returnValue) = $this->manager->getPageByUrl($site, $url);

        // THEN
        $this->assertEquals($site, $returnSite, 'Should have the site object');
        $this->assertEquals('url', $returnField, 'Should have the field name');
        $this->assertEquals($url, $returnValue, 'Should have the url');
    }

    public function getPageByRouteName()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());
        $site = $this->getMockSite();
        $routeName = 'myRouteName';

        // WHEN
        list($returnSite, $returnField, $returnValue) = $this->manager->getPageByRouteName($site, $routeName);

        // THEN
        $this->assertEquals($site, $returnSite, 'Should have the site object');
        $this->assertEquals('routeName', $returnField, 'Should have the field name');
        $this->assertEquals($routeName, $returnValue, 'Should have the route name');
    }

    public function getPageByName()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());
        $site = $this->getMockSite();
        $name = 'myName';

        // WHEN
        list($returnSite, $returnField, $returnValue) = $this->manager->getPageByName($site, $name);

        // THEN
        $this->assertEquals($site, $returnSite, 'Should have the site object');
        $this->assertEquals('name', $returnField, 'Should have the field name');
        $this->assertEquals($name, $returnValue, 'Should have the page name');
    }

    public function testFindContainerWithEmpty()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());

        // WHEN
        $container = $this->manager->findContainer('name', $page);

        // THEN
        $this->assertFalse($container, 'Should return no container');
    }

    public function testFindContainerNotFound()
    {
        // GIVEN
        $block = $this->getMockBlockWithName('myName');
        $page = $this->getMockPageWithBlocks(array($block));

        // WHEN
        $container = $this->manager->findContainer('otherName', $page);

        // THEN
        $this->assertFalse($container, 'Should not find any block');
    }

    public function testFindContainerInPage()
    {
        // GIVEN
        $block = $this->getMockBlockWithName('name');
        $page = $this->getMockPageWithBlocks(array($block));

        // WHEN
        $container = $this->manager->findContainer('name', $page);

        // THEN
        $this->assertEquals($block, $container, 'Should return block from page');
    }

    public function testFindContainerInParent()
    {
        // GIVEN
        $block = $this->getMockBlockWithName('name');
        $parent = $this->getMockPageWithBlocks(array($block));
        $page = $this->getMockPageWithParents(array($parent));

        // WHEN
        $container = $this->manager->findContainer('name', $page);

        // THEN
        $this->assertEquals($block, $container, 'Should return block from page parent');
    }

    public function testFindContainerInMultipleParents()
    {
        // GIVEN
        $block = $this->getMockBlockWithName('name');
        $parent1 = $this->getMockPageWithBlocks(array());
        $parent2 = $this->getMockPageWithBlocks(array($block));
        $parent3 = $this->getMockPageWithBlocks(array());
        $page = $this->getMockPageWithParents(array($parent1, $parent2, $parent3));

        // WHEN
        $container = $this->manager->findContainer('name', $page);

        // THEN
        $this->assertEquals($block, $container, 'Should return block from page grand-parent');
    }

    /**
     * Returns a mock Block object with a given name
     *
     * @param string $name
     *
     * @return \Sonata\BlockBundle\Model\Block
     */
    protected function getMockBlockWithName($name)
    {
        $block = $this->getMock('\Sonata\BlockBundle\Model\Block');
        $block->expects($this->once())
            ->method('getSetting')
            ->with($this->equalTo('name'))
            ->will($this->returnValue($name));

        return $block;
    }

    /**
     * Returns a mock Page object with given blocks
     *
     * @param array $blocks An array of block objects
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    protected function getMockPageWithBlocks(array $blocks)
    {
        $page = $this->getMock('\Sonata\PageBundle\Model\PageInterface');
        $page->expects($this->any())
            ->method('getBlocks')
            ->will($this->returnValue($blocks));

        return $page;
    }

    /**
     * Returns a mock Page object with given parents
     *
     * @param array $parents An array of Page objects
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    protected function getMockPageWithParents(array $parents)
    {
        $page = $this->getMock('\Sonata\PageBundle\Model\PageInterface');
        $page->expects($this->any())
            ->method('getParents')
            ->will($this->returnValue($parents));

        return $page;
    }

    /**
     * Returns a mock Site object
     *
     * @return \Sonata\PageBundle\Model\SiteInterface
     */
    protected function getMockSite()
    {
        return $this->getMock('\Sonata\PageBundle\Model\SiteInterface');
    }
}

/**
 * Concrete implementation required to test the abstract class "BaseCmsPageManager"
 */
class ConcreteCmsPageManager extends BaseCmsPageManager
{
    public function getInternalRoute(SiteInterface $site, $routeName)
    {
        return array($site, $routeName);
    }

    public function getBlock($id)
    {
        return $id;
    }

    public function getPageBy(SiteInterface $site = null, $fieldName, $value)
    {
        return array($site, $fieldName, $value);
    }

    public function getPage(SiteInterface $site, $page)
    {
        return array($site, $page);
    }
}