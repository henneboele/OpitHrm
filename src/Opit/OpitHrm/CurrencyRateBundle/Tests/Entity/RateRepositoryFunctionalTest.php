<?php

/*
 * This file is part of the OPIT-HRM project.
 *
 * (c) Opit Consulting Kft. <info@opit.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opit\OpitHrm\CurrencyRateBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of RateRepositoryFunctionalTest
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package OPIT-HRM
 * @subpackage CurrencyRateBundle
 */
class RateRepositoryFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Set up the testing
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * test FindAllByDateInterval method
     */
    public function testFindAllByDateInterval()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->findAllByDates(new \DateTime('yesterday'), new \DateTime('today'));

        $this->assertNotNull($rate, 'FindAllByDates: The given result is null.');
    }

    /**
     * test FindFirstRate method
     */
    public function testFindFirstRate()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->findFirstRate();

        $this->assertNotNull($rate, 'FindFirstRate: The given result is null.');
    }

    /**
     * test FindLastRate method
     */
    public function testFindLastRate()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->findLastRate();

        $this->assertNotNull($rate, 'FindLastRate: The given result is null.');
    }

    /**
     * test HasRate method
     */
    public function testHasRate()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->hasRate('EUR', new \DateTime('today'));

        $this->assertTrue($rate, 'HasRate: The given result is null.');
    }

    /**
     * test FindRateByCodeAndDate method
     */
    public function testFindRateByCodeAndDate()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->findRateByCodeAndDate('EUR', new \DateTime('today'));

        $this->assertNotNull($rate, 'FindRateByCodeAndDate: The given result is null.');
    }

    /**
     * test GetRatesArray
     */
    public function testGetRatesArray()
    {
        $rate = $this->em->getRepository("OpitOpitHrmCurrencyRateBundle:Rate")
                    ->getRatesArray(new \DateTime('yesterday'));

        $this->assertNotNull($rate, 'GetRatesArray: The given result is null.');
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
