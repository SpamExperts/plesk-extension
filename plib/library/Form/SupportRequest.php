<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_Form_SupportRequest extends pm_Form_Simple
{
    public const OPTION_TITLE = 'title';
    public const OPTION_REPLY_TO = 'reply_to';
    public const OPTION_MESSAGE = 'message';

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
            'style' => 'width: 100%; max-width: 450px;',
        ]);

        $this->addElement('text', self::OPTION_REPLY_TO, [
            'label' => 'Reply-To',
            'description' => "An email address where a reply should be sent",
            'required' => true,
            'value' => pm_Session::getClient()->getProperty('email'),
            'validators' => [
                ['EmailAddress', true],
            ],
            'style' => 'width: 100%; max-width: 450px;',
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
