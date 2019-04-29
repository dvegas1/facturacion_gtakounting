<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of articulo_codbarras
 *
 * @author carlos
 */
class articulo_codbarras extends fs_model
{

    public $id;
    public $referencia;
    public $codbarras;

    public function __construct($data = FALSE)
    {
        parent::__construct('articulo_codbarras');
        if ($data) {
            $this->id = $this->intval($data['id']);
            $this->referencia = $data['referencia'];
            $this->codbarras = $data['codbarras'];
        } else {
            $this->id = NULL;
            $this->referencia = NULL;
            $this->codbarras = NULL;
        }
    }

    protected function install()
    {
        return '';
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM articulo_codbarras WHERE id = " . $this->var2str($id) . ";");
        if ($data) {
            return new articulo_codbarras($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM articulo_codbarras WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE articulo_codbarras SET referencia = " . $this->var2str($this->referencia) . ",
            codbarras = " . $this->var2str($this->codbarras) . " WHERE id = " . $this->var2str($this->id) . ";";
            return $this->db->exec($sql);
        }

        $sql = "INSERT INTO articulo_codbarras (referencia,codbarras) VALUES (" . $this->var2str($this->referencia) . "," . $this->var2str($this->codbarras) . ");";
        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastval();
            return TRUE;
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM articulo_codbarras WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function all_from_ref($ref)
    {
        $clist = array();

        $data = $this->db->select("SELECT * FROM articulo_codbarras WHERE referencia = " . $this->var2str($ref) . ";");
        if ($data) {
            foreach ($data as $d) {
                $clist[] = new articulo_codbarras($d);
            }
        }

        return $clist;
    }

    public function search($codbar)
    {
        $clist = array();

        $data = $this->db->select("SELECT * FROM articulo_codbarras WHERE codbarras = " . $this->var2str($codbar) . ";");
        if ($data) {
            foreach ($data as $d) {
                $clist[] = new articulo_codbarras($d);
            }
        }

        return $clist;
    }
}
