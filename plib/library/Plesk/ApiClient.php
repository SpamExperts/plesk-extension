<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_Plesk_ApiClient
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;

    static final public function getVersion()
    {
        $api = new self;

        /** @noinspection PhpUndefinedFieldInspection */
        return (string) $api->xmlapi('<server><get><gen_info/></get></server>')['version'];
    }

}
