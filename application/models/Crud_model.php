<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Crud_model extends CI_Model
{ //il nome della classe deve essere uguale al nome del file



    // CRUD

    // CREATE
    function create_object($table, $data)
    {
        $insert = $this->db->insert($table, $data);
        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    // READ
    function read_object(
        $table,
        $select = '*',
        $where_table_name = null,
        $where_table_value = null,
        $like_column = null,
        $like_value = null,
        $limit = null,
        $order_by = null,
        $asc = 'ASC',
        $group_by = null
    ) {

        $this->db->select($select);
        $this->db->from($table);
        if ($where_table_value && $where_table_name) {
            if (gettype($where_table_name) == "array") {
                for ($i = 0; $i < count($where_table_name); $i++) {
                    $this->db->where($where_table_name[$i], $where_table_value[$i]);
                }
            } else {
                $this->db->where($where_table_name, $where_table_value);
            }
        }
        if ($like_column && $like_value) {
            $this->db->like($like_column, $like_value);
        }
        if ($group_by) {
            $this->db->group_by($group_by);
        }
        if ($order_by) {
            $this->db->order_by($order_by, $asc);
        }
        if ($limit) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        } else {
            return false;
        }
    }

    //UPDATE
    function update_object($table, $where_table_name, $where_table_value, $data)
    {
        $this->db->where($where_table_name, $where_table_value);
        $update = $this->db->update($table, $data);
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    //DELETE
    function delete_object($table, $where_table_name, $id)
    {
        $this->db->where($where_table_name, $id);
        $delete = $this->db->delete($table);
        if ($delete) {
            return true;
        } else {
            return false;
        }
    }
}
