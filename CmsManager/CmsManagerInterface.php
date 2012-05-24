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

use Sonata\BlockBundle\Model\BlockInterface;

use Sonata\CacheBundle\Cache\CacheElement;

use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The CmsManagerInterface class is in charge of retrieving the correct page (cms page or action page)
 *
 * An action page is linked to a symfony action and a cms page is a standalone page.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
interface CmsManagerInterface
{
    /**
     * Returns a block container matching the provided name and page
     *
     * @param string                                        $name            Block name
     * @param \Sonata\PageBundle\Model\PageInterface        $page            Page object
     * @param null|\Sonata\BlockBundle\Model\BlockInterface $parentContainer Parent Block
     *
     * @return bool|null|\Sonata\BlockBundle\Model\BlockInterface
     */
    function findContainer($name, PageInterface $page, BlockInterface $parentContainer = null);

    /**
     * Returns a fully loaded page ( + blocks ) from a url
     *
     * @param \Sonata\PageBundle\Model\SiteInterface $site Site object
     * @param string                                 $slug Slug name
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getPageByUrl(SiteInterface $site, $slug);

    /**
     * Returns a fully loaded page ( + blocks ) from a route name
     *
     * @param \Sonata\PageBundle\Model\SiteInterface $site      Site object
     * @param string                                 $routeName Route name
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getPageByRouteName(SiteInterface $site, $routeName);

    /**
     * Returns a fully loaded page ( + blocks ) from an internal page name
     *
     * @param \Sonata\PageBundle\Model\SiteInterface $site      Site object
     * @param string                                 $routeName Route Name
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getInternalRoute(SiteInterface $site, $routeName);

    /**
     * Returns a fully loaded page ( + blocks ) from a name
     *
     * @param \Sonata\PageBundle\Model\SiteInterface $site Site object
     * @param string                                 $name Page name
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getPageByName(SiteInterface $site, $name);

    /**
     * Returns a fully loaded page ( + blocks ) from a page id
     *
     * @param integer $id Page Id
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getPageById($id);

    /**
     * Returns a Block by its Id
     *
     * @param integer $id Block id
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getBlock($id);

    /**
     * Returns the current page
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getCurrentPage();

    /**
     * Sets the current page
     *
     * @param \Sonata\PageBundle\Model\PageInterface $page
     */
    function setCurrentPage(PageInterface $page);

    /**
     * Returns the list of loaded block from the current http request
     *
     * @return array
     */
    function getBlocks();

    /**
     * Returns the page object from a mixed page variable
     *
     * @param \Sonata\PageBundle\Model\SiteInterface $site Site object
     * @param mixed                                  $page A mixed value representing a page
     *
     * @return \Sonata\PageBundle\Model\PageInterface
     */
    function getPage(SiteInterface $site, $page);
}