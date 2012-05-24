<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PageBundle\CmsManager;

use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Base class CMS Manager
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class BaseCmsPageManager implements CmsManagerInterface
{
    protected $currentPage;

    protected $blocks = array();

    /**
     * {@inheritdoc}
     */
    public function findContainer($name, PageInterface $page, BlockInterface $parentContainer = null)
    {
        if ($parentContainer) {
            // parent container is set, nothing to find, don't need to loop across the
            // name to find the correct container (main template level)
            return $parentContainer;
        }

        // search page
        $container = $this->findBlockByName($name, $page) ;
        if ($container) {

            return $container;
        }

        // search page's parents
        if ($page->getParents()) {
            foreach ($page->getParents() as $parent) {
                $container = $this->findBlockByName($name, $parent);
                if ($container) {

                    return $container;
                }
            }
        }

        return false;
    }

    /**
     * Returns a block by name
     *
     * @param string                                 $name Name of Block
     * @param \Sonata\PageBundle\Model\PageInterface $page Page to look for block
     *
     * @return \Sonata\BlockBundle\Model\BlockInterface
     */
    protected function findBlockByName($name, PageInterface $page)
    {
        $container = false;

        if ($page->getBlocks()) {
            foreach ($page->getBlocks() as $block) {
                if ($block->getSetting('name') == $name) {

                    $container = $block;
                    break;
                }
            }
        }

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPage(PageInterface $page)
    {
        $this->currentPage = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageByUrl(SiteInterface $site, $url)
    {
        return $this->getPageBy($site, 'url', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageByRouteName(SiteInterface $site, $routeName)
    {
        return $this->getPageBy($site, 'routeName', $routeName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageByName(SiteInterface $site, $name)
    {
        return $this->getPageBy($site, 'name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageById($id)
    {
        return $this->getPageBy(null, 'id', $id);
    }

    /**
     * @param null|\Sonata\PageBundle\Model\SiteInterface $site
     * @param string                                      $fieldName
     * @param mixed                                       $value
     *
     * @return PageInterface
     */
    abstract protected function getPageBy(SiteInterface $site = null, $fieldName, $value);
}
