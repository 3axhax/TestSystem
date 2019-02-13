<?php

class User {
    private $intelligence = 0;

    private $config;

    public function __construct($intelligence) {
        $this->intelligence = (int)$intelligence;
        $paramsPath = ROOT . '/config/main_config.php';
        $this->config = include($paramsPath);
    }

    /**
     * Get answer on the question
     *
     * First check border states. Then, if user intelligence > difficult of question, error correction is made;
     * if difficult > user intelligence, guess the answer correction is made
     *
     * @param $difficult - difficult of question
     * @return int 1|0
     */

    public function answerOnQuestion ($difficult) {
        if ($this->intelligence == $this->config['min_difficult']) return 0;
        if ($difficult == $this->config['max_difficult']) return 0;
        if ($difficult == $this->config['min_difficult'] &&
            $this->intelligence != $this->config['min_difficult']) return 1;
        if ($this->intelligence >= $difficult) {
            $factor = ($this->intelligence == $difficult) ? $this->config['factor_answer_questions'] :
                $this->config['factor_answer_questions']/($this->intelligence - $difficult);
            $answer = round(mt_rand(500-$factor,1000)/1000);
            return $answer;
        }
        if ($this->intelligence < $difficult) {
            $factor = $this->config['factor_answer_questions']/($difficult - $this->intelligence);
            $rand = rand(1000 + $factor,$this->config['task_in_questions']*1000)/1000;
            $answer = (round($rand) == $this->config['task_in_questions']) ? 1 : 0;
            return $answer;
        }
        return false;
    }
}