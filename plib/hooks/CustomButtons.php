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
                    self::PLACE_DOMAIN,
                ],
                'title' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME)
                        ?: "Professional SpamFilter",
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'description' => 'Professional SpamFilter Management',
                'icon' => htmlentities(
                        pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_LOGO_URL),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?: pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index'),
            ],
        ];

        return $buttons;
    }

}
