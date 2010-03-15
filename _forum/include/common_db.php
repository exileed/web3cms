<?php
if (defined('FORUM_SHOW_QUERIES'))
{
    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}

class DBLayer extends CDbConnection
{
    public $db_prefix, $CDbCommand, $CDbDataReader;
    public $sameQuery = false;
    public $saved_queries = array();
    public $num_queries = 0;
    public $datatype_transformations = array(
        '/^SERIAL$/' => 'INT(10) UNSIGNED AUTO_INCREMENT'
        );

    public function setQuery($sql, $unbuffered = false)
    {
        if (strlen($sql) > 140000)
            message('Insane query. Aborting.') ;

        if (defined('FORUM_SHOW_QUERIES'))
            $q_start = get_microtime();

        $this->CDbCommand = $this->createCommand($sql);
        $this->sameQuery = false;

        if ($this->CDbCommand)
        {
            if (defined('FORUM_SHOW_QUERIES'))
                $this->saved_queries[] = array($sql, sprintf('%.5f', get_microtime() - $q_start));

            ++$this->num_queries;

            return true;
        }
        else
        {
            if (defined('FORUM_SHOW_QUERIES'))
                $this->saved_queries[] = array($sql, 0);

            return false;
        }
    }

    public function result($row = 0, $col = 0)
    {
        if ($row != 0)
        {
            $res = $this->CDbCommand->query()->readAll();
            return $res[$row];
        }
        else
        {
            $res = $this->CDbCommand->queryRow(false);
            return $res[$col];
        }
    }

    public function fetch_assoc()
    {
        if ($this->sameQuery == false)
        {
            $this->sameQuery = true;
            $this->CDbDataReader = $this->CDbCommand->query();
        }
        return $this->CDbDataReader->read();
    }

    public function fetch_row()
    {
        if ($this->sameQuery == false)
        {
            $this->sameQuery = true;
        	$this->CDbDataReader = $this->CDbCommand->query();
        }
        return $this->CDbDataReader->read();
    }

    public function num_rows()
    {
		if ($this->sameQuery == false) {
			$this->sameQuery = true;
			$this->CDbDataReader = $this->CDbCommand->query();
		}
    return $this->CDbDataReader->getRowCount();
    }

    public function affected_rows()
    {
        return $this->CDbCommand->execute();
    }

    public function insert_id()
    {
        return $this->getLastInsertID();
    }

    public function get_num_queries()
    {
        return $this->num_queries;
    }

    public function get_saved_queries()
    {
        return $this->saved_queries;
    }

    public function escape($str)
    {
    	return strtr($str, array(
    	"\x00" => '\x00',
    	"\n" => '\n',
    	"\r" => '\r',
    	'\\' => '\\\\',
    	"'" => "\'",
    	'"' => '\"',
    	"\x1a" => '\x1a'
    ));
    }

    public function error()
    {
        $result['error_sql'] = @current(@end($this->saved_queries));
        return $result;
    }

    public function close()
    {
        return;
    }

    public function set_names($names)
    {
        return $this->setQuery('SET NAMES \'' . $this->escape($names) . '\'');
    }

    public function get_version()
    {
        $result = $this->setQuery('SELECT VERSION()');

        return array(
            'name' => 'MySQL Improved',
            'version' => preg_replace('/^([^-]+).*$/', '\\1', $this->CDbCommand($result))
            );
    }

    public function table_exists($table_name, $no_prefix = false)
    {
        $this->setQuery('SHOW TABLES LIKE \'' . ($no_prefix ? '' : $this->db_prefix) . $this->escape($table_name) . '\'');
        return $this->num_rows() > 0;
    }

    public function field_exists($table_name, $field_name, $no_prefix = false)
    {
        $this->setQuery('SHOW COLUMNS FROM ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' LIKE \'' . $this->escape($field_name) . '\'');
        return $this->num_rows() > 0;
    }

    public function index_exists($table_name, $index_name, $no_prefix = false)
    {
        $exists = false;

        $this->setQuery('SHOW INDEX FROM ' . ($no_prefix ? '' : $this->db_prefix) . $table_name);
        while ($cur_index = $this->fetch_assoc())
        {
            if ($cur_index['Key_name'] == ($no_prefix ? '' : $this->db_prefix) . $table_name . '_' . $index_name)
            {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    public function create_table($table_name, $schema, $no_prefix = false)
    {
        if ($this->table_exists($table_name, $no_prefix))
            return;

        $result = 'CREATE TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . " (\n";
        foreach ($schema['FIELDS'] as $field_name => $field_data)
        {
            $field_data['datatype'] = preg_replace(array_keys($this->datatype_transformations), array_values($this->datatype_transformations), $field_data['datatype']);

            $result .= $field_name . ' ' . $field_data['datatype'];

            if (isset($field_data['collation']))
                $result .= 'CHARACTER SET utf8 COLLATE utf8_' . $field_data['collation'];

            if (!$field_data['allow_null'])
                $result .= ' NOT NULL';

            if (isset($field_data['default']))
                $result .= ' DEFAULT ' . $field_data['default'];

            $result .= ",\n";
        }
        if (isset($schema['PRIMARY KEY']))
            $result .= 'PRIMARY KEY (' . implode(',', $schema['PRIMARY KEY']) . '),' . "\n";
        if (isset($schema['UNIQUE KEYS']))
        {
            foreach ($schema['UNIQUE KEYS'] as $key_name => $key_fields)
            $result .= 'UNIQUE KEY ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . '_' . $key_name . '(' . implode(',', $key_fields) . '),' . "\n";
        }
        if (isset($schema['INDEXES']))
        {
            foreach ($schema['INDEXES'] as $index_name => $index_fields)
            $result .= 'KEY ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . '_' . $index_name . '(' . implode(',', $index_fields) . '),' . "\n";
        }
        $result = substr($result, 0, strlen($result) - 2) . "\n" . ') ENGINE = ' . (isset($schema['ENGINE']) ? $schema['ENGINE'] : 'MyISAM') . ' CHARACTER SET utf8';

        $this->setQuery($result) or error(__FILE__, __LINE__);
    }

    public function drop_table($table_name, $no_prefix = false)
    {
        if (!$this->table_exists($table_name, $no_prefix))
            return;

        $this->setQuery('DROP TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name) or error(__FILE__, __LINE__);
    }

    public function add_field($table_name, $field_name, $field_type, $allow_null, $default_value = null, $after_field = null, $no_prefix = false)
    {
        if ($this->field_exists($table_name, $field_name, $no_prefix))
            return;

        $field_type = preg_replace(array_keys($this->datatype_transformations), array_values($this->datatype_transformations), $field_type);

        if ($default_value !== null && !is_int($default_value) && !is_float($default_value))
            $default_value = '\'' . $this->escape($default_value) . '\'';

        $this->setQuery('ALTER TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' ADD ' . $field_name . ' ' . $field_type . ($allow_null ? ' ' : ' NOT NULL') . ($default_value !== null ? ' DEFAULT ' . $default_value : ' ') . ($after_field != null ? ' AFTER ' . $after_field : '')) or error(__FILE__, __LINE__);
    }

    public function alter_field($table_name, $field_name, $field_type, $allow_null, $default_value = null, $after_field = null, $no_prefix = false)
    {
        if (!$this->field_exists($table_name, $field_name, $no_prefix))
            return;

        $field_type = preg_replace(array_keys($this->datatype_transformations), array_values($this->datatype_transformations), $field_type);

        if ($default_value !== null && !is_int($default_value) && !is_float($default_value))
            $default_value = '\'' . $this->escape($default_value) . '\'';

        $this->setQuery('ALTER TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' MODIFY ' . $field_name . ' ' . $field_type . ($allow_null ? ' ' : ' NOT NULL') . ($default_value !== null ? ' DEFAULT ' . $default_value : ' ') . ($after_field != null ? ' AFTER ' . $after_field : '')) or error(__FILE__, __LINE__);
    }

    public function drop_field($table_name, $field_name, $no_prefix = false)
    {
        if (!$this->field_exists($table_name, $field_name, $no_prefix))
            return;

        $this->setQuery('ALTER TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' DROP ' . $field_name) or error(__FILE__, __LINE__);
    }

    public function add_index($table_name, $index_name, $index_fields, $unique = false, $no_prefix = false)
    {
        if ($this->index_exists($table_name, $index_name, $no_prefix))
            return;

        $this->setQuery('ALTER TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' ADD ' . ($unique ? 'UNIQUE ' : '') . 'INDEX ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . '_' . $index_name . ' (' . implode(',', $index_fields) . ')') or error(__FILE__, __LINE__);
    }

    public function drop_index($table_name, $index_name, $no_prefix = false)
    {
        if (!$this->index_exists($table_name, $index_name, $no_prefix))
            return;

        $this->setQuery('ALTER TABLE ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . ' DROP INDEX ' . ($no_prefix ? '' : $this->db_prefix) . $table_name . '_' . $index_name) or error(__FILE__, __LINE__);
    }

    public function query_build($result, $return_query_string = false, $unbuffered = false)
    {
        $sql = '';

        if (isset($result['SELECT']))
        {
            $sql = 'SELECT ' . $result['SELECT'] . ' FROM ' . (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . $result['FROM'];

            if (isset($result['JOINS']))
            {
                foreach ($result['JOINS'] as $cur_join)
                $sql .= ' ' . key($cur_join) . ' ' . (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . current($cur_join) . ' ON ' . $cur_join['ON'];
            }

            if (!empty($result['WHERE']))
                $sql .= ' WHERE ' . $result['WHERE'];
            if (!empty($result['GROUP BY']))
                $sql .= ' GROUP BY ' . $result['GROUP BY'];
            if (!empty($result['HAVING']))
                $sql .= ' HAVING ' . $result['HAVING'];
            if (!empty($result['ORDER BY']))
                $sql .= ' ORDER BY ' . $result['ORDER BY'];
            if (!empty($result['LIMIT']))
                $sql .= ' LIMIT ' . $result['LIMIT'];
        }
        else if (isset($result['INSERT']))
        {
            $sql = 'INSERT INTO ' . (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . $result['INTO'];

            if (!empty($result['INSERT']))
                $sql .= ' (' . $result['INSERT'] . ')';

            if (is_array($result['VALUES']))
                $sql .= ' VALUES(' . implode('),(', $result['VALUES']) . ')';
            else
                $sql .= ' VALUES(' . $result['VALUES'] . ')';
        }
        else if (isset($result['UPDATE']))
        {
            $result['UPDATE'] = (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . $result['UPDATE'];

            $sql = 'UPDATE ' . $result['UPDATE'] . ' SET ' . $result['SET'];

            if (!empty($result['WHERE']))
                $sql .= ' WHERE ' . $result['WHERE'];
        }
        else if (isset($result['DELETE']))
        {
            $sql = 'DELETE FROM ' . (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . $result['DELETE'];

            if (!empty($result['WHERE']))
                $sql .= ' WHERE ' . $result['WHERE'];
        }
        else if (isset($result['REPLACE']))
        {
            $sql = 'REPLACE INTO ' . (isset($result['PARAMS']['NO_PREFIX']) ? '' : $this->db_prefix) . $result['INTO'];

            if (!empty($result['REPLACE']))
                $sql .= ' (' . $result['REPLACE'] . ')';

            $sql .= ' VALUES(' . $result['VALUES'] . ')';
        }

        return ($return_query_string) ? $sql : $this->setQuery($sql, $unbuffered);
    }
}