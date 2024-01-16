<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\StaContact\Site\Helper\StaContactHelper;

$data = Factory::getApplication()->input->post->get('jform', [], 'array');

Form::addFormPath(JPATH_SITE . '/modules/mod_sta_contact/forms');

$form = Form::getInstance('mod_sta_contact.sendmessage', 'sendmessage',['control' => 'jform']);

if(isset($data['contact_sta_send_mail'])){
	$staContact = new StaContactHelper();
	$staContact->submit();
}

require ModuleHelper::getLayoutPath('mod_sta_contact');