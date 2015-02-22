<?php
/**
 * Created by PhpStorm.
 * User: noras
 * Date: 04.02.15
 * Time: 21:50
 */
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

$controller = JControllerLegacy::getInstance('Wp');
$model = $this->getModel();

$host = JURI::root();
$document = JFactory::getDocument();
$db = JFactory::getDBO();

$query = 'SELECT extension_id FROM #__extensions WHERE element="wysylka_platnosc_vm3" AND enabled=1 ';


$db->setQuery($query);
$plgWlaczony = $db->loadResult();
if ($plgWlaczony < 1) {
    JError::raiseNotice(100, JText::_('COM_WYSYLKA_PLATNOSCI_PLUGIN_WARNING'));
}
?>
<h3><?php echo JText::_('COM_WYSYLKA_PLATNOSCI_HEADER_INFO'); ?></h3>
<form method='POST'>
    <table border=0>
        <?php

        $wysylki = $model->wysylki();

        $platnosci = $model->platnosci();

        // parametry
        $params = JComponentHelper::getParams('com_wysylka_platnosci');
        for ($i = 1; $i <= 50; ++$i) {

            echo "<tr>";
            echo "<td colspan=2>".JText::_('COM_WYSYLKA_PLATNOSCI_CREATE')."<br></td>";
            echo "</tr>";
            echo "<tr><td>";

            echo $controller->getSelectSending($i, $wysylki, $params);

            echo "</td>";

            echo "<td><br>";
            echo $controller->getSelectPayment($i, $platnosci, $params);

            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <p>
        <?php echo JText::_('COM_WYSYLKA_PLATNOSCI_INFO_BOTTOM'); ?>
    </p>
    <input type='submit' name='zapisz' value='<?php echo JText::_('COM_WYSYLKA_PLATNOSCI_SAVE_SETTING'); ?>'>
    <input type='hidden' name='option' value='com_wysylka_platnosci'>
</form>

