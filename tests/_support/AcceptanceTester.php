<?php

use Codeception\Scenario;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */
   	private $parameters = [];

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->parseYaml();
    }

    public function getEnvParameter($name, $default = null)
    {
        if (isset($this->parameters['env'][$name]))
            return $this->parameters['env'][$name];

        return $default;
    }

    private function parseYaml()
    {
        $file = __DIR__.'/../acceptance.suite.yml';
        $this->parameters = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($file));
    }
}
