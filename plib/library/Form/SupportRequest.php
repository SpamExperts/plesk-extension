<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_Form_SupportRequest extends pm_Form_Simple
{
    const OPTION_TITLE = 'title';
    const OPTION_REPLY_TO = 'reply_to';
    const OPTION_MESSAGE = 'message';

    /**
     * Modules_SpamexpertsExtension_Form_Brand constructor.
     *
     * @param array|mixed $options
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->addElement('text', self::OPTION_TITLE, [
            'label' => 'Subject',
            'description' => "Your support request subject",
            'required' => true,
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addElement('text', self::OPTION_REPLY_TO, [
            'label' => 'Reply-To',
            'description' => "An email address where a reply should be sent",
            'required' => true,
            'validators' => [
                ['EmailAddress', true],
            ],
        ]);

        $this->addElement('textarea', self::OPTION_MESSAGE, [
            'label' => 'Message',
            'description' => "Your message",
            'required' => true,
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addControlButtons([
            'cancelHidden' => true,
            'sendTitle'    => 'Send',
        ]);
    }
}
