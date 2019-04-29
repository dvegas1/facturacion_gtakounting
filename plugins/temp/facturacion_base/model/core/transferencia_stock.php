<?php
/*
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016-2017, Carlos García Gómez. All Rights Reserved.
 */
namespace FacturaScripts\model;

/**
 * Description of transferencia_stock
 *
 * @author Carlos García Gómez
 */
class transferencia_stock extends \fs_model
{

    /// clave primaria. integer
    public $idtrans;
    public $codalmadestino;
    public $codalmaorigen;
    public $fecha;
    public $hora;
    public $usuario;

    public function __construct($data = FALSE)
    {
        parent::__construct('transstock');
        if ($data) {
            $this->idtrans = $this->intval($data['idtrans']);
            $this->codalmadestino = $data['codalmadestino'];
            $this->codalmaorigen = $data['codalmaorigen'];
            $this->fecha = date("d-m-Y", strtotime($data['fecha']));
            $this->hora = date('H:i:s', strtotime($data['hora']));
            $this->usuario = $data['usuario'];
        } else {
            /// valores predeterminados
            $this->idtrans = NULL;
            $this->codalmadestino = NULL;
            $this->codalmaorigen = NULL;
            $this->fecha = date('d-m-Y');
            $this->hora = date('H:i:s');
            $this->usuario = NULL;
        }
    }

    public function install()
    {
        return '';
    }

    public function url()
    {
        return 'index.php?page=editar_transferencia_stock&id=' . $this->idtrans;
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE idtrans = " . $this->var2str($id) . ";");
        if ($data) {
            return new \transferencia_stock($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->idtrans)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE idtrans = " . $this->var2str($this->idtrans) . ";");
    }

    public function test()
    {
        if ($this->codalmadestino == $this->codalmaorigen) {
            $this->new_error_msg('El almacén de orígen y de destino no puede ser el mismo.');
            return FALSE;
        }

        return TRUE;
    }

    public function save()
    {
        if ($this->test()) {
            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name . " SET codalmadestino = " . $this->var2str($this->codalmadestino)
                    . ", codalmaorigen = " . $this->var2str($this->codalmaorigen)
                    . ", fecha = " . $this->var2str($this->fecha)
                    . ", hora = " . $this->var2str($this->hora)
                    . ", usuario = " . $this->var2str($this->usuario)
                    . "  WHERE idtrans = " . $this->var2str($this->idtrans) . ";";

                return $this->db->exec($sql);
            }

            $sql = "INSERT INTO " . $this->table_name . " (codalmadestino,codalmaorigen,fecha,hora,usuario) VALUES "
                . "(" . $this->var2str($this->codalmadestino)
                . "," . $this->var2str($this->codalmaorigen)
                . "," . $this->var2str($this->fecha)
                . "," . $this->var2str($this->hora)
                . "," . $this->var2str($this->usuario) . ");";

            if ($this->db->exec($sql)) {
                $this->idtrans = $this->db->lastval();
                return TRUE;
            }
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE idtrans = " . $this->var2str($this->idtrans) . ";");
    }

    public function all()
    {
        $tlist = array();

        $data = $this->db->select("SELECT * FROM " . $this->table_name . " ORDER BY fecha DESC, hora DESC;");
        if ($data) {
            foreach ($data as $d) {
                $tlist[] = new \transferencia_stock($d);
            }
        }

        return $tlist;
    }
}
