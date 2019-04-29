<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2015-2019, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Description of articulo_propiedad
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class compras_art_prop extends fs_model
{

    public $name;
    public $idartprov;
    public $text;

    public function __construct($data = FALSE)
    {
        parent::__construct('compras_art_prop');
        if ($data) {
            $this->name = $data['name'];
            $this->idartprov = $data['idartprov'];
            $this->text = $data['text'];
        } else {
            $this->name = NULL;
            $this->idartprov = NULL;
            $this->text = NULL;
        }
    }

    protected function install()
    {
        return '';
    }

    public function exists()
    {
        if (is_null($this->name) || is_null($this->idartprov)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM compras_art_prop WHERE name = " .
                $this->var2str($this->name) . " AND idartprov = " . $this->var2str($this->idartprov) . ";");
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE compras_art_prop SET text = " . $this->var2str($this->text)
                . " WHERE name = " . $this->var2str($this->name)
                . " AND idartprov = " . $this->var2str($this->idartprov) . ";";
        } else {
            $sql = "INSERT INTO compras_art_prop (name,idartprov,text) VALUES
                   (" . $this->var2str($this->name)
                . "," . $this->var2str($this->idartprov)
                . "," . $this->var2str($this->text) . ");";
        }

        return $this->db->exec($sql);
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM compras_art_prop WHERE name = " .
                $this->var2str($this->name) . " AND idartprov = " . $this->var2str($this->idartprov) . ";");
    }

    /**
     * Devuelve un array con los pares name => text para una idartprov dado.
     *
     * @param int $idartprov
     *
     * @return array
     */
    public function array_get($idartprov)
    {
        $vlist = [];

        $data = $this->db->select("SELECT * FROM compras_art_prop WHERE idartprov = " . $this->var2str($idartprov) . ";");
        if ($data) {
            foreach ($data as $d) {
                $vlist[$d['name']] = $d['text'];
            }
        }

        return $vlist;
    }

    public function array_save($idartprov, $values)
    {
        $done = TRUE;

        foreach ($values as $key => $value) {
            $aux = new compras_art_prop();
            $aux->name = $key;
            $aux->idartprov = $idartprov;
            $aux->text = $value;
            if (!$aux->save()) {
                $done = FALSE;
                break;
            }
        }

        return $done;
    }

    public function simple_get($idartprov, $name)
    {
        $sql = "SELECT * FROM compras_art_prop WHERE idartprov = " . $this->var2str($idartprov)
            . " AND name = " . $this->var2str($name) . ";";
        $data = $this->db->select($sql);
        if ($data) {
            return $data[0]['text'];
        }

        return FALSE;
    }

    public function simple_get_idartprov($name, $text)
    {
        $sql = "SELECT * FROM compras_art_prop WHERE text = " . $this->var2str($text)
            . " AND name = " . $this->var2str($name) . ";";
        $data = $this->db->select($sql);
        if ($data) {
            return $data[0]['idartprov'];
        }

        return FALSE;
    }

    public function simple_delete($idartprov, $name)
    {
        return $this->db->exec("DELETE FROM compras_art_prop WHERE idartprov = " . $this->var2str($idartprov)
                . " AND name = " . $this->var2str($name) . ";");
    }
}
