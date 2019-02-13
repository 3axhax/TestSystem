<?php
define('ROOT', dirname(__FILE__));
require_once ROOT."/components/QuestionsTables.php";
require_once ROOT."/components/Database.php";
require_once ROOT."/components/User.php";

$ajax = new Ajax();
$ajax->runAjax();

class Ajax {

    private $requests = [];

    private $questions_table;

    public function __construct() {
        $this->questions_table = new QuestionsTables();
    }

    public function runAjax() {
        $this->getRequests();
        if ($this->requests['action']) {
            switch ($this->requests['action']) {
                case 'generate_table':
                    echo $this->questions_table->generateTable($this->requests['min_dif'], $this->requests['max_dif']);
                    exit();
                    break;
                case 'emulate_test':
                    echo $this->questions_table->emulateTest($this->requests['user_int']);
                    exit();
                    break;
            }
        }
    }

    private function getRequests() {

        if (isset($_REQUEST['action']) && trim($_REQUEST['action']) != '') {
            $this->requests['action'] = (string)$_REQUEST['action'];
        }
        else {
            $this->requests['action'] = false;
        }

        if (isset($_REQUEST['min_dif']) && ctype_digit($_REQUEST['min_dif']) &&
            (int)$_REQUEST['min_dif'] >= $this->questions_table->getMinDifficult() &&
            (int)$_REQUEST['min_dif'] <= $this->questions_table->getMaxDifficult()) {
            $this->requests['min_dif'] = (int)$_REQUEST['min_dif'];
        }
        else {
            $this->requests['min_dif'] = false;
        }

        if (isset($_REQUEST['max_dif']) && ctype_digit($_REQUEST['max_dif']) &&
            (int)$_REQUEST['max_dif'] >= $this->questions_table->getMinDifficult() &&
            (int)$_REQUEST['max_dif'] <= $this->questions_table->getMaxDifficult()) {
            $this->requests['max_dif'] = (int)$_REQUEST['max_dif'];
        }
        else {
            $this->requests['max_dif'] = false;
        }

        if (isset($_REQUEST['user_int']) && ctype_digit($_REQUEST['user_int']) &&
            (int)$_REQUEST['user_int'] >= $this->questions_table->getMinDifficult() &&
            (int)$_REQUEST['user_int'] <= $this->questions_table->getMaxDifficult()) {
            $this->requests['user_int'] = (int)$_REQUEST['user_int'];
        }
        else {
            $this->requests['user_int'] = false;
        }
    }
}