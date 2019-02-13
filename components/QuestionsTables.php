<?php


class QuestionsTables {

    private $db;

    private $config;

    private $questions_table;

    public function __construct() {

        $paramsPath = ROOT . '/config/main_config.php';
        $this->config = include($paramsPath);

        $this->db = new Database();
    }

    public function getMaxDifficult() {
        return $this->config['max_difficult'];
    }

    public function getMinDifficult() {
        return $this->config['min_difficult'];
    }

    public function generateTable($min, $max) {
        if ($min !== false && $max !== false && ($min <= $max)) {

            $result = true;
            $sql_create = sprintf("create table if not exists questions (
                question_id       int unsigned auto_increment
                  primary key,
                question_difficult int not null,
                uses_count int unsigned default 0 null);");
            $result = $result && $this->db->run($sql_create);

            $sql_truncate = sprintf("TRUNCATE questions");
            $result = $result && $this->db->run($sql_truncate);

            for ($i = 1; $i <= 100; $i++) {
                $sql = sprintf("
            INSERT INTO questions (question_difficult) 
            VALUES (ROUND(%d+RAND()*%d))", $min,  $max - $min);
                $result = $result && $this->db->run($sql);
            }
            if ($result) {
                return json_encode([
                    'success' => 'Table create success',
                    'min_dif' => $min,
                    'max_dif' => $max,
                ]);
            }
            return json_encode(['error' => 'Error in generate table']);
        }
        else {
            return json_encode(['error' => 'Invalid boundary parameters']);
        }
    }

    public function emulateTest ($user_int) {
        if($user_int !== false) {
            $this->selectQuestions();

            $user = new User($user_int);
            foreach ($this->questions_table as $k => $q) {
                $this->questions_table[$k]['answer'] = $user->answerOnQuestion($q['question_difficult']);
            }
            $right_answer = $this->getResultTest();
            $this->logResult($user_int, $right_answer);
            $result = sprintf("The tested person answered correctly %d questions out of %d", $right_answer, $this->config['size_question_table']);

            return json_encode([
                'success' => 'Test emulate success',
                'table' => $this->questions_table,
                'result' => $result
            ]);
        }
        else {
            return json_encode(['error' => 'Invalid user intelligence']);
        }
    }

    private function selectQuestions () {
        $sql = sprintf("SELECT question_id, question_difficult, 
                (uses_count + ROUND(RAND()*%d)) as weight, uses_count
                FROM questions ORDER BY weight ASC LIMIT 0,%d",
            $this->config['factor_select_questions'], $this->config['size_question_table']);
        $this->questions_table = $this->db->select($sql, true);

        $selected = [];
        foreach ($this->questions_table as $q) {
            $selected[] = $q['question_id'];
        }
        $selected = implode(',' , $selected);
        $sql = sprintf("UPDATE questions SET uses_count=uses_count+1 WHERE question_id IN (%s)", $selected);
        $this->db->run($sql);
    }

    private function getResultTest() {
        $right_answer = 0;
        foreach ($this->questions_table as $q) {
            if ($q['answer'] == 1) $right_answer ++;
        }
        return $right_answer;
    }

    private function logResult($user_int, $right_answer) {
        $sql_create = sprintf("create table if not exists tests_log
                      (
                        test_id       int auto_increment,
                        user_int      int         not null,
                        questions_dif varchar(50) not null,
                        result        varchar(50) not null,
                        constraint tests_log_test_id_uindex
                          unique (test_id)
                      );
                      
                      alter table tests_log
                        add primary key (test_id);");
        $this->db->run($sql_create);

        $dif = array_column($this->questions_table, 'question_difficult');
        $min_dif = min($dif);
        $max_dif = max($dif);

        $sql_add_log = sprintf("INSERT INTO tests_log (user_int, questions_dif, result) 
            VALUES (%d, '%s', '%s')",
            $user_int,
            $min_dif . ' - ' . $max_dif,
            $right_answer . ' out of ' . $this->config['size_question_table']);
        $this->db->run($sql_add_log);
    }
}