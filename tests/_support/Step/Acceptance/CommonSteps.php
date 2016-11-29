<?php
namespace Step\Acceptance;

use Page\PleskExtensionLoginPage;
use Page\PleskExtensionHomePage;
use Codeception\Util\Locator;

class CommonSteps extends \AcceptanceTester
{
	// Current addon name
    protected $currentBrandname = 'Professional Spam Filter';

    // Used for save created accounts and to cleanup them when finish the test
    private static $accounts = array();

    // Used to check if logged in as client
    private static $loggedInAsClient = false;

    // Used to save default package name
    private $defaultPackage = 'package1';

    /**
     * Function used to login as root
     */
    public function loginAsRoot()
    {
        // Get root credentials from environment variables
        $user = getenv($this->getEnvParameter('username'));
        $pass = getenv($this->getEnvParameter('password'));

        // Login with those credentials
        $this->login($user, $pass);

        // I am not logged in as client
        self::$loggedInAsClient = false;
    }

    /**
     * Function used to login, if no credentials provided will login as root
     * @param string $username - username
     * @param string $password - password
     */
    public function login($username = null, $password = null)
    {
        // If no credentials provided, login as root
        if (!$username && !$password) {
            $this->loginAsRoot();
            return;
        }

        // Go to login page
        $this->amOnUrl(getenv($this->getEnvParameter('url')));

        // Fill the username field
        $this->waitForElement(Locator::combine(
        	PleskExtensionLoginPage::USERNAME_FIELD_XPATH,
        	PleskExtensionLoginPage::USERNAME_FIELD_CSS), 10);
        $this->fillField(Locator::combine(
        	PleskExtensionLoginPage::USERNAME_FIELD_XPATH,
        	PleskExtensionLoginPage::USERNAME_FIELD_CSS), $username);

        // Fill password field
        $this->waitForElement(Locator::combine(
        	PleskExtensionLoginPage::PASSWORD_FIELD_XPATH,
        	PleskExtensionLoginPage::PASSWORD_FIELD_CSS), 10);
        $this->fillField(Locator::combine(
        	PleskExtensionLoginPage::PASSWORD_FIELD_XPATH,
        	PleskExtensionLoginPage::PASSWORD_FIELD_CSS), $password);

        // Click the login button
        $this->click(Locator::combine(
        	PleskExtensionLoginPage::LOGIN_BTN_XPATH,
        	PleskExtensionLoginPage::LOGIN_BTN_CSS));

        // Wait for all frames to show
        $this->waitForElement(PleskExtensionHomePage::LEFT_SIDE_MENU, 10);
        $this->waitForElement(PleskExtensionHomePage::PAGE_HEADER, 10);
        $this->waitForElement(PleskExtensionHomePage::PAGE_CONTENT, 10);
    }

    /**
     * Function used to logout from cPanel if logged in as root
     */
    public function logout()
    {
        // Click logout button
        $this->click(PleskExtensionHomePage::ACCOUNT_MENU_XPATH,
        	PleskExtensionLoginPage::PAGE_HEADER);
        $this->waitForElement(PleskExtensionLoginPage::LOGOUT_BTN);
        $this->click(PleskExtensionLoginPage::LOGOUT_BTN);
    }

     /**
     * Function used to go to certain plugin page
     * @param $page - page name
     * @param $title - page title
     */
    public function goToPage($page, $title)
    {
        $this->amGoingTo("\n\n --- Go to {$title} page --- \n");

        $this->switchToWindow();
        $this->reloadPage();
        $this->switchToMainFrame();
        $this->waitForText('Plugins');
        $this->click('Plugins');
        $this->waitForText($this->currentBrandname);
        $this->click($this->currentBrandname);
        $this->switchToMainFrame();
        $this->waitForText($this->currentBrandname);
        $this->see($this->currentBrandname);
        $this->waitForText('Configuration');
        $this->click($page);
        $this->waitForText($title);
    }

    /**
     * Function used to generate random domain name
     * @return string - domain
     */
    public function generateRandomDomainName()
    {
        $domain = uniqid("domain") . ".example.net";
        $this->comment("I generated random domain: $domain");

        return $domain;
    }

    /**
     * Function used to generate random username
     * @return string - domain
     */
    public function generateRandomUserName()
    {
        // cPanel requires first 8 chars to be unique and not start with a number
        $username = 'u'.strrev(uniqid());
        $this->comment("I generated random username: $username");

        return $username;
    }
}