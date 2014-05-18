<?php

class DataBase {

    private $db_host = 'localhost';
    private $db_user = 'root';
    private $db_pass = 'root';
    private $db_name = 'Weapons';
    private $con = false;
    private $result = array();

    public function getResult() {
        return $this->result0;
    }

    /*
     * Соединяемся с бд, разрешено только одно соединение
     */

    public function connect() {
        if (!$this->con) {
            $myconn = @mysql_connect($this->db_host, $this->db_user, $this->db_pass);
            mysql_query("SET character_set_client = utf8");
            mysql_query("SET character_set_connection = utf8");
            mysql_query("SET character_set_results = utf8");
            if ($myconn) {
                $seldb = mysql_select_db($this->db_name, $myconn);
//                mysql_query("set names 'utf-8',$myconn");
//                echo $this->db_host;
//                echo $this->db_user;
//                echo $this->db_pass;
//                echo $this->db_name;
//                echo $myconn;
                if ($seldb) {
                    $this->con = true;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function setDatabase($name) {
        if ($this->con) {
            if (mysql_close()) {
                $this->con = false;
                $this->results0 = null;
                $this->db_name = $name;
                $this->connect();
            }
        }
    }

    public function disconnect() {
        if ($this->con) {
            if (mysql_close()) {
                $this->con = false;
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * Выборка информации из бд
     * Требуется: table (наименование таблицы)
     * Опционально: rows (требуемые колонки, разделитель запятая)
     *           where (колонка = значение, передаем строкой)
     *           order (сортировка, передаем строкой)
     */

//    public function select($table, $rows = '*', $where = null, $order = null) {
//        $q = 'SELECT ' . $rows . ' FROM ' . $table;
//        if ($where != null) {
//            $q .= ' WHERE ' . $where;
//        }
//        if ($order != null) {
//            $q .= ' ORDER BY ' . $order;
//        }
//        if ($this->tableExists($table)) {
//            $query = mysql_query($q);
//            echo "В таблице mytable ".mysql_num_rows($query)." записей";
//
//
//            if ($query) {
//                $this->numResults = mysql_num_rows($query);
//                for ($i = 0; $i < $this->numResults; $i++) {
//                    $r = mysql_fetch_array($query);
//                    $key = array_keys($r);
//                    for ($x = 0; $x < count($key); $x++) {
//                        // Sanitizes keys so only alphavalues are allowed
//                        if (!is_int($key[$x])) {
//                            if (mysql_num_rows($query) > 1) {
//                                $this->result[$i][$key[$x]] = $r[$key[$x]];
//                            } else if (mysql_num_rows($query) < 1) {
//                                $this->result = null;
//                            } else {
//                                $this->result[$key[$x]] = $r[$key[$x]];
//                            }
//                        }
//                    }
//                }
//                return true;
//            } else {
//                return false;
//            }
//        } else {
//            return false;
//        }
//    }


    public function select($table, $rows = '*', $where = null) {
        $q = 'SELECT ' . $rows . ' FROM ' . $table;
        if ($where != null) {
            $q .= ' WHERE ' . $where;
        }
        $query = mysql_query($q);

        return $query;
    }

    /*
     * Вставляем значения в таблицу
     * Требуемые: table (наименование таблицы)
     *            values (вставляемые значения, передается массив  значений, например,
     * array(3,"Name 4","this@wasinsert.ed"); )
     * Опционально:
     *             rows (название столбцов, куда вставляем значения, передается строкой,
     *            например, 'title,meta,date'
     *
     */

    public function insert($table, $values, $rows = null) {
        if ($this->tableExists($table)) {
            $insert = 'INSERT INTO ' . $table;
            if ($rows != null) {
                $insert .= ' (' . $rows . ')';
            }
            for ($i = 0; $i < count($values); $i++) {
                if (is_string($values[$i])) {
                    $values[$i] = '"' . $values[$i] . '"';
                }
            }
            $values = implode(',', $values);
            $insert .= ' VALUES (' . $values . ')';
            $ins = mysql_query($insert);
            if ($ins) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * Удаяем таблицу или записи удовлетворяющие условию
     * Требуемые: таблица (наименование таблицы)
     * Опционально: где (условие [column =  value]), передаем строкой, например, 'id=4'
     */

    public function delete($table, $where = null) {
        echo $where;
        if ($this->tableExists($table)) {
            if ($where == null) {
                $delete = 'DELETE ' . $table;
            } else {
                $delete = 'DELETE FROM ' . $table . ' WHERE ' . $where;
            }
            $del = mysql_query($delete);
            if ($del) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function update($table, $rows, $where, $condition) {
        if ($this->tableExists($table)) {




            for ($i = 0; $i < count($where); $i++) {
                if ($i % 2 != 0) {
                    if (is_string($where[$i])) {
                        if (($i + 1) != null) {
                            $where[$i] = '"' . $where[$i] . '" AND ';
                        } else {
                            $where[$i] = '"' . $where[$i] . '"';
                        }
                    }
                }
            }
            $where = implode($condition, $where);



            $update = 'UPDATE ' . $table . ' SET ';
            $keys = array_keys($rows);
            for ($i = 0; $i < count($rows); $i++) {
                if (is_string($rows[$keys[$i]])) {
                    $update .= $keys[$i] . '="' . $rows[$keys[$i]] . '"';
                } else {
                    $update .= $keys[$i] . '=' . $rows[$keys[$i]];
                }

                // Parse to add commas
                if ($i != count($rows) - 1) {
                    $update .= ',';
                }
            }
            $update .= ' WHERE ' . $where;
            print $update;

            $query = mysql_query($update);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Проверяем наличие таблицы при выполнении запроса
     *
     */

    private function tableExists($table) {
        $tablesInDb = mysql_query('SHOW TABLES FROM ' . $this->db_name . ' LIKE "' . $table . '"');
        if ($tablesInDb) {
            if (mysql_num_rows($tablesInDb) == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

}
