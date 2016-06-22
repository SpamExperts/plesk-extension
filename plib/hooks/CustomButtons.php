<?php

class Modules_SpamexpertsExtension_CustomButtons extends pm_Hook_CustomButtons
{
    /**
     * Custom Buttons generator
     *
     * @return array
     */
    public function getButtons()
    {
        $buttons = [
            [
                'place' => [
                    self::PLACE_ADMIN_HOME,
                    self::PLACE_RESELLER_HOME,
                    self::PLACE_CUSTOMER_HOME,
                ],
                'title' => 'Professional SpamFilter',
                'description' => 'Professional SpamFilter Management',
                'icon' => pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index'),
            ],
        ];

        return $buttons;
    }

}
