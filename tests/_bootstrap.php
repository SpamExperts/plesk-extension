<?php
// This is global bootstrap for autoloading

use Symfony\Component\Yaml\Yaml;

class ExtensionConfig
{
    private static $parameters;

    /**
     * Function used to load environment variable names from acceptance.suite.yml file.
     *
     * api_url = environment variable name used to store the AntiSpam API URL
     * api_hostname = environment variable name used to store the API hostname
     * api_password = environment variable name used to store the API password
     * primary_mx = environment variable name used to store the Primary MX
     */
    public static function load()
    {
        // Obtain the path for the acceptance.suite.yml file
        $file = realpath(__DIR__ . '/acceptance.suite.yml');

        // Parse yml file and store the environment variable names in the parameters variable
        self::$parameters = Yaml::parse(file_get_contents($file));
    }

    /**
     * Get the AntiSpam API URL value
     * @return string api_url environment variable value
     */
    public static function getApiUrl()
    {
        return getenv(self::$parameters['env']['api_url']);
    }

    /**
     * Get the API hostname value
     * @return string api_hostname environment variable value
     */
    public static function getApiHostname()
    {
       return getenv(self::$parameters['env']['api_hostname']);
    }

    /**
     * Get the API username value
     * @return string api_username environment variable value
     */
    public static function getApiUsername()
    {
        return getenv(self::$parameters['env']['api_username']);
    }

    /**
     * Get the API password value
     * @return string api_password environment variable value
     */
    public static function getApiPassword()
    {
        return getenv(self::$parameters['env']['api_password']);
    }

    /**
     * Get the Primary MX value
     * @return string primary_mx environment variable value
     */
    public static function getPrimaryMX()
    {
        return getenv(self::$parameters['env']['primary_mx']);
    }

    /**
     * Get the Secondary MX value
     * @return string secondary_mx environment variable value
     */
    public static function getSecondaryMX()
    {
        return getenv(self::$parameters['env']['secondary_mx']);
    }
}


ExtensionConfig::load();
