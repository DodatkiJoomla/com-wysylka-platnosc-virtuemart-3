<?php
/**
 * Created by PhpStorm.
 * User: noras
 * Date: 04.02.15
 * Time: 21:38
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');


// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Wp');

// Perform the Request task and Execute request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();