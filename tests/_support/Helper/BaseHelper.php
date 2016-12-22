<?php

namespace Helper;

class BaseHelper extends \Codeception\Module
{
    /**
     * @return WebDriver
     */
    protected function getWebDriver()
    {
        return $this->getModule('WebDriver');
    }
}
