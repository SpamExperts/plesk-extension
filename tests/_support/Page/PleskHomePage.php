<?php
namespace Page;

class PleskHomePage
{
    const LEFT_SIDE_MENU = "//div[@class='page-sidebar-menu-wrapper']";
    const PAGE_HEADER = "//div[@class='page-header']";
    const PAGE_CONTENT = "//div[contains(concat(' ', normalize-space(@class), ' '), ' pul-layout__content ')]";

    /*"Welcome to Plesk!" survey dialog shown after login*/
    const SURVEY_CANCEL_BTN = "//div[@data-cy='survey-iframe']//button[contains(., 'Cancel')]";

    const ACCOUNT_MENU_XPATH = "//div[@id='account-menu']";
    const ACCOUNT_MENU_CSS = ".btn.dropdown-toggle";

    const LOGOUT_BTN = ".//*[@id='account-menu-content-area']/li[2]/a";
}
