<?php
/**
 * @copyright Copyright (c) 2014 DodatkiJoomla.pl
 * @license GNU/GPL v2
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');
jimport('joomla.registry.registry');
jimport( 'joomla.pl' );

if (isset($_POST['zapisz'])) {

    $params = array();
    unset($_POST['zapisz']);
    unset($_POST['option']);

    foreach ($_POST as $k => $p) {
        $params[$k] = $p;
    }

    $db = JFactory::getDBO();
    if (version_compare(JVERSION, '1.6.0', 'ge')) {
        $query = " UPDATE #__extensions SET params='" . json_encode($params) . "' WHERE element='com_wysylka_platnosc_vm3' ";
    } else {
        $query = " UPDATE #__components SET params='" . json_encode($params) . "' WHERE `option`='com_wysylka_platnosc_vm3' ";
    }

    $db->setQuery($query);
    $wynik = $db->query($query);
    if (empty($wynik)) {
        JError::raiseWarning(100, 'Nie można zapisać powiązań do bazy danych!');
    }
    $jap = JFactory::getApplication();
    $jap->redirect('index.php?option=com_wysylka_platnosc_vm3');
}

$host = JURI::root();
$document = JFactory::getDocument();

// dodaj tipsy
JHTML::_('behavior.tooltip');

// menu & toolbar
JToolbarHelper::title('Powiązanie metod wysyłki z metodami płatności w Virtuemart 2');


$db = JFactory::getDBO();

    $query = 'SELECT extension_id FROM #__extensions WHERE element="com_wysylka_platnosc_vm3" AND enabled=1 ';


$db->setQuery($query);
$plgWlaczony = $db->loadResult();

if (empty($plgWlaczony)) {
    JError::raiseNotice(100, 'Plugin dołączony do komponentu, powinien zostać zainstalowany i opublikowany. W innym wypadku powiązanie wysyłek i płatności nie będzie działać!');
}

?>
<h3>Poniżej powiążesz metody wysyłek z metodami płatności:</h3>
<form action='index.php' method='POST'>
    <table border=0>
        <?php
        // język zaplecza

        $lang = JFactory::getLanguage();
        $lang = $lang->getLocale();
        $jezyk = substr(strtolower($lang[2]), 0, 5);

        // wysyłka
        $q1 = "SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN #__virtuemart_shipmentmethods_" . $jezyk . " using(virtuemart_shipmentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_shipmentmethods_%' AND Name NOT LIKE '%_virtuemart_shipmentmethods_" . $jezyk . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q1 .= ' UNION SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN ' . $j->Name . ' using(virtuemart_shipmentmethod_id)   ';
        }
        $db->setQuery($q1);
        $wysylki = $db->loadObjectList();

        // płatności
        $q2 = "SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN #__virtuemart_paymentmethods_" . $jezyk . " using(virtuemart_paymentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_paymentmethods_%' AND Name NOT LIKE '%_virtuemart_paymentmethods_" . $jezyk . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q2 .= ' UNION SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN ' . $j->Name . ' using(virtuemart_paymentmethod_id)   ';
        }
        $db->setQuery($q2);
        $platnosci = $db->loadObjectList();

        // parametry
        $params = JComponentHelper::getParams('com_wysylka_platnosc_vm3');
        for ($i = 1; $i <= 50; ++$i) {

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
    <input type='hidden' name='option' value='com_wysylka_platnosc_vm3'>
</form>


<div style='text-align: center;'>
    <br> <br>Stworzone przez:<br>
    <a target="_blank" href="http://dodatkijoomla.pl/index.php?wp_vm2">
        <img border="0" src="http://dodatkijoomla.pl/images/logo_podpis_site_mini.png">
    </a>

    <p> Szukaj najlepszych rozszerzeń dla Joomla na <a target="_blank" href="http://dodatkijoomla.pl/index.php?wp_vm2">DodatkiJoomla.pl </a>
    </p>
</div>