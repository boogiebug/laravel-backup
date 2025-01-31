<?php

namespace Pinacono\Backup\Tests;

use Pinacono\Backup\Tasks\Backup\Zip;

class ZipTest extends TestCase
{
    protected string $pathToZip;

    protected Zip $zip;

    public function setUp(): void
    {
        parent::setUp();

        $this->initializeTempDirectory();

        $this->pathToZip = "{$this->getTempDirectory()}/test.zip";

        $this->zip = new Zip($this->pathToZip);
    }

    /** @test */
    public function it_can_create_a_zip_file()
    {
        $this->zip->add(__FILE__);
        $this->zip->close();

        $this->assertFileExists($this->pathToZip);
    }

    /** @test */
    public function it_can_report_its_own_size()
    {
        $this->assertEquals(0, $this->zip->size());

        $this->zip->add(__FILE__);
        $this->zip->close();

        $this->assertNotEquals(0, $this->zip->size());
    }
}
