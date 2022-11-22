<?php

namespace App\Tests\Period;

use App\Controller\PeriodController;
use App\Entity\Period;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PeriodControllerUnitTest extends TestCase
{
    private PeriodController $controller;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = new PeriodController();
    }

    /**
     * These looks more like integration tests than unit tests
     *
     * @return void
     */
    public function testNewLeave(): void
    {
        // Success test
        $request = new Request();
        $request->request->set('start', '2022-11-22T14:00:00');
        $request->request->set('end', '2022-11-31T15:00:00');

        $jsonResponse = $this->controller->newLeave($request);
        $expectedFile = file_get_contents(__DIR__ . '/data/expected_serialized_newLeave.json');

        $this->assertEquals($expectedFile, $jsonResponse->getContent());

        // RFC3339 test
        $request = new Request();
        $request->request->set('start', '2022-11-22T14:00');
        $request->request->set('end', '2022-11-31T15:00');

        $jsonResponse = $this->controller->newLeave($request);
        $expectedFile = file_get_contents(__DIR__ . '/data/expected_rfc3339_error_newLeave.json');

        $this->assertEquals($expectedFile, $jsonResponse->getContent());

        // Error with dates order test
        $request = new Request();
        $request->request->set('start', '2022-11-31T15:00:00');
        $request->request->set('end', '2022-11-22T14:00:00');

        $jsonResponse = $this->controller->newLeave($request);
        $expectedFile = file_get_contents(__DIR__ . '/data/expected_error_with_dates_newLeave.json');

        $this->assertEquals($expectedFile, $jsonResponse->getContent());
    }

    /**
     * @return void
     * @throws \App\Exceptions\PeriodException
     */
    public function testCreatePeriods(): void
    {
        $start = new \DateTime('2022-11-22T14:00:00');
        $end = new \DateTime('2022-12-31T15:00:00');
        /** @var Period[] $periods */
        $periods = array();

        $this->controller->createPeriods(Period::MEDICAL_TYPE, $start, $end, $periods);

        $this->assertEquals(new \DateTime('2022-11-22T12:00:00'), $periods[0]->getStart());
        $this->assertEquals(new \DateTime('2022-11-30T23:59:59'), $periods[0]->getEnd());
        $this->assertEquals(Period::MEDICAL_TYPE, $periods[0]->getType());
        $this->assertEquals(new \DateTime('2022-12-01T00:00:00'), $periods[1]->getStart());
        $this->assertEquals(new \DateTime('2022-12-31T23:59:59'), $periods[1]->getEnd());
        $this->assertEquals(Period::MEDICAL_TYPE, $periods[1]->getType());
    }

    /**
     * @return void
     * @throws \App\Exceptions\PeriodException
     */
    public function testSerializePeriods(): void
    {
        /** @var Period[] $periods */
        $periods = array();

        $periods[] = new Period(
            Period::LEAVE_TYPE,
            new \DateTime('2022-11-22T00:00:00'),
            new \DateTime('2022-11-22T12:00:00'),
        );
        $periods[] = new Period(
            Period::LEAVE_TYPE,
            new \DateTime('2022-11-22T12:00:00'),
            new \DateTime('2022-11-22T23:59:59'),
        );

        $serialized = $this->controller->serializePeriods($periods);
        $expectedFile = file_get_contents(__DIR__ . '/data/expected_serialized_period.json');

        $this->assertEquals($expectedFile, $serialized);
    }
}