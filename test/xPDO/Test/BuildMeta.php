<?php
/**
 * Goals:
 * 1. Test that xPDO generates the PHP meta class files correctly
 * 2. Test that xPDO can create the tables from the generated classes
 */

namespace xPDO\Test;

use xPDO\TestCase;


final class BuildMeta extends TestCase
{

    protected $sample_package_name = 'sample';

    protected $sample_table_prefix = '';

    protected function getBuildMetaModelDirector()
    {
        return dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'db/model/';
    }

    protected function getSchemaXmlFilePath()
    {
        return dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'model/schema/sample.mysql.schema.xml';
    }

    public function testBuildMetaFromSchema()
    {
        /** @var $xPDO */
        $xPDO = self::getInstance(true);
        /**
         * @param \xPDO\Om\mysql $generator
         */
        $generator = $xPDO->getManager()->getGenerator();

        $success = $generator->parseSchema(
            $this->getSchemaXmlFilePath(),
            $this->getBuildMetaModelDirector());


        $this->assertEquals(
            true,
            $success
        );
    }

    public function testAddPackage()
    {
        /** @var $xPDO */
        $xPDO = self::getInstance(true);

        $this->assertEquals(
            true,
            $xPDO->addPackage($this->sample_package_name, $this->getBuildMetaModelDirector(), $this->sample_table_prefix)
        );
    }

    public function testCreateTablesFromGeneratedMeta()
    {

        /** @var $xPDO */
        $xPDO = self::getInstance(true);

        // is this still needed for 3.0, can it be auto loaded?
        $xPDO->addPackage($this->sample_package_name, $this->getBuildMetaModelDirector(), $this->sample_table_prefix);

        /**
         * @param xPDOManager_mysql $manager
         */
        $manager = $xPDO->getManager();

        // can this be done auto?
        $sample_classes = [
            'BloodType',
            'Item',
            'Person',
            'PersonPhone',
            'Phone',
            'xPDOSample'
        ];

        $success = false;
        foreach ($sample_classes as $class) {
            if ($success = $manager->createObjectContainer($class)) {
                echo PHP_EOL.'Created: '.$class;
            } else {
                echo PHP_EOL.'Did not create: '.$class;
                exit();
            }
        }
        $this->assertEquals(
            true,
            $success
        );
    }
}
