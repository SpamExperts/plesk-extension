<?php

class Modules_SpamexpertsExtension_Form_Brand extends pm_Form_Simple
{
    const OPTION_BRAND_NAME = 'brand.name';
    const OPTION_LOGO_URL = 'brand.logo_url';

    public function __construct($options)
    {
        parent::__construct($options);

        $this->addElement('text', self::OPTION_BRAND_NAME, [
            'label' => 'Extension name',
            'value' => pm_Settings::get(self::OPTION_BRAND_NAME),
            'description' => "This will be shown as a title on all extension pages.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addElement('text', self::OPTION_LOGO_URL, [
            'label' => 'Logo URL',
            'value' => pm_Settings::get(self::OPTION_LOGO_URL),
            'description' => "This must be a valid URL poiniting to a PNG, GIF or JPEG image having dimensions 32x32px.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addControlButtons([
            'cancelHidden' => true,
            'sendTitle'    => 'Save',
        ]);
    }
}
