<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_sta_contact
 *
 */

namespace Joomla\Module\StaContact\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_sta_contact
 *
 * @since  1.5
 */
class StaContactHelper
{
	public function submit()
    {
        // Check for request forgeries.
        Factory::getApplication()->checkToken();
        $app    = Factory::getApplication();
       
        // Get the data from POST
        $data = $app->input->post->get('jform', [], 'array');
        $msg = '';
        $sent = false;

        $sent = $this->_sendEmail($data);

        // Set the success message if it was a success
        if ($sent) {
            $msg = Text::_('MOD_STA_CONTACT_EMAIL_THANKS');
        }
		$app->enqueueMessage($msg);
		
        return true;
    }
	
	private function _sendEmail($data)
    {
        $app = Factory::getApplication();

        $templateData = [
            'sitename'     => $app->get('sitename'),
            'name'         => $data['contact_name'],
            'contactname'  => 'Contact me form',
            'email'        => PunycodeHelper::emailToPunycode($data['contact_email']),
            'subject'      => $data['contact_subject'],
            'body'         => stripslashes($data['contact_message']),
            'url'          => Uri::base(),
            'customfields' => '',
        ];

        try {
            $mailer = new MailTemplate('com_contact.mail', $app->getLanguage()->getTag());
            $mailer->addRecipient($app->get('mailfrom'));
            $mailer->setReplyTo($templateData['email'], $templateData['name']);
            $mailer->addTemplateData($templateData);
            $sent = $mailer->send();

		} catch (MailDisabledException | phpMailerException $exception) {
            try {
                Log::add(Text::_($exception->getMessage()), Log::WARNING, 'jerror');

                $sent = false;
            } catch (\RuntimeException $exception) {
                $app->enqueueMessage(Text::_($exception->errorMessage()), 'warning');

                $sent = false;
            }
        }

        return $sent;
    }

}
