<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();


        /**
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable(
            $installer->getTable('devester_poll_poll')
        );
        $installer->getConnection()->dropTable(
            $installer->getTable('devester_poll_answer')
        );
        $installer->getConnection()->dropTable(
            $installer->getTable('devester_poll_store')
        );


        /**
         * Create table devester_poll
         */
        $table = $installer->getConnection()
        	->newTable($installer->getTable('devester_poll_poll'))
            ->addColumn(
                'poll_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Poll ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Poll Title'
            )
            ->addColumn(
                'votes_count',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'default' => '0'],
                'Votes'
            )
            ->addColumn(
                'date_posted',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Date Posted'
            )
            ->addColumn(
                'date_closed',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Closed'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Status'
            )
            ->addIndex(
                $installer->getIdxName(
                    'devester_poll_poll',
                    ['title'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            )
            ->setComment('Devester Polls');
        $installer->getConnection()->createTable($table);
        /**
         * End create table devester_poll_poll
         */


        /**
         * Create table devester_poll_answer
         */
        $table = $installer->getConnection()
        ->newTable($installer->getTable('devester_poll_answer'))
        	->addColumn(
                'answer_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Answer ID'
            )
            ->addColumn(
                'poll_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => false],
                'Poll ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Answer title'
            )
            ->addColumn(
                'votes_count',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'default' => '1'],
                'Votes Count'
            )
            ->addColumn(
                'answer_order',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '0'],
                'Answers display order'
            ) 
            ->addIndex(
                $installer->getIdxName(
                    'devester_poll_answer',
                    ['poll_id']
                ),
                ['poll_id']
            )
            ->setComment('Devester polls Answers');
        $installer->getConnection()->createTable($table);
        /**
         * End create table devester_poll_answer
         */


        /**
         * Create table devester_poll_store
         */
		$table = $installer->getConnection()
        	->newTable($installer->getTable('devester_poll_store'))
        	->addColumn(
                'poll_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Poll ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    'devester_poll_store',
                    ['store_id']
                ),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'devester_poll_store',
                    'poll_id',
                    'devester_poll_poll',
                    'poll_id'
                ),
                'poll_id',
                $installer->getTable('devester_poll_poll'),
                'poll_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'devester_poll_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Devester Store Table');
        $installer->getConnection()->createTable($table);
        /**
         * End create table devester_poll_store
         */



        $installer->endSetup();
    }
}
