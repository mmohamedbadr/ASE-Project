<?php



class MY_Model extends CI_Model {

    const DB_TABLE      = 'abstract';

    const DB_TABLE_PK   = 'abstract'; 

   function __construct()

    {

          parent::__construct();

    }

    /**

     * find_by_id from the database.

     * @param int $id

     */

    public function find_by_id($id) {

        $query = $this->db->get_where( $this::DB_TABLE, array($this::DB_TABLE_PK => $id, ) );

        

        $this->populate($query->row());

    }

    

    /**

     * Get an array of Models with an optional limit, offset.

     * 

     * @param int $limit Optional.

     * @param int $offset Optional; if set, requires $limit.

     * @return array Models populated by database, keyed by PK.

    */

     public function find_all( $limit= 0, $offset= 0 , $fields= '',$cond= '',$condval= null  ) {

        if($fields !=null){    $this->db->select($fields); }

        if ($cond ) {

            $query = $this->db->where($cond , $condval);

        }

        if ($limit ) {

            $query = $this->db->get($this::DB_TABLE, $limit, $offset);

        }

        else {

            $query = $this->db->get($this::DB_TABLE);

        }

        $ret_val = array();

        $class = get_class($this);

        foreach ($query->result() as $row) {

            $model = new $class;

            $model->populate($row);

            $ret_val[$row->{$this::DB_TABLE_PK}] = $model;

        }

        return $ret_val;

    }



    public function find_list_by( $field_name, $field_val)

    {

        $ret_vals = array();

        $this->db->from($this::DB_TABLE.' AS tbl ');

        $this->db->select('tbl.*'); 

        $this->db->where('tbl.'.$field_name , $field_val);

        $query = $this->db->get();



        $class = get_class($this);

        foreach ($query->result() as $row)

        {

            $model = new $class;

            $model->populate($row);

            $ret_vals[$row->{$this::DB_TABLE_PK}] = $model;

        }



        return $ret_vals;

    }



    public function count_all() {

        $rows_count = $this->db->count_all($this::DB_TABLE);

        return $rows_count;

    }



    public function count_by($field_name, $field_val)

    {

        $this->db->where($field_name , $field_val); 

        $this->db->from($this::DB_TABLE); 



        return $this->db->count_all_results();

    }  

    /**

      * Populate from an array or standard class.

      * @param mixed $row

    */

    public function populate($row) {

     if(!empty($row)) { 
     foreach ($row as $key => $value) {

            $this->$key = $value;
        }
     }

    }



    /**---  Save the record. --- */

    public function save() {

        if (  isset($this->{$this::DB_TABLE_PK}) ) {

            $this->update();

        }

        else {

            $this->insert();

        }

    }



    /**--  * Create record.  --*/

    protected function insert() {

        $this->db->insert($this::DB_TABLE, $this);    

        if( $this->db->insert_id() ){ $this->{$this::DB_TABLE_PK} = $this->db->insert_id(); }

    }

    

    /**--  Update record. ----*/

    protected function update() {

        $this->db->where($this::DB_TABLE_PK, $this->{$this::DB_TABLE_PK});

        $this->db->update($this::DB_TABLE, $this);

    }

    

    /**--  Delete the current record. ----*/

    public function delete() {

        $this->db->delete($this::DB_TABLE, array(

           $this::DB_TABLE_PK => $this->{$this::DB_TABLE_PK}, 

        ));

        unset($this->{$this::DB_TABLE_PK});

        if($this->db->affected_rows()== 1){return true;}

        else{return false;}

    }



}