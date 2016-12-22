<?php
namespace Page;

class PleskLoginPage
{
    const USERNAME_FIELD_XPATH = "//input[@id='loginSection-username']";
    const USERNAME_FIELD_CSS = "#loginSection-username";

    const PASSWORD_FIELD_XPATH = "//input[@id='loginSection-password']";
    const PASSWORD_FIELD_CSS = "#loginSection-password";

    const INTERFACE_LANG_XPATH = "//select[@id='fid-locale_id']";
    const INTERFACE_LANG_CSS = "#fid-locale_id";

    const LOGIN_BTN_XPATH = "//span[@id='btn-send']";
    const LOGIN_BTN_CSS = "#btn-send>button";
}
