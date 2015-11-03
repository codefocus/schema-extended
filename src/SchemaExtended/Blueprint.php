<?php namespace SchemaExtended;

use Illuminate\Support\Fluent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

/**
 * Extended version of Blueprint with
 * support of 'set' data type
 */
class Blueprint extends IlluminateBlueprint {


    /**
    * Create a new Geometry column on the table.
    *
    * @param  string   $column
    * @return \Illuminate\Support\Fluent
    */
    public function geometry($column, $type)
    {
        $spatial_types = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon', 'geometrycollection'];
    
        if ( ! in_array( $type, $spatial_types) )
            throw new \Exception('Unknown geometry type: ' . $type);
    
        // necessario per non overridare type che è già usato nel namespace chiamante
        $geotype = $type;
    
        return $this->addColumn('geometry', $column, compact('geotype'));
    }

    /**
     * Create a new binary column on the table.
     *
     * @param  string  $column
     * @param  int  $length
     * @return \Illuminate\Support\Fluent
     */
    public function binary($column, $length = 255)
    {
        return $this->addColumn('binary', $column, compact('length'));
    }

    /**
     * Create a new 'set' column on the table.
     *
     * @param  string  $column
     * @param  array   $allowed
     * @return \Illuminate\Support\Fluent
     */
    public function set($column, array $allowed)
    {
        return $this->addColumn('set', $column, compact('allowed'));
    }

    /**
     * Specify a unique index for the table.
     *
     * @param  string|array  $columns
     * @param  string        $name
     * @param  int           $length
     * @return \Illuminate\Support\Fluent
     */
    public function unique($columns, $name = null, $length = null)
    {
        return $this->indexCommand('unique', $columns, $name, $length);
    }

    /**
     * Specify an index for the table.
     *
     * @param  string|array  $columns
     * @param  string        $name
     * @param  int           $length
     * @return \Illuminate\Support\Fluent
     */
    public function index($columns, $name = null, $length = null)
    {
        return $this->indexCommand('index', $columns, $name, $length);
    }

    /**
     * Determine if the given table exists.
     *
     * @param  string $table
     *
     * @return bool
     */
    public function hasForeign($table, $foreign)
    {
        $sql = $this->grammar->compileHasForeign();

        $table = $this->connection->getTablePrefix() . $table;

        return count($this->connection->select($sql, [$table, $foreign])) > 0;
    }

    /**
     * Add a new index command to the blueprint.
     *
     * @param  string        $type
     * @param  string|array  $columns
     * @param  string        $index
     * @param  int           $length
     * @return \Illuminate\Support\Fluent
     */
    protected function indexCommand($type, $columns, $index, $length = null)
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        if (is_null($index))
        {
            $index = $this->createIndexName($type, $columns);
        }

        return $this->addCommand($type, compact('index', 'columns', 'length'));
    }

}
