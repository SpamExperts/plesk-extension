<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_CustomButtons extends pm_Hook_CustomButtons
{
    /**
     * Custom Buttons generator
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getButtons()
    {
        $buttons = [
            [
                'place' => [
                    self::PLACE_ADMIN_HOME,
                ],
                'title' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME)
                        ?: "Spam Experts Email Security",
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'description' => 'Spam Experts Email Security Management',
                'icon' => htmlentities(
                        pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_LOGO_URL),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?: pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index'),
            ],
            [
                'place' => [
                    self::PLACE_RESELLER_HOME,
                ],
                'title' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME)
                        ?: "Spam Experts Email Security",
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'description' => 'Spam Experts Email Security Management',
                'icon' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_LOGO_URL),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?: pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index'),
                'visibility' => function ($options) {
                    return ! Modules_SpamexpertsExtension_Form_Settings::areEmpty();
                }
            ],
            [
                'place' => [
                    self::PLACE_CUSTOMER_HOME,
                ],
                'title' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME)
                        ?: "Spam Experts Email Security",
                    ENT_QUOTES,
                    'UTF-8'
                ) . " (All domains)",
                'description' => 'Spam Experts Email Security Management',
                'icon' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_LOGO_URL),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?: pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index'),
                'visibility' => function ($options) {
                    return ! Modules_SpamexpertsExtension_Form_Settings::areEmpty();
                },
            ],
            [
                'place' => [
                    self::PLACE_DOMAIN,
                ],
                'title' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME)
                        ?: "Spam Experts Email Security",
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'description' => 'Spam Experts Email Security Management',
                'icon' => htmlentities(
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_LOGO_URL),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?: pm_Context::getBaseUrl() . 'images/seicon-32x32.png',
                'link' => pm_Context::getActionUrl('index', 'domain'),
                'visibility' => function ($options) {
                    return ! Modules_SpamexpertsExtension_Form_Settings::areEmpty();
                },
            ],
        ];

        return $buttons;
    }

}
