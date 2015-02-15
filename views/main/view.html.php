<?php
/**
 * Created by PhpStorm.
 * User: noras
 * Date: 04.02.15
 * Time: 21:48
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');


class WpViewMain extends JViewLegacy
{

    public function display($tpl = null)
    {

        if (isset($_POST['zapisz'])) {

            $params = array();
            unset($_POST['zapisz']);
            unset($_POST['option']);

            foreach ($_POST as $k => $p) {
                $params[$k] = $p;
            }

            $db = JFactory::getDBO();
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                $query = " UPDATE #__extensions SET params='" . json_encode($params) . "' WHERE element='com_wysylka_platnosc' ";
            } else {
                $query = " UPDATE #__components SET params='" . json_encode($params) . "' WHERE `option`='com_wysylka_platnosc' ";
            }

            $db->setQuery($query);
            $wynik = $db->query($query);
            if (empty($wynik)) {
                JError::raiseWarning(100, 'Nie można zapisać powiązań do bazy danych!');
            }
            $jap = JFactory::getApplication();
            $jap->redirect('index.php?option=com_wysylka_platnosc');
        }

        parent::display($tpl);
    }
}