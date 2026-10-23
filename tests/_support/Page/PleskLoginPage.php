<?php
namespace Page;

class PleskLoginPage
{
    const USERNAME_FIELD_XPATH = "//input[@id='login_name']";
    const USERNAME_FIELD_CSS = "#login_name";

    const PASSWORD_FIELD_XPATH = "//input[@id='passwd']";
    const PASSWORD_FIELD_CSS = "#passwd";

    const INTERFACE_LANG_XPATH = "//select[@id='fid-locale_id']";
    const INTERFACE_LANG_CSS = "#fid-locale_id";

    const LOGIN_BTN_XPATH = "//button[@data-action='log-in']";
    const LOGIN_BTN_CSS = "button[data-action='log-in']";

    /*Cookie consent banner shown on the login page*/
    const ACCEPT_NECESSARY_COOKIES_BTN = "//button[@data-type='cookie-policy-info-accept-necessary-button']";
}
