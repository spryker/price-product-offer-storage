<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\SchedulerJenkins\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\SchedulerJobTransfer;
use Generated\Shared\Transfer\SchedulerResponseTransfer;
use Generated\Shared\Transfer\SchedulerScheduleTransfer;
use GuzzleHttp\Psr7\Response;
use Spryker\Zed\SchedulerJenkins\Business\Api\JenkinsApi;
use Spryker\Zed\SchedulerJenkins\Business\Reader\JenkinsJobReader;
use Spryker\Zed\SchedulerJenkins\Business\SchedulerJenkinsBusinessFactory;
use Spryker\Zed\SchedulerJenkins\Business\SchedulerJenkinsFacade;
use Spryker\Zed\SchedulerJenkins\Business\SchedulerJenkinsFacadeInterface;
use Spryker\Zed\SchedulerJenkins\Business\TemplateGenerator\XmlJenkinsJobTemplateGenerator;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group SchedulerJenkins
 * @group Business
 * @group Facade
 * @group SchedulerJenkinsFacadeTest
 * Add your own group annotations below this line
 */
class SchedulerJenkinsFacadeTest extends Unit
{
    /**
     * @return void
     */
    public function testSetupSchedulerJenkins(): void
    {
        $scheduleTransfer = $this->createSchedulerTransfer();
        $schedulerResponseTransfer = $this->getSchedulerFacade()->setupSchedulerJenkins($scheduleTransfer);

        $this->assertInstanceOf(SchedulerResponseTransfer::class, $schedulerResponseTransfer);
        $this->assertTrue($schedulerResponseTransfer->getStatus());
        $this->assertNull($schedulerResponseTransfer->getMessage());
    }

    /**
     * @return void
     */
    public function testCleanSchedulerJenkins(): void
    {
        $scheduleTransfer = $this->createSchedulerTransfer();
        $schedulerResponseTransfer = $this->getSchedulerFacade()->cleanSchedulerJenkins($scheduleTransfer);

        $this->assertInstanceOf(SchedulerResponseTransfer::class, $schedulerResponseTransfer);
        $this->assertTrue($schedulerResponseTransfer->getStatus());
        $this->assertNull($schedulerResponseTransfer->getMessage());
    }

    /**
     * @return void
     */
    public function testSuspendSchedulerJenkinsJobs(): void
    {
        $scheduleTransfer = $this->createSchedulerTransfer();
        $schedulerResponseTransfer = $this->getSchedulerFacade()->suspendSchedulerJenkins($scheduleTransfer);

        $this->assertInstanceOf(SchedulerResponseTransfer::class, $schedulerResponseTransfer);
        $this->assertTrue($schedulerResponseTransfer->getStatus());
        $this->assertNull($schedulerResponseTransfer->getMessage());
    }

    /**
     * @return void
     */
    public function testResumeSchedulerJenkinsJobs(): void
    {
        $scheduleTransfer = $this->createSchedulerTransfer();
        $schedulerResponseTransfer = $this->getSchedulerFacade()->resumeSchedulerJenkins($scheduleTransfer);

        $this->assertInstanceOf(SchedulerResponseTransfer::class, $schedulerResponseTransfer);
        $this->assertTrue($schedulerResponseTransfer->getStatus());
        $this->assertNull($schedulerResponseTransfer->getMessage());
    }

    /**
     * @return \Generated\Shared\Transfer\SchedulerScheduleTransfer
     */
    protected function createSchedulerTransfer(): SchedulerScheduleTransfer
    {
        return (new SchedulerScheduleTransfer())
            ->addJob(
                (new SchedulerJobTransfer())
                    ->setName('DE__test')
                    ->setStore('DE')
                    ->setEnable(true)
                    ->setSchedule('* * * * *')
                    ->setPayload([])
            )
            ->addJob(
                (new SchedulerJobTransfer())
                    ->setName('DE__test1')
                    ->setStore('DE')
                    ->setEnable(true)
                    ->setSchedule('* * * * *')
                    ->setPayload([])
            )
            ->setIdScheduler('test')
            ->setStore('DE');
    }

    /**
     * @return \Spryker\Zed\SchedulerJenkins\Business\SchedulerJenkinsFacadeInterface
     */
    protected function getSchedulerFacade(): SchedulerJenkinsFacadeInterface
    {
        return (new SchedulerJenkinsFacade())
            ->setFactory($this->getSchedulerBusinessFactoryMock());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\SchedulerJenkins\Business\SchedulerJenkinsBusinessFactory
     */
    protected function getSchedulerBusinessFactoryMock()
    {
        $schedulerJenkinsBusinessFactoryMock = $this->getMockBuilder(SchedulerJenkinsBusinessFactory::class)
            ->setMethods(
                [
                    'createJenkinsJobReader',
                    'createXmkJenkinsJobTemplateGenerator',
                    'createJenkinsApi',
                ]
            )
            ->getMock();

        $schedulerJenkinsBusinessFactoryMock
            ->method('createJenkinsJobReader')
            ->willReturn($this->getJenkinsJobReaderMock());

        $schedulerJenkinsBusinessFactoryMock
            ->method('createXmkJenkinsJobTemplateGenerator')
            ->willReturn($this->getJenkinsJobXmlGeneratorMock());

        $schedulerJenkinsBusinessFactoryMock
            ->method('createJenkinsApi')
            ->willReturn($this->createJenkinsApiMock());

        return $schedulerJenkinsBusinessFactoryMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\SchedulerJenkins\Business\Api\JenkinsApiInterface
     */
    protected function createJenkinsApiMock()
    {
        $jenkinsApiMock = $this->getMockBuilder(JenkinsApi::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'executeGetRequest',
                'executePostRequest',
            ])
            ->getMock();

        $jenkinsApiMock
            ->method('executeGetRequest')
            ->willReturn(new Response());

        $jenkinsApiMock
            ->method('executePostRequest')
            ->willReturn(new Response());

        return $jenkinsApiMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\SchedulerJenkins\Business\Reader\JenkinsJobReaderInterface
     */
    protected function getJenkinsJobReaderMock()
    {
        $schedulerJenkinsJobReaderMock = $this->getMockBuilder(JenkinsJobReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerJenkinsJobReaderMock
            ->method('getExistingJobs')
            ->willReturn($this->getExistingJobs());

        return $schedulerJenkinsJobReaderMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\SchedulerJenkins\Business\TemplateGenerator\JenkinsJobTemplateGeneratorInterface
     */
    protected function getJenkinsJobXmlGeneratorMock()
    {
        $schedulerJenkinsJobXmlTemplateGeneratorMock = $this->getMockBuilder(XmlJenkinsJobTemplateGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $schedulerJenkinsJobXmlTemplateGeneratorMock
            ->method('getJobTemplate')
            ->willReturn('');

        return $schedulerJenkinsJobXmlTemplateGeneratorMock;
    }

    /**
     * @return string[]
     */
    protected function getExistingJobs(): array
    {
        return ['DE__test', 'DE__test1'];
    }
}
