<?php
/**
 * Created by PhpStorm.
 * User: noras
 * Date: 04.02.15
 * Time: 21:46
 */
defined('_JEXEC') or die;

/**
 * General Controller of HelloWorld component
 */
class WpController extends JControllerLegacy
{

    /**
     * display task
     *
     * @return void
     */

    public function display($cachable = false, $urlparams = false)
    {
        $host = JURI::root();
        $document = JFactory::getDocument();
        if (isset($_POST['zapisz'])) {

            $params = array();
            unset($_POST['zapisz']);
            unset($_POST['option']);

            foreach ($_POST as $k => $p) {
                $params[$k] = $p;
            }

            $db = JFactory::getDBO();
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                $query = " UPDATE #__extensions SET params='" . json_encode($params) . "' WHERE element='com_wysylka_platnosci' ";
            } else {
                $query = " UPDATE #__components SET params='" . json_encode($params) . "' WHERE `option`='com_wysylka_platnosci' ";
            }

            $db->setQuery($query);
            $wynik = $db->query($query);
            if (empty($wynik)) {
                JError::raiseWarning(100, 'Nie można zapisać powiązań do bazy danych!');
            } else {
                JError::raiseNotice(100, 'Powiązanie zostało zapisane poprawnie!');

            }
            $jap = JFactory::getApplication();
            $jap->redirect('index.php?option=com_wysylka_platnosci');
        }
        // set default view if not set
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'main'));
        $this->lang = JFactory::getLanguage();
        $this->lang = $this->lang->getLocale();
        $this->jezyk = substr(strtolower($this->lang[2]), 0, 5);
        $this->addToolbar();
        // call parent behavior
        parent::display($cachable);
    }


    protected function addToolbar()
    {
        // assuming you have other toolbar buttons ...

        JToolBarHelper::title( 'Powiązanie wysyłki z płatnościami Dla Virtuemart', 'generic.png' );

    }

    public function jezyk() {
        $lang = JFactory::getLanguage();
        $lang = $lang->getLocale();
        $jezyk = substr(strtolower($lang[2]), 0, 5);
        return $jezyk;
    }

    public function wysylki()
    {
        $db = JFactory::getDBO();

        // wysyłka
        $q1 = "SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN #__virtuemart_shipmentmethods_" . $this->jezyk() . " using(virtuemart_shipmentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_shipmentmethods_%' AND Name NOT LIKE '%_virtuemart_shipmentmethods_" . $this->jezyk() . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q1 .= ' UNION SELECT virtuemart_shipmentmethod_id AS id, shipment_name FROM #__virtuemart_shipmentmethods JOIN ' . $j->Name . ' using(virtuemart_shipmentmethod_id)   ';
        }
        $db->setQuery($q1);
        return $db->loadObjectList();
    }

    public function platnosci()
    {
        $db = JFactory::getDBO();

        // płatności
        $q2 = "SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN #__virtuemart_paymentmethods_" . $this->jezyk() . " using(virtuemart_paymentmethod_id)";
        // dodanie kolejnych tabel translacji!
        $join_sql = "SHOW TABLE STATUS WHERE Name LIKE '%_virtuemart_paymentmethods_%' AND Name NOT LIKE '%_virtuemart_paymentmethods_" . $this->jezyk() . "%'";
        $db->setQuery($join_sql);
        $joiny = $db->loadObjectList();
        foreach ($joiny as $j) {
            $q2 .= ' UNION SELECT virtuemart_paymentmethod_id AS id, payment_name FROM #__virtuemart_paymentmethods JOIN ' . $j->Name . ' using(virtuemart_paymentmethod_id)   ';
        }
        $db->setQuery($q2);
        return $db->loadObjectList();
    }

}