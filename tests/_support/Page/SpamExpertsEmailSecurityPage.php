<?php
namespace Page;

class SpamExpertsEmailSecurityPage
{
	const TITLE = "SpamExperts Email Security";

    const DOMAINS_TAB = "//*[contains(@href,'/modules/spamexperts-extension/index.php/index/domains')]";
    const SETTINGS_TAB = "//*[contains(@href,'/modules/spamexperts-extension/index.php/index/settings')]";
    const BRANDING_TAB = "//*[contains(@href,'/modules/spamexperts-extension/index.php/index/branding')]";
    const SUPPORT_TAB = "//*[contains(@href,'/modules/spamexperts-extension/index.php/index/support')]";

    /*Error alert*/
    const ERROR_ALERT_XPATH = "//div[@class='msg-box msg-error']";
    const ERROR_ALERT_CSS = ".msg-box.msg-error";

    /*Warning alert*/
    const WARNING_ALERT_XPATH = "//div[@class='msg-box msg-warning']";
    const WARNING_ALERT_CSS = ".msg-box.msg-warning";

    /*Success alert*/
    const SUCCESS_ALERT_XPATH = "//div[@class='msg-box msg-info']";
    const SUCCESS_ALERT_CSS = ".msg-box.msg-info";

    /*Notice alert*/
    const NOTICE_ALERT_XPATH = "//div[@class='msg-box msg-notice']";
    const NOTICE_ALERT_CSS = ".msg-box.msg-error";
}
