<?php
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$document  = Factory::getDocument();
$app->getDocument()->getWebAssetManager()
    ->useScript('core')
    ->useScript('keepalive')
    ->useScript('form.validate');
	
// Get the message queue
$messages = Factory::getApplication()->getMessageQueue();
$list_messages = [];
// Build the sorted message list
if (\is_array($messages) && !empty($messages)) {
	foreach ($messages as $msg) {
		if (isset($msg['type']) && isset($msg['message'])) {
			$list_messages[$msg['type']][] = $msg['message'];
		}
	}
}
?>
<form id="sta-contact-form" class="mod-sta-contact needs-validation" novalidate action="<?php echo Route::_('index.php#contact'); ?>" method="post">
	<h1 style="color:#013F4E" class="h3 mb-3 fw-normal">Contact me</h1>
	<?php if (is_array($list_messages) && !empty($list_messages)) {
		echo $list_messages["info"][0];
	}else{ ?>
	<p><?=Text::_('MOD_STA_CONTACT_ADD_MESSAGE_TEXT')?></p>
	
	<?php foreach ($form->getFieldsets() as $fieldset) : ?>
            <?php if ($fieldset->name === 'captcha') : ?>
                <?php continue; ?>
            <?php endif; ?>
            <?php $fields = $form->getFieldset($fieldset->name); ?>
            <?php if (count($fields)) : ?>
                <fieldset class="m-0 text-start">
                    <?php foreach ($fields as $field) : ?>
						<div class="control-group form-floating">
						<?php echo $field->input; ?>
						<?php echo $field->label; ?>
						</div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        <?php endforeach; ?>
		
    <?php echo $form->renderFieldset('captcha'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
	<button class="btn btn-primary w-100 py-2" id="contact-send" type="button"><?= Text::_('MOD_STA_CONTACT_SEND'); ?></button>
</form>
<?php 
Factory::getDocument()->addScriptDeclaration('
	window.onload = function(){

  document.getElementById("contact-send").addEventListener("click",function (){
	var form = document.getElementById("sta-contact-form");
	if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
		  form.classList.add("was-validated");
        }else{
			document.getElementById("sta-contact-form").submit();
		}
   });
};');
}?>