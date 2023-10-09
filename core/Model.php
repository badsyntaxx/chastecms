<?php 

/**
 * Model Core Class
 *
 * The model class is the main model of the application system.
 * All model classes will be extensions of this class.
 */
class Model
{
    /**
     * Reference to database object
     *
     * @var object
     */
    private $con;

    /**
     * Table to be used in the query
     *
     * @var string
     */
    private $table;

    /**
     * The query string
     *
     * @var string
     */
    private $query;

    /**
     * Where clause in the query string
     *
     * @var string
     */
    private $where;

    /**
     * Post data to be inserted or updated
     *
     * @var mixed
     */
    private $post_data;

    /**
     * Limit of records to get
     *
     * @var int
     */
    private $limit;


    /**
     * Model construct
     * 
     * @return void
     */
    public function __construct()
    {
        $this->con = Database::getInstance();
    }

    /**
     * Table method
     * 
     * Begins building the query string by creating a table property.
     * Also calls the reset method which sets all class properties back 
     * to null so we can start a fresh query.
     *
     * @param string - $table - Table to be used.
     * @return object
     */
    public function table($table)
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    /**
     * Select method
     * 
     * Begins building the select portion of the query string.
     * Multiple columns can be select or you can use "*" everything.
     *
     * @param string - $select - What to select.
     * @return object
     */
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

    /**
     * Count records
     * 
     * Begins a count query string. Once one of the "get" methods 
     * are used with this query string. You can get the record count
     * from a table. Faster than doing a select and counting afterwards.
     *
     * @return object
     */
    public function count()
    {
        $this->query = 'SELECT COUNT(1) FROM `' . $this->table . '`';
        return $this;
    }

    /**
     * Where clause
     *
     * Begin building the where portion of the query. Also 
     * create a where property to be used later.
     * 
     * @param string - $where
     * @param mixed - $equals - Probably string or int
     * @return object
     */
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

    /**
     * And where clause
     * 
     * Continue building the where portion of the query.
     *
     * @param string - $where
     * @param mixed - $equals - Probably string or int
     * @return object
     */
    public function andWhere($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' AND `' . $where . '` = ?';
        } else {
            $this->query = $this->query . ' AND `' . $where . '` = "' . $equals . '"';
        }
        
        return $this;
    }

    /**
     * Or where clause
     * 
     * Continue building the where portion of the query.
     *
     * @param string - $where
     * @param mixed - $equals - Probably string or int
     * @return object
     */
    public function orWhere($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' OR `' . $where . '` = ?';
        } else {
            $this->query = $this->query . ' OR `' . $where . '` = "' . $equals . '"';
        }
        
        return $this;
    }

    /**
     * Where not clause
     *
     * Begin building the where portion of the query.
     * 
     * @param string - $where
     * @param mixed - $equals - Probably string or int
     * @return object
     */
    public function whereNot($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' WHERE `' . $where . '` != ?';
        } else {
            $this->query = $this->query . ' WHERE `' . $where . '` != "' . $equals . '"';
        }
        
        return $this;
    }

    /**
     * And where not clause
     * 
     * Continue building the where portion of the query.
     *
     * @param string - $where
     * @param mixed - $equals - Probably string or int
     * @return object
     */
    public function andWhereNot($where, $equals = null)
    {
        if (!isset($equals)) {
            $this->query = $this->query . ' AND `' . $where . '` != ?';
        } else {
            $this->query = $this->query . ' AND `' . $where . '` != "' . $equals . '"';
        }
        
        return $this;
    }

    /**
     * Where like clause
     *
     * Begin building the where portion of the query.
     * 
     * @param string - $where
     * @param mixed - $like - Probably string or int
     * @return object
     */
    public function whereLike($where, $like)
    {
        $this->query = $this->query . '  WHERE `' . $where . '` LIKE "%'. $like . '%"';
        return $this;
    }

    /**
     * Or where like clause
     *
     * Continue building the where portion of the query.
     * 
     * @param string - $where
     * @param mixed - $like - Probably string or int
     * @return object
     */
    public function orWhereLike($where, $like)
    {
        $this->query = $this->query . ' OR `' . $where . '` LIKE "%'. $like . '%"';
        return $this;
    }

    /**
     * Where greater clause
     *
     * Begin building the where portion of the query.
     * 
     * @param string - $record
     * @param mixed - $param - Probably string or int
     * @return object
     */
    public function whereGreater($record, $param)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` > ' . $param;
        return $this;
    }

    /**
     * Where lesser clause
     *
     * Begin building the where portion of the query.
     * 
     * @param string - $record
     * @param mixed - $param - Probably string or int
     * @return object
     */
    public function whereLesser($record, $param)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` < ' . $param;
        return $this;
    }

    /**
     * Where highest clause
     *
     * Begin building the where portion of the query.
     * 
     * @param string - $record
     * @param mixed - $param - Probably string or int
     * @return object
     */
    public function highest($record)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` = (SELECT max(' . $record . ') FROM `' . $this->table . '`)';
        return $this;
    }

    /**
     * Where highest and where clause
     * 
     * Begin building the where portion of the query.
     *
     * @param string - $record
     * @param string - $column
     * @param mixed - $is - Probably string or int
     * @return object
     */
    public function highestAndWhere($record, $column, $is)
    {
        $this->query = $this->query . ' WHERE `' . $record . '` = (SELECT max(' . $record . ') FROM `' . $this->table . '` WHERE `' . $column . '` = "' . $is . '")';
        return $this;
    }

    /**
     * Where month
     *
     * Begin building the where month portion of the query.
     * 
     * @param string - $column
     * @param string - $month
     * @param string - $year
     * @return object
     */
    public function whereMonth($column, $month, $year)
    {
        $this->query = $this->query . ' WHERE MONTH(`' . $column . '`) = ' . $month . ' AND YEAR(`' . $column . '`) = ' . $year;
        return $this;
    }
    
    /**
     * Order by clause
     * 
     * Continue building the query string deciding which order the 
     * records will come in.
     *
     * @param string - $orderby
     * @param string - $direction
     * @return object
     */
    public function orderBy($orderby, $direction)
    {
        $this->query = $this->query . ' ORDER BY `' . $orderby . '` ' . strtoupper($direction);
        return $this;
    }

    /**
     * Limit clause
     * 
     * Continue building the query string and limiting the amount
     * of records.
     *
     * @param int - $limit
     * @return object
     */
    public function limit($limit)
    {
        $this->query = $this->query . ' LIMIT ' . $limit;
        $this->limit = $limit;
        return $this;
    }

    /**
     * Limit between clause
     * 
     * Continue building the query string and limiting the amount
     * of records.
     *
     * @param int - $start
     * @param int - $end
     * @return object
     */
    public function limitBetween($start, $end)
    {
        $this->query = $this->query . ' LIMIT ' . $start . ', ' . $end;
        return $this;
    }

    /**
     * Left join
     * 
     * Begin a join portion of the query string, joinging 2 tables.
     * Returns all records from the left table, and the matched 
     * records from the right table.
     *
     * @param string - $join - A table to join
     * @param string - $selector1 - A column to compare to another
     * @param string - $selector2 - A column to be compared against
     * @return object
     */
    public function leftJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' LEFT JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    /**
     * Right join
     * 
     * Begin a join portion of the query string, joinging 2 tables.
     * Returns all records from the right table, and the matched 
     * records from the left table.
     *
     * @param string - $join - A table to join
     * @param string - $selector1 - A column to compare to another
     * @param string - $selector2 - A column to be compared against
     * @return object
     */
    public function rightJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' RIGHT JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    /**
     * Inner join
     * 
     * Begin a join portion of the query string, joinging 2 tables.
     * Returns records that have matching values in both tables.
     *
     * @param string - $join - A table to join
     * @param string - $selector1 - A column to compare to another
     * @param string - $selector2 - A column to be compared against
     * @return object
     */
    public function innerJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' INNER JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    /**
     * Full join
     * 
     * Begin a join portion of the query string, joinging 2 tables.
     * Returns all records when there is a match in either left or right table.
     *
     * @param string - $join - A table to join
     * @param string - $selector1 - A column to compare to another
     * @param string - $selector2 - A column to be compared against
     * @return object
     */
    public function fullJoin($join, $selector1, $selector2)
    {
        $this->query = $this->query . ' FULL JOIN `' . $join . '` ON ' . $this->table . '.' . $selector1 . ' = ' . $join . '.' . $selector2;
        return $this;
    }

    /**
     * Delete clause
     * 
     * Begin the delete portion of the query.
     *
     * @return object
     */
    public function delete()
    {
        $this->query = 'DELETE FROM `' . $this->table . '`';
        return $this;
    }

    /**
     * Truncate clause
     * 
     * Begin the truncate portion of the query.
     *
     * @return object
     */
    public function truncate($log)
    {
        $this->query = 'TRUNCATE TABLE `' . $log . '`';
        return $this->con->mysqli->query($this->query);
    }

    /**
     * Get
     *
     * Using the query string, execute the fetch and return a response
     * and a status string.
     * 
     * @param string - $type - The type of fetch to do.
     * @return array
     */
    public function get($type = 'assoc')
    {
        $response = [];
        $query = $this->con->mysqli->query($this->query);
        $fetch = 'fetch_' . $type;
        if ($type == 'string') {
            $fetch = 'fetch_row';
        }
        if ($query) {
            while ($col = $query->$fetch()) {
                $response[] = $col;
            }

            if ($this->limit == 1) {
                if (!empty($response)) {
                    $response = $response[0];
                }
            }

            if ($type == 'string') {
                if (count($response) == count($response, COUNT_RECURSIVE)) {
                    $response = implode($response);
                } else {
                    $response = array_values($response[0]);
                    $response = $response[0]; 
                }
            } 

            $output = ['status' => 'success', 'response' => $response];
            return $output;
        } else {
            return $this->debug($this->con->mysqli->error);
        }
    }

    /**
     * Insert
     * 
     * Using the query string prepare the data for insertion into 
     * the database.
     *
     * @param mixed - $data - Array of data to be inserted.
     * @return object
     */
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

    /**
     * Update
     *
     * Using the query string prepare the data to be updated in 
     * the database.
     * 
     * @param mixed - $data - Array of data to be updated.
     * @return void
     */
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

    /**
     * Execute
     * 
     * After the data has been prepared by either the update or the insert
     * methods execute the query.
     *
     * @return array
     */
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

    /**
     * Test Execute
     * 
     * Text an execute without actually inserting or updating any records.
     *
     * @return void
     */
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
            echo '<pre>' , var_dump($data) , '</pre>';
        }
        
        if (isset($types)) {
            echo '<pre>' , var_dump($types) , '</pre>';
        }
    }
    
    /**
     * Test Select
     * 
     * Echo and var dump a select query.
     *
     * @return void
     */
    public function testSelect()
    {
        echo $this->query . '<br>';
        echo '<pre>' , var_dump($this->query) , '</pre>';
    }

    /**
     * Reset
     * 
     * Reset the class properties so when a new query is started 
     * fresh data can be manipulated.
     *
     * @return void
     */
    public function reset()
    {
        $this->post_data = null;
        $this->where = null;
        $this->query = null;
        $this->limit = null;
    }

    /**
     * Log
     * 
     * Insert a message into the log table.
     *
     * @param string - $message - Log message
     * @return void
     */
    public function log($message)
    {
        $this->table('errors')->insert($message = ['event' => $message])->execute();
    }

    /**
     * Debug
     * 
     * If there's an error in the sql query log it and determine where it came from.
     *
     * @param string - $sql_error - The error returned by sql.
     * @return array
     */
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