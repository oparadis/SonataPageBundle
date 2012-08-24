<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PageBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update the block name field based on the code setting
 */
class UpdateBlockNameCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('sonata:page:update-block-name');
        $this->addOption('class', null, InputOption::VALUE_OPTIONAL, 'Block entity class',
            'Application\Sonata\PageBundle\Entity\Block');
        $this->setDescription('Updates empty block names with the block codes');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $repository = $this->getRepository($input->getOption('class'));
        $blocks = $repository->findAll();

        foreach ($blocks as $block) {
            $settings = $block->getSettings();

            // set default name with code
            if (isset($settings['code']) && $block->getName() === null) {
                $block->setName($settings['code']);
                $count++;
            }

            if ($count%100) {
                $this->getEntityManager()->flush();
            }
        }

        $this->getEntityManager()->flush();

        $output->writeln("Updated $count blocks");
    }

    /**
     * Returns the entity repository for given class name
     *
     * @param string $class Entity class name
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($class)
    {
        return $this->getEntityManager()->getRepository($class);
    }

    /**
     * Returns the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
