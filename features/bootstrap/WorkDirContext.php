<?php

/**
 * Utility for creating a working directory for scenarios.
 * Proudly stolen from PHPSpec.
 */
trait WorkDirContext
{
    /**
    * @var string|null
    */
    private $workDir = null;

    /**
    * @BeforeScenario
    */
    public function createWorkDir()
    {
        $this->workDir = sys_get_temp_dir().'/'.uniqid('WorkDirContext_').'/';

        mkdir($this->workDir, 0777, true);
        chdir($this->workDir);
    }

    /**
    * @AfterScenario
    */
    public function removeWorkDir()
    {
        system('rm -rf '.$this->workDir);
    }
}

