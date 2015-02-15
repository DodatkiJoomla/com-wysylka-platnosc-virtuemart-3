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

        $model = $this->getModel('main');
        $host = JURI::root();
        $document = JFactory::getDocument();
        if (isset($_POST['zapisz'])) {

            $params = array();
            unset($_POST['zapisz']);
            unset($_POST['option']);

            foreach ($_POST as $k => $p) {
                $params[$k] = $p;
            }

            $model->saveData($params);
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

    /*
     * Toolbar tittle.
     */
    protected function addToolbar()
    {
        // assuming you have other toolbar buttons ...

        JToolBarHelper::title('Powiązanie wysyłki z płatnościami Dla Virtuemart', 'generic.png');

    }

    /*
     * Obsługa języka.
     */
    public function jezyk()
    {
        $lang = JFactory::getLanguage();
        $lang = $lang->getLocale();
        $jezyk = substr(strtolower($lang[2]), 0, 5);
        return $jezyk;
    }

    /*
     * Wypisanie wsyztskich metod wysyłki
     * $param $i, $wysylka and $params
     * $return - view html
     */
    public function getSelectSending($i, $wysylki, $params)
    {

        $view = "<select style='margin: 10px 0;' name='shipment_name" . $i . "'>";
        $view .= "<option value='0'>wybierz</option>";
        foreach ($wysylki as $wysylka) {
            $selected = "";
            $shipment = $params->get("shipment_name" . $i);
            if (!empty($shipment) && $shipment == $wysylka->id) {
                $selected = "selected='selected'";
            }
            $view .= "<option " . $selected . " value='" . $wysylka->id . "'>" . $wysylka->shipment_name . "</option>";
        }
        $view .= "</select>";
        return $view;
    }

    /*
        * Wypisanie wsyztskich metod płatności
        * $param $i, $platnosci and $params
        * $return - view html
        */
    public function getSelectPayment($i, $platnosci, $params)
    {

        $view = "<select multiple='multiple' style='margin: 10px 0;' name='payment_name" . $i . "[]'>";
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


            $view .= "<option " . $selected . " value='" . $platnosc->id . "'>" . $platnosc->payment_name . "</option>";
        }
        $view .= "</select>";

        return $view;
    }

}