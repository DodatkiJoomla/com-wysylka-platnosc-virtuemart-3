<?php
/**
 * Created by PhpStorm.
 * User: noras
 * Date: 15.02.15
 * Time: 15:58
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');


class WpModelMain extends JModelItem
{
    /*
     * Zapis parametrów do bazy
     * $params = $params
     * $return Success or Error message
     */

    public function saveData($params)
    {
        $db = JFactory::getDBO();
        if (version_compare(JVERSION, '1.6.0', 'ge')) {
            $query = " UPDATE #__extensions SET params='" . json_encode($params) . "' WHERE element='com_wysylka_platnosci' ";
        } else {
            $query = " UPDATE #__components SET params='" . json_encode($params) . "' WHERE `option`='com_wysylka_platnosci' ";
        }

        $db->setQuery($query);
        $wynik = $db->query($query);
        if (empty($wynik)) {
            JError::raiseWarning(100, 'COM_WYSYLKA_PLATNOSCI_ERROR_SAVE');
        } else {
            JError::raiseNotice(100, 'COM_WYSYLKA_PLATNOSCI_SUCCESS_SAVE');

        }
    }

    /*
     * Wyświetlanie wysyłek VM3
     */
    public function wysylki()
    {
        $controller = JControllerLegacy::getInstance('Wp');

        $db = JFactory::getDBO();

        // wysyłka
        $q1 = "SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN #__virtuemart_shipmentmethods_" . $controller->jezyk() . " using(virtuemart_shipmentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_shipmentmethods_%' AND Name NOT LIKE '%_virtuemart_shipmentmethods_" . $controller->jezyk() . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q1 .= ' UNION SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN ' . $j->Name . ' using(virtuemart_shipmentmethod_id)   ';
        }
        $db->setQuery($q1);
        return $db->loadObjectList();
    }

    /*
     * Wyświetlanie płatności VM3
     */
    public function platnosci()
    {
        $controller = JControllerLegacy::getInstance('Wp');

        $db = JFactory::getDBO();

        // płatności
        $q2 = "SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN #__virtuemart_paymentmethods_" . $controller->jezyk() . " using(virtuemart_paymentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_paymentmethods_%' AND Name NOT LIKE '%_virtuemart_paymentmethods_" . $controller->jezyk() . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q2 .= ' UNION SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN ' . $j->Name . ' using(virtuemart_paymentmethod_id)   ';
        }
        $db->setQuery($q2);
        return $db->loadObjectList();
    }


}