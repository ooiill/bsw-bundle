<?php

namespace Leon\BswBundle\Component;

use Doctrine\DBAL\Connection;

class MysqlDoc
{
    /**
     * @var Connection
     */
    protected $pdo;

    /**
     * @var array
     */
    protected $databases;

    /**
     * @var array
     */
    protected $tables;

    /**
     * Stringify for databases / tables name
     *
     * @param array $items
     *
     * @return string
     */
    protected function stringify(array $items): string
    {
        return implode(',', $items);
    }

    /**
     * Get sql for list items by databases
     *
     * @param array $databases
     *
     * @return string
     */
    protected function sqlListItemsByDatabase(array $databases): string
    {
        $databases = $this->stringify($databases);

        return <<<EOS
        SELECT
            TABLE_SCHEMA,
            TABLE_NAME,
            COLUMN_NAME,
            COLUMN_DEFAULT,
            IS_NULLABLE,
            COLUMN_COMMENT,
            DATA_TYPE,
            COLUMN_TYPE,
            COLUMN_KEY,
            EXTRA,
            CHARACTER_MAXIMUM_LENGTH,
            NUMERIC_PRECISION
        FROM
            information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA IN ('{$databases}')
        ORDER BY
            TABLE_SCHEMA ASC,
            TABLE_NAME ASC,
            ORDINAL_POSITION ASC
EOS;
    }

    /**
     * Get sql for list items by tables
     *
     * @param array $databases
     * @param array $tables
     *
     * @return string
     */
    protected function sqlListItemsByTable(array $databases, array $tables): string
    {
        $databases = $this->stringify($databases);
        $tables = $this->stringify($tables);

        return <<<EOS
        SELECT
            TABLE_SCHEMA,
            TABLE_NAME,
            COLUMN_NAME,
            COLUMN_DEFAULT,
            IS_NULLABLE,
            COLUMN_COMMENT,
            DATA_TYPE,
            COLUMN_TYPE,
            COLUMN_KEY,
            EXTRA,
            CHARACTER_MAXIMUM_LENGTH,
            NUMERIC_PRECISION
        FROM
            information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA IN ('{$databases}') AND TABLE_NAME IN ('{$tables}')
        ORDER BY
            TABLE_SCHEMA ASC,
            TABLE_NAME ASC,
            ORDINAL_POSITION ASC
EOS;
    }

    /**
     * Get sql for list comment by databases
     *
     * @param array $databases
     *
     * @return string
     */
    protected function sqlListComment(array $databases): string
    {
        $databases = $this->stringify($databases);

        return <<<EOS
        SELECT
            TABLE_SCHEMA,
            TABLE_NAME,
            TABLE_COMMENT
        FROM
            information_schema.TABLES
        WHERE
            TABLE_SCHEMA IN ('{$databases}')
EOS;
    }

    /**
     * Get sql for list index for table
     *
     * @param string $database
     * @param string $table
     *
     * @return string
     */
    protected function sqlTableIndex(string $database, string $table): string
    {
        $target = Helper::tableFieldAddTag("{$database}.{$table}");

        return "SHOW INDEX FROM {$target}";
    }

    /**
     * Create document like array
     *
     * @param Connection $pdo
     * @param array      $tables
     * @param array      $databases
     *
     * @return array
     */
    public function create(Connection $pdo, ?array $databases = null, array $tables = []): array
    {
        $this->pdo = $pdo;
        $this->databases = $databases ?: [$this->pdo->getDatabase()];
        $this->tables = $tables;

        $itemsList = $this->listItems();
        $commentList = $this->listComment();

        if (empty($itemsList)) {
            return [];
        }

        foreach ($itemsList as $dbName => &$table) {
            foreach ($table as $tableName => &$data) {
                $data['comment'] = $commentList[$dbName][$tableName];
                $data['index'] = $this->getTableIndex($dbName, $tableName);
            }
        }

        return $itemsList;
    }

    /**
     * List items for databases / tables
     *
     * @return array
     */
    private function listItems()
    {
        if ($this->tables) {
            $sql = $this->sqlListItemsByTable($this->databases, $this->tables);
        } else {
            $sql = $this->sqlListItemsByDatabase($this->databases);
        }

        $itemsList = $this->pdo->fetchAll($sql);
        if (empty($itemsList)) {
            return [];
        }

        $main = [];
        foreach ($itemsList as $val) {
            $main[$val['TABLE_SCHEMA']][$val['TABLE_NAME']]['fields'][] = [
                'name'     => $val['COLUMN_NAME'],
                'type'     => strtolower($val['DATA_TYPE']),
                'comment'  => $val['COLUMN_COMMENT'],
                'null'     => $val['IS_NULLABLE'] == 'NO' ? false : true,
                'default'  => Helper::numericValue($val['COLUMN_DEFAULT']),
                'flag'     => $val['COLUMN_KEY'],
                'extra'    => $val['EXTRA'],
                'unsigned' => strpos($val['COLUMN_TYPE'], 'unsigned') !== false,
                'length'   => $val['CHARACTER_MAXIMUM_LENGTH'],
            ];
        }

        return $main;
    }

    /**
     * List comment for database
     *
     * @return array
     */
    private function listComment(): array
    {
        $sql = $this->sqlListComment($this->databases);
        $commentList = $this->pdo->fetchAll($sql);

        if (empty($commentList)) {
            return [];
        }

        $comment = [];
        foreach ($commentList as $val) {
            $comment[$val['TABLE_SCHEMA']][$val['TABLE_NAME']] = $val['TABLE_COMMENT'];
        }

        return $comment;
    }

    /**
     * Get the index and primary for table
     *
     * @param string $database
     * @param string $table
     *
     * @return array
     */
    private function getTableIndex(string $database, string $table): array
    {
        $sql = $this->sqlTableIndex($database, $table);
        $indexList = $this->pdo->fetchAll($sql);

        if (empty($indexList)) {
            return [];
        }

        $index = [];
        foreach ($indexList as $key => $val) {
            $k = $val['Key_name'];
            if (empty($index[$k])) {
                $index[$k] = [
                    'fields' => [$val['Column_name']],
                    'unique' => !$val['Non_unique'],
                    'type'   => $val['Index_type'],
                ];
            } else {
                array_push($index[$k]['fields'], $val['Column_name']);
            }
        }

        return $index;
    }
}