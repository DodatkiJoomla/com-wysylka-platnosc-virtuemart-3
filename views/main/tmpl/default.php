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
$host = JURI::root();
$document = JFactory::getDocument();
$db = JFactory::getDBO();

$query = 'SELECT extension_id FROM #__extensions WHERE element="wysylka_platnosc_vm3" AND enabled=1 ';


$db->setQuery($query);
$plgWlaczony = $db->loadResult();
if ($plgWlaczony < 1) {
    JError::raiseNotice(100, 'Plugin dołączony do komponentu, powinien zostać zainstalowany i opublikowany. W innym wypadku powiązanie wysyłek i płatności nie będzie działać!');
}
?>
<h3>Poniżej powiążesz metody wysyłek z metodami płatności:</h3>
<form method='POST'>
    <table border=0>
        <?php

        $wysylki = $controller->wysylki();

        $platnosci = $controller->platnosci();

        // parametry
        $params = JComponentHelper::getParams('com_wysylka_platnosci');
        for ($i = 1; $i <= 3; ++$i) {

            echo "<tr>";
            echo "<td colspan=2>Utwórz " . $i . " powiązanie wysyłki z płatnościami:<br></td>";
            echo "</tr>";
            echo "<tr><td>";
            echo "<select style='margin: 10px 0;' name='shipment_name" . $i . "'>";
            echo "<option value='0'>wybierz</option>";
            foreach ($wysylki as $wysylka) {
                $selected = "";
                $shipment = $params->get("shipment_name" . $i);
                if (!empty($shipment) && $shipment == $wysylka->id) {
                    $selected = "selected='selected'";
                }
                echo "<option " . $selected . " value='" . $wysylka->id . "'>" . $wysylka->shipment_name . "</option>";
            }
            echo "</select>";

            echo "</td>";

            echo "<td><br>";
            echo "<select multiple='multiple' style='margin: 10px 0;' name='payment_name" . $i . "[]'>";
            foreach ($platnosci as $platnosc) {
                $selected = "";
                if (version_compare(JVERSION, '1.6.0', '<')) {
                    if (is_array($params->get("payment_name" . $i)) && in_array($platnosc->id, $params->get("payment_name" . $i))) {
                        $selected = "selected='selected'";
                    } else if (!is_array($params->get("payment_name" . $i)) && $platnosc->id == $params->get("payment_name" . $i)) {
                        $selected = "selected='selected'";
                    }
                } else {
                    $param_array = $params->get("payment_name" . $i);
                    if (!empty($param_array)) {
                        if (in_array($platnosc->id, $param_array)) {
                            $selected = "selected='selected'";
                        }
                    }
                }


                echo "<option " . $selected . " value='" . $platnosc->id . "'>" . $platnosc->payment_name . "</option>";
            }
            echo "</select>";

            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <p>
        CTRL + lewy przycisk myszy - aby wybrać więcej niż 1 płatność, lub usunąć instniejące.
    </p>
    <input type='submit' name='zapisz' value='Zapisz ustawienia'>
    <input type='hidden' name='option' value='com_wysylka_platnosci'>
</form>

