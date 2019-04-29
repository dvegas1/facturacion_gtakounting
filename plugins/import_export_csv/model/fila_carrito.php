<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2014-2019, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Línea del carrito de compras -> artículos.
 */
class fila_carrito extends fs_model
{

    public $id;
    public $nick;
    public $idarticulop;
    public $articulop;
    public $cantidad;
    private static $articulo_proveedor;

    public function __construct($data = FALSE)
    {
        parent::__construct('filascarrito');
        if ($data) {
            $this->id = intval($data["id"]);
            $this->cantidad = floatval($data["cantidad"]);
            $this->idarticulop = intval($data["idarticuloproveedor"]);
            $this->nick = $data["nickusuario"];
        } else {
            $this->id = NULL;
            $this->cantidad = 1;
            $this->idarticulop = NULL;
            $this->nick = NULL;
        }

        if (!isset(self::$articulo_proveedor)) {
            self::$articulo_proveedor = new articulo_proveedor();
        }

        $this->articulop = self::$articulo_proveedor->get($this->idarticulop);
    }

    protected function install()
    {
        return '';
    }

    public function url()
    {
        return 'index.php?page=compras_articulos#carrito';
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM filascarrito WHERE id = " . $this->var2str($id) . ';');
        if ($data) {
            return new fila_carrito($data[0]);
        }

        return FALSE;
    }

    public function get_by_idarticulop($id)
    {
        $data = $this->db->select("SELECT * FROM filascarrito WHERE idarticuloproveedor = " . $this->var2str($id) . ';');
        if ($data) {
            return new fila_carrito($data[0]);
        }

        return FALSE;
    }

    public function get_by_ref($ref)
    {
        $sql = "SELECT * FROM filascarrito WHERE idarticuloproveedor IN"
            . " (SELECT id as idarticuloproveedor FROM articulosprov WHERE referencia = " . $this->var2str($ref) . ");";

        $data = $this->db->select($sql);
        if ($data) {
            return new fila_carrito($data[0]);
        }

        return FALSE;
    }

    public function get_stockfis()
    {
        $stockfis = 0;

        if ($this->articulo_proveedor()) {
            $art0 = new articulo();
            $articulo = $art0->get($this->articulop->referencia);
            if ($articulo) {
                $stockfis = $articulo->stockfis;
            }
        }

        return $stockfis;
    }

    public function articulo_proveedor()
    {
        if (!$this->articulop) {
            $this->articulop = self::$articulo_proveedor->get($this->idarticulop);
        }

        return $this->articulop;
    }

    public function exists()
    {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM filascarrito WHERE id = " . $this->var2str($this->id) . ';');
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE filascarrito SET nickUsuario = " . $this->var2str($this->nick)
                . ", idarticuloproveedor = " . $this->var2str($this->idarticulop)
                . ", cantidad = " . $this->var2str($this->cantidad)
                . "  WHERE id = " . $this->var2str($this->id) . ';';

            return $this->db->exec($sql);
        }

        $sql = "INSERT INTO filascarrito (nickUsuario,idarticuloproveedor"
            . ",cantidad) VALUES (" . $this->var2str($this->nick)
            . "," . $this->var2str($this->idarticulop)
            . "," . $this->var2str($this->cantidad) . ");";

        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastval();
            return TRUE;
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM filascarrito WHERE id = " . $this->var2str($this->id) . ';');
    }

    public function limpiar_carrito($nick)
    {
        return $this->db->exec("DELETE FROM filascarrito WHERE nickUsuario = " . $this->var2str($nick) . ';');
    }

    /**
     * Devuelve las filas del carrito del usuario.
     *
     * @param string $nick
     *
     * @return \fila_carrito
     */
    public function all_from_nick($nick)
    {
        $filas_carrito = [];

        $data = $this->db->select("SELECT * FROM filascarrito WHERE nickUsuario = " . $this->var2str($nick) . ';');
        if ($data) {
            foreach ($data as $d) {
                $filas_carrito[] = new fila_carrito($d);
            }
        }

        return $filas_carrito;
    }
}
