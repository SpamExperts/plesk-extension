<?php
namespace Page;

class ProfessionalSpamFilterPage
{
	const TITLE = "Professional SpamFilter";

    const DOMAINS_TAB = "//*[contains(span,'Domains') and @href='/modules/spamexperts-extension/index.php/index/domains']";
    const SETTINGS_TAB = "//*[contains(span,'Settings')";
    const BRANDING_TAB = "//*[contains(span,'Branding')";
    const BRANDING_TAB = "//*[contains(span,'Support')";

    /*AntiSpam API URL input field*/
    const ANTISPAM_API_URL_XPATH = "//input[@id='spampanel_url']";
    const ANTISPAM_API_URL_CSS = "#spampanel_url";

    /*SpamFilter API hostname input field*/
    const ANTISPAM_API_URL_XPATH = "//input[@id='spampanel_url']";
    const ANTISPAM_API_URL_CSS = "#spampanel_url";
}
