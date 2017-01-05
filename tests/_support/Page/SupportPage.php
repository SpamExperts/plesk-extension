<?php

namespace Page;

class SupportPage
{
    const PLESK_INFORMATION = ".//*[@id='main']/table[1]/tbody/tr[1]";
    const PHP_VERSION_INFORMATION = ".//*[@id='main']/table[1]/tbody/tr[2]";
    const EXTENSION_VERSION_INFORMATION = ".//*[@id='main']/table[1]/tbody/tr[3]";

    const PLESK_DIAGNOSTICS = ".//*[@id='main']/table[2]/tbody/tr[1]";
    const PHP_VERSION_DIAGNOSTICS = ".//*[@id='main']/table[2]/tbody/tr[2]";
    const EXTENSION_VERSION_DIAGNOSTICS = ".//*[@id='main']/table[2]/tbody/tr[3]";

    /*Request support locaters*/
    const SUPPORT_SUBJECT_XPATH = "//input[@id='title']";
    const SUPPORT_SUBJECT_CSS = "#title";

    const SUPPORT_REPLY_TO_XPATH = "//input[@id='reply_to']";
    const SUPPORT_REPLY_TO_CSS = "#reply_to";

    const SUPPORT_MESSAGE_XPATH = "//textarea[@id='message']";
    const SUPPORT_MESSAGE_CSS = "#message";

    /*Send request button*/
    const SEND_BTN_XPATH = "//*[@id='btn-send']/button";
    const SEND_BTN_CSS = "#btn-send>button";
}
