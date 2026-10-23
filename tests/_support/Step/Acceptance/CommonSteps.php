<?php
namespace Step\Acceptance;

use Page\PleskLoginPage;
use Page\PleskHomePage;
use Page\SpamExpertsEmailSecurityPage;
use Codeception\Step\Argument\PasswordArgument;
use Codeception\Util\Locator;

class CommonSteps extends \AcceptanceTester
{
	// Current addon name
    protected $currentBrandname = 'SpamExperts Email Security';

    // Used for save created accounts and to cleanup them when finish the test
    private static $accounts = array();

    // Used to check if logged in as client
    private static $loggedInAsClient = false;

    // Used to save default package name
    private $defaultPackage = 'package1';

    /**
     * Function used to login as root
     */
    public function loginAsAdminstrator()
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
            $this->loginAsAdminstrator();
            return;
        }

        // Go to login page
        $this->amOnUrl(getenv($this->getEnvParameter('url')));

        // Dismiss the cookie consent banner, if shown
        if ($this->grabMultiple(PleskLoginPage::ACCEPT_NECESSARY_COOKIES_BTN)) {
            $this->click(PleskLoginPage::ACCEPT_NECESSARY_COOKIES_BTN);
        }

        // Fill the username field
        $this->waitForElement(Locator::combine(
        	PleskLoginPage::USERNAME_FIELD_XPATH,
        	PleskLoginPage::USERNAME_FIELD_CSS), 10);
        $this->fillField(Locator::combine(
        	PleskLoginPage::USERNAME_FIELD_XPATH,
        	PleskLoginPage::USERNAME_FIELD_CSS), $username);

        // Fill password field
        $this->waitForElement(Locator::combine(
        	PleskLoginPage::PASSWORD_FIELD_XPATH,
        	PleskLoginPage::PASSWORD_FIELD_CSS), 10);
        // Mask the password in the test output
        $this->fillField(Locator::combine(
        	PleskLoginPage::PASSWORD_FIELD_XPATH,
        	PleskLoginPage::PASSWORD_FIELD_CSS), new PasswordArgument($password));

        // Click the login button
        $this->click(PleskLoginPage::LOGIN_BTN_CSS);

        // Wait for all frames to show
        $this->waitForElement(PleskHomePage::LEFT_SIDE_MENU, 10);
        $this->waitForElement(PleskHomePage::PAGE_HEADER, 10);
        $this->waitForElement(PleskHomePage::PAGE_CONTENT, 10);

        // Cancel the "Welcome to Plesk!" survey, if shown (it opens shortly after the page loads)
        $this->wait(3);
        if ($this->grabMultiple(PleskHomePage::SURVEY_CANCEL_BTN)) {
            $this->click(PleskHomePage::SURVEY_CANCEL_BTN);
        }
    }

    /**
     * Function used to logout from cPanel if logged in as root
     */
    public function logout()
    {
        // Click logout button
        $this->click(PleskHomePage::ACCOUNT_MENU_XPATH,
        	PleskLoginPage::PAGE_HEADER);
        $this->waitForElement(PleskHomePage::LOGOUT_BTN);
        $this->click(PleskHomePage::LOGOUT_BTN);
    }

    /**
     * Function used to access the email
     * security plesk extension
     */
    public function openEmailSecurityExtension()
    {
        $this->amGoingTo(
            "\n\n --- Open SpamExperts Email Security extension --- \n");

        // Open the extension directly, independently of the Plesk extensions catalog page
        $this->amOnUrl(rtrim(getenv($this->getEnvParameter('url')), '/')
            . '/modules/spamexperts-extension/index.php');
        $this->waitForText(SpamExpertsEmailSecurityPage::TITLE, 30);
    }

    /**
     * Function used to go to certain plugin page
     * @param $page - page name
     * @param $title - page title
     */
    public function goToPage($tab)
    {
        $this->amGoingTo("\n\n --- Go to {$tab} page --- \n");

        $selectedTab = $tab . "[@aria-selected='true']";

        $this->click($tab);
        try {
            $this->waitForElement($selectedTab, 10);
        } catch (\Exception $e) {
            /*The click can be lost while the page is still busy after a form save*/
            $this->click($tab);
            $this->waitForElement($selectedTab, 10);
        }
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