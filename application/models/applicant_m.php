<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Applicant_model
 *
 * @author Mohamed Badr
 */
class Applicant_m extends MY_Model {

    const DB_TABLE = 'applicant';
    const DB_TABLE_PK = 'id';

    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $mobile;
    public $isactive;

    public function getByID($id) {

        $query = $this->db->get_where($this::DB_TABLE, array('id' => $id,));
        return $query->result();
    }

}
