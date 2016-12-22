<?php

namespace Page;

class SettingsPage
{
    const SAVE_BUTTON_XPATH = "//*[@type='button']";
    const SAVE_BUTTON_CSS = "#btn-send>button";

    /*AntiSpam API URL input field*/
    const ANTISPAM_API_URL_XPATH = "//input[@id='spampanel_url']";
    const ANTISPAM_API_URL_CSS = "#spampanel_url";

    /*SpamFilter API hostname input field*/
    const SPAMFILTER_API_HOSTNAME_XPATH = "//input[@id='apihost']";
    const SPAMFILTER_API_HOSTNAME_CSS = "#apihost";

    /*SpamFilter API hostname input field*/
    const SPAMFILTER_API_USERNAME_XPATH = "//input[@id='apiuser']";
    const SPAMFILTER_API_USERNAME_CSS = "#apiuser";

    /*SpamFilter API password input field*/
    const SPAMFILTER_API_PASSWORD_XPATH = "//input[@id='apipass']";
    const SPAMFILTER_API_PASSWORD_CSS = "#apipass";

    /*Primary MX input field*/
    const PRIMARY_MX_XPATH = "//input[@id='mx1']";
    const PRIMARY_MX_CSS = "#mx1";

    /*Secondary MX input field*/
    const SECONDARY_MX_XPATH = "//input[@id='mx2']";
    const SECONDARY_MX_CSS = "#mx2";

    /*Tertiary MX input field*/
    const TERTIARY_MX_XPATH = "//input[@id='mx3']";
    const TERTIARY_MX_CSS = "#mx3";

    /*Quaternary MX input field*/
    const QUATERNARY_MX_XPATH = "//input[@id='mx3']";
    const QUATERNARY_MX_CSS = "#mx3";

    /*Automatic action for a new domain when it is added to this server*/
    const PROTECT_AUTO_ACTION_FOR_NEW_DOMAIN = "//input[@id='auto_add_domain-1']";
    const SKIP_AUTO_ACTION_FOR_NEW_DOMAIN = "//input[@id='auto_add_domain-0']";

    /*Automatic action for a domain when it is deleted from this server*/
    const UNPROTECT_AUTO_ACTION_FOR_DELETED_DOMAINS = "//input[@id='auto_del_domain-1']";
    const SKIP_AUTO_ACTION_FOR_DELETED_DOMAINS = "//input[@id='auto_del_domain-0']";

    /*Action on the MX records for protected/unprotected domains*/
    const UPDATE_ACTION_MX_RECORD_PROTECT_UNPROTECT = "//input[@id='provision_dns-1']";
    const SKIP_ACTION_MX_RECORD_PROTECT_UNPROTECT = "//input[@id='provision_dns-0']";

    /*Primary contact email for protected domains*/
    const SET_PRIMARY_CONTACT_EMAIL = "//input[@id='set_contact-1']";
    const SKIP_PRIMARY_CONTACT_EMAIL = "//input[@id='set_contact-0']";

    /*Action on secondary domains (domain aliases) */
    const PROTECT_AS_DOMAINS_SECONDARY_DOMAINS = "//input[@id='handle_extra_domains-1']";
    const PROTECT_AS_ALIASES_SECONDARY_DOMAINS = "//input[@id='handle_extra_domains-2']";
    const SKIP_ACTIONS_SECONDARY_DOMAINS = "//input[@id='handle_extra_domains-0']";

    /*Action on "remote" domains (hosted on external DNS servers)*/
    const PROTECT_ACTIONS_REMOTE_DOMAINS = "//input[@id='handle_only_localdomains-0']";
    const SKIP_ACTIONS_REMOTE_DOMAINS = "//input[@id='handle_only_localdomains-1']";

    /*Redirect users upon logout*/
    const REDIRECT_TO_SPAMFILTER_LOGOUT = "//input[@id='redirectback-0']";
    const REDIRECT_BACK_TO_PLESK = "//input[@id='redirectback-1']";

    /*Action upon SpamFilter panel login to not protected domain*/
    const PROTECT_DOMAIN_AND_MAKE_ANOTHER_LOGIN_ATEMPT = "//input[@id='add_domain_loginfail-0']";
    const REPORT_ERROR = "//input[@id='add_domain_loginfail-1']";

    /*Use as destination routes for clean mail when protecting domains */
    const USE_DETINATION_ROUTES_FOR_HOSTNAMES = "//input[@id='use_ip_address_as_destination_routes-0']";
    const USE_DETINATION_ROUTES_FOR_IPADDRESSES = "//input[@id='use_ip_address_as_destination_routes-1']";

    /*Rows where mandatory fields are located*/
    const ANTISPAM_API_URL_ROW = "//div[@id='spampanel_url-form-row']";
    const SPAMFILTER_API_HOSTNAME_ROW = "//div[@id='apihost-form-row']";
    const SPAMFILTER_API_USERNAME_ROW = "//div[@id='apiuser-form-row']";
    const SPAMFILTER_API_PASSWORD_ROW = "//div[@id='apipass-form-row']";
    const PRIMARY_MX_ROW = "//div[@id='mx1-form-row']";
    const SECONDARY_MX_ROW = "//div[@id='mx2-form-row']";

    /*Locators for all options on the settings page*/
    const AUTOMATIC_ADD_DOMAIN = "//div[@id='auto_add_domain-form-row']//label[contains(text(),'%s')]";
    const AUTOMATIC_DELETE_DOMAIN = "//div[@id='auto_del_domain-form-row']//label[contains(text(),'%s')]";
    const ACTIONS_ON_MXRECORDS = "//div[@id='provision_dns-form-row']//label[contains(text(),'%s')]";
    const ACTIONS_ON_CONTACT_EMAIL = "//div[@id='set_contact-form-row']//label[contains(text(),'%s')]";
}
