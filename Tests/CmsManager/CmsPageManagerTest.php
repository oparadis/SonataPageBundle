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

use Sonata\PageBundle\CmsManager\CmsPageManager;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Sonata\BlockBundle\Model\Block;
use Sonata\PageBundle\Tests\Model\Page;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Sonata\CacheBundle\Cache\CacheManagerInterface;
use Sonata\PageBundle\Model\BlockInteractorInterface;

/**
 * Test concrete CmsPageManager class
 */
class CmsPageManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->pageManager = $this->getMock('Sonata\PageBundle\Model\PageManagerInterface');
        $this->blockInteractor = $this->getMockBlockInteractor();
        $this->manager = new CmsPageManager($this->pageManager, $this->blockInteractor);
    }

    public function testFindContainerWithNullParent()
    {
        // GIVEN
        $page = $this->getMockPageWithParents(null);

        // WHEN
        $container = $this->manager->findContainer('newOne', $page);

        // THEN
        $this->assertEquals('newOne', $container->getSetting('name'), 'Should return a new block named "newOne"');
        $this->assertEquals($page, $container->getPage(), 'Should be a block from page');
    }

    public function testFindContainerCreatesInPage()
    {
        // GIVEN
        $page = $this->getMockPageWithBlocks(array());

        // WHEN
        $container = $this->manager->findContainer('newOne', $page);

        // THEN
        $this->assertEquals('newOne', $container->getSetting('name'), 'Should return a new block named "newOne"');
        $this->assertEquals($page, $container->getPage(), 'Should be a block from page');
    }

    public function testFindContainerCreatesInParent()
    {
        // GIVEN
        $parent = $this->getMockPageWithBlocks(array());
        $page = $this->getMockPageWithParents(array($parent));

        // WHEN
        $container = $this->manager->findContainer('newOne', $page);

        // THEN
        $this->assertEquals('newOne', $container->getSetting('name'), 'Should return a new block named "newOne"');
        $this->assertEquals($parent, $container->getPage(), 'Should be a block from parent');
    }

    /**
     * Returns a mock BlockInteractor that mocks the createNewContainer() method
     * and returns a mock block object with values matching the method's parameters
     *
     * @return \Sonata\PageBundle\Model\BlockInteractorInterface
     */
    protected function getMockBlockInteractor()
    {
        $testCase = $this;
        $callback = function($array) use ($testCase) {

            $page = $array['page'];
            $name = $array['name'];
            $mock = $testCase->getMockBlock($name, $page);

            return $mock;
        };

        $blockInteractor = $this->getMock('\Sonata\PageBundle\Model\BlockInteractorInterface');
        $blockInteractor->expects($this->any())
            ->method('createNewContainer')
            ->will($this->returnCallback($callback));

        return $blockInteractor;
    }

    /**
     * Returns a mock Block object with a given name and page
     * to mock getSetting() and getPage() methods
     *
     * @param string $name Name of block
     * @param mixed  $page Page object
     *
     * @return \Sonata\PageBundle\Model\Block
     */
    public function getMockBlock($name, $page)
    {
        $block = $this->getMock('\Sonata\PageBundle\Model\Block');

        $block->expects($this->any())
            ->method('getSetting')
            ->with($this->equalTo('name'))
            ->will($this->returnValue($name));

        $block->expects($this->any())
            ->method('getPage')
            ->will($this->returnValue($page));

        return $block;
    }

    /**
     * Returns a mock Page object with given blocks
     *
     * @param array $blocks An array of block objects
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    protected function getMockPageWithBlocks($blocks)
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
    protected function getMockPageWithParents($parents)
    {
        $page = $this->getMock('\Sonata\PageBundle\Model\PageInterface');
        $page->expects($this->any())
            ->method('getParents')
            ->will($this->returnValue($parents));

        return $page;
    }
}