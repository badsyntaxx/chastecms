<?php 

/**
 * Application Model
 *
 * The model class is the main model of the application system.
 * All model classes will be extensions of this class.
 */
abstract class Model
{
    public $con;

    public function __construct()
    {
        $this->con = Database::getInstance();
    }

    public function table($table)
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    public function insert($data)
    {
        $placeholders = [];
        $values = [];
        $columns = [];
        $query = $this->con->mysqli->query('SELECT * FROM ' . $this->table);
        $fields = $query->fetch_fields();

        foreach ($fields as $field) {
            array_push($columns, $field->name);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($data[$key]);
                unset($data[$key]);
            }
        }
        
        foreach ($data as $key => $value) {
            $new_val = '`' . $key .  '`';
            array_push($values, $new_val);
            array_push($placeholders, '?');
        }

        $values = implode(', ', $values);
        $placeholders = implode(', ', $placeholders);
        $this->post_data = $data;
        $this->query = 'INSERT INTO ' . $this->table . ' (' . $values . ') VALUES (' . $placeholders . ')';

        return $this;
    }

    public function update($data)
    {
        $values = [];
        $columns = [];
        $query = $this->con->mysqli->query('SELECT * FROM ' . $this->table);
        $fields = $query->fetch_fields();

        $this->post_data = $data;

        // Create an array of column names from a table.
        foreach ($fields as $field) {
            array_push($columns, $field->name);
        }

        // Check if the same keys exist in the data and columns arrays. Unset the data keys that dont exist in the column keys.
        foreach ($data as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($data[$key]);
                unset($data[$key]);
            }
        }
        
        foreach ($data as $key => $value) {
            $new_val = '`' . $key .  '`';
            array_push($values, $new_val);
        }

        $values = implode(' = ?, ', $values);

        $this->query = 'UPDATE `' . $this->table . '` SET ' . $values . ' = ?';

        return $this;
    }

    public function execute()
    {
        if (isset($this->where)) {
            $where1 = ', `' . $this->where . '` = ?';
            $where2 = 'SET `' . $this->where . '` = ?,';
            if (strpos($this->query, $where1)) {
                $this->query = str_replace($where1, '', $this->query);
            }
            if (strpos($this->query, $where2)) {
                $this->query = str_replace($where2, 'SET', $this->query);
            }
        }
        
        if (isset($this->post_data)) {
            $data = $this->post_data;
            foreach ($data as $key => $value) {
                $types[] = 's';
                if (isset($this->where)) {
                    if ($key == $this->where) {
                        unset($data[$key]);
                        $data[$key] = $value;
                    }
                }
            }

            $query = $this->con->mysqli->prepare($this->query);
            $query->bind_param(implode($types), ...array_values($data));
            if ($query->execute()) {
                $output = ['status' => 'success', 'response' => $query->error, 'affected_rows' => $query->affected_rows];
                return $output;
            } else {
                $this->debug($query->error);
            }
        } else {
            $query = $this->con->mysqli->query($this->query);
            if ($query) {
                $output = ['status' => 'success', 'response' => ''];
                return $output;
            } else {
                return $this->debug($query->error);
            }
        }  
    }

    public function select($select)
    {
        $selects = ['*'];
        if (strpos($select, ',')) {
            $choices = explode(', ', $select);
        } else {
            $choices = [$select];
        }
        if ($select != '*') {
            $selects = [];
            if ($choices) {
                foreach ($choices as $a) {
                    $new_val = '`' . $a .  '`';
                    array_push($selects, $new_val);
                }
            }
        }
        $selects = implode(', ', $selects);
        $this->query = 'SELECT ' . $selects . ' FROM `' . $this->table . '`';
        return $this;
    }

    public function leftJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' LEFT JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    public function rightJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' RIGHT JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    public function innerJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' INNER JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    public function fullJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' FULL JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    public function get() 
    {
        $query = $this->con->mysqli->query($this->query);
        if ($query) {
            $output = ['status' => 'success', 'response' => $query->fetch_assoc()];
            return $output;
        } else {
            return $this->debug($this->con->mysqli->error);
        }
    }

    public function getOne()
    {
        $query = $this->con->mysqli->query($this->query);
        if ($query) {
            $row = $query->fetch_row();
            $output = ['status' => 'success', 'response' => $row[0]];
            return $output;
        } else {
            return $this->debug($this->con->mysqli->error);
        }
    }

    public function getAll()
    {
        $array = [];
        $query = $this->con->mysqli->query($this->query);
        if ($query) {
            while ($col = $query->fetch_assoc()) {
                $array[] = $col;
            }
            $output = ['status' => 'success', 'response' => $array];
            return $output;
        } else {
            return $this->debug($this->con->mysqli->error);
        }
    }

    public function getTotal()
    {
        $query = $this->con->mysqli->query($this->query);
        if ($query) {
            $output = ['status' => 'success', 'response' => $query->num_rows];
            return $output;
        } else {
            return $this->debug($this->con->mysqli->error);
        }
    }

    public function where($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' WHERE `' . $where . '` = ?';
        } else {
            $this->query = $this->query . ' WHERE `' . $where . '` = "' . $equals . '"';
        }

        $this->where = $where;
        
        return $this;
    }

    public function andWhere($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' AND `' . $where . '` = ?';
        } else {
            $this->query = $this->query . ' AND `' . $where . '` = "' . $equals . '"';
        }
        
        return $this;
    }

    public function orWhere($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' OR `' . $where . '` = ?';
        } else {
            $this->query = $this->query . ' OR `' . $where . '` = "' . $equals . '"';
        }
        
        return $this;
    }

    public function whereNot($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' WHERE `' . $where . '` != ?';
        } else {
            $this->query = $this->query . ' WHERE `' . $where . '` != "' . $equals . '"';
        }
        
        return $this;
    }

    public function andWhereNot($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' AND `' . $where . '` != ?';
        } else {
            $this->query = $this->query . ' AND `' . $where . '` != "' . $equals . '"';
        }
        
        return $this;
    }

    public function whereLike($where, $like)
    {
        $this->query = $this->query . '  WHERE `' . $where . '` LIKE "%'. $like . '%"';
        return $this;
    }

    public function orWhereLike($where, $like)
    {
        $this->query = $this->query . ' OR `' . $where . '` LIKE "%'. $like . '%"';
        return $this;
    }

    public function whereHighest($record)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` = (SELECT max(' . $record . ') FROM `' . $this->table . '`)';
        return $this;
    }

    public function whereGreater($record, $param)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` > ' . $param;
        return $this;
    }

    public function whereLesser($record, $param)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` < ' . $param;
        return $this;
    }

    public function orderBy($orderby, $direction)
    {
        $this->query = $this->query . ' ORDER BY `' . $orderby . '` ' . strtoupper($direction);
        return $this;
    }

    public function limit($limit)
    {
        $this->query = $this->query . ' LIMIT ' . $limit;
        return $this;
    }

    public function limitBetween($start, $end)
    {
        $this->query = $this->query . ' LIMIT ' . $start . ', ' . $end;
        return $this;
    }

    public function delete()
    {
        $this->query = 'DELETE FROM `' . $this->table . '`';
        return $this;
    }

    public function truncate($log)
    {
        $this->query = 'TRUNCATE TABLE `' . $log . '`';
        return $this->con->mysqli->query($this->query);
    }

    public function testExecute()
    {
        if (isset($this->where)) {
            $where1 = ', `' . $this->where . '` = ?';
            $where2 = 'SET `' . $this->where . '` = ?,';
            if (strpos($this->query, $where1)) {
                $this->query = str_replace($where1, '', $this->query);
            }
            if (strpos($this->query, $where2)) {
                $this->query = str_replace($where2, 'SET', $this->query);
            }
        }
        
        $data = isset($this->post_data) ? $this->post_data : [];
        foreach ($data as $key => $value) {
            $types[] = 's';
            if ($key == $this->where) {
                unset($data[$key]);
                $data[$key] = $value;
            }
        }

        echo $this->query . '<br>';

        if (isset($data)) {
            var_dump($data);
        }
        
        if (isset($types)) {
            var_dump($types);
        }
    }

    public function testSelect()
    {
        var_dump($this->query);
    }

    public function reset()
    {
        $this->post_data = null;
        $this->where = null;
        $this->query = null;
    }

    public function log($data)
    {
        // INSERT INTO `errors` (`event`) VALUES (?)
        $insert = $this->table('errors')->insert($data = ['event' => $data])->execute();
    }

    public function debug($sql_error)
    {
        $backtrace = debug_backtrace();
        $data = $backtrace[2];

        $data['file'] = explode('\\', $data['file']);
        $data['file'] = end($data['file']);

        $controller = str_replace('.php', '', $data['file']);
        $line = $data['line'];
        $class = $data['class'];
        $function = $data['function'];
        $error = $sql_error;

        $string = 'Error: ' . $class . '->' . $function . ' - ' . $error . '. - Called by ' . $controller . ' on line ' . $line . '.';
        
        $this->log($string);

        $output = ['status' => 'error', 'response' => $error];

        return $output;
    }
}