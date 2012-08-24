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
 * Migrates the name setting of all blocks into a code setting
 */
class MigrateBlockNameSettingCommand extends BaseCommand
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('sonata:page:migrate-block-name-setting');
        $this->addOption('class', null, InputOption::VALUE_OPTIONAL, 'Block entity class',
            'Application\Sonata\PageBundle\Entity\Block');
        $this->setDescription('Migrate the "name" setting of all blocks into a "code" setting');
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

            // only change rows that need to
            if (isset($settings['name'])) {
                $settings['code'] = $settings['name'];
                unset($settings['name']);
                $block->setSettings($settings);
                $count++;
            }

            if ($count%100) {
                $this->getEntityManager()->flush();
            }
        }

        $this->getEntityManager()->flush();

        $output->writeln("Migrated $count blocks");
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
