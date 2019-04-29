<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Línea de una comanda de TPV
 */
class linea_comanda extends fs_model
{

    /**
     * Clave primaria.
     * @var type 
     */
    public $idlinea;

    /**
     * ID de la comanda relacionada.
     * @var type 
     */
    public $idtpv_comanda;
    public $pvptotal;
    public $dtopor;
    public $recargo;
    public $irpf;
    public $pvpsindto;
    public $cantidad;
    public $codimpuesto;
    public $pvpunitario;
    public $descripcion;

    /**
     * Referencia del artículo.
     * @var type 
     */
    public $referencia;

    /**
     * Código de la combinación seleccionada, en el caso de los artículos con atributos.
     * @var type 
     */
    public $codcombinacion;
    public $iva;

    public function __construct($data = FALSE)
    {
        parent::__construct('tpv_lineascomanda');
        if ($data) {
            $this->idlinea = $this->intval($data['idlinea']);
            $this->idtpv_comanda = $this->intval($data['idtpv_comanda']);
            $this->referencia = $data['referencia'];
            $this->codcombinacion = $data['codcombinacion'];
            $this->descripcion = $data['descripcion'];
            $this->cantidad = floatval($data['cantidad']);
            $this->pvpunitario = floatval($data['pvpunitario']);
            $this->pvpsindto = floatval($data['pvpsindto']);
            $this->dtopor = floatval($data['dtopor']);
            $this->pvptotal = floatval($data['pvptotal']);
            $this->codimpuesto = $data['codimpuesto'];
            $this->iva = floatval($data['iva']);
            $this->recargo = floatval($data['recargo']);
            $this->irpf = floatval($data['irpf']);
        } else {
            $this->idlinea = NULL;
            $this->idtpv_comanda = NULL;
            $this->referencia = NULL;
            $this->codcombinacion = NULL;
            $this->descripcion = '';
            $this->cantidad = 0;
            $this->pvpunitario = 0;
            $this->pvpsindto = 0;
            $this->dtopor = 0;
            $this->pvptotal = 0;
            $this->codimpuesto = NULL;
            $this->iva = 0;
            $this->recargo = 0;
            $this->irpf = 0;
        }
    }

    protected function install()
    {
        return '';
    }

    public function total_iva()
    {
        return $this->pvptotal * (100 + $this->iva - $this->irpf + $this->recargo) / 100;
    }

    public function articulo_url()
    {
        if (is_null($this->referencia) OR $this->referencia == '') {
            return "index.php?page=ventas_articulos";
        }

        return "index.php?page=ventas_articulo&ref=" . urlencode($this->referencia);
    }

    public function exists()
    {
        if (is_null($this->idlinea)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->table_name . " WHERE idlinea = " . $this->var2str($this->idlinea) . ";");
    }

    public function test()
    {
        $this->descripcion = $this->no_html($this->descripcion);
        $total = $this->pvpunitario * $this->cantidad * (100 - $this->dtopor) / 100;
        $totalsindto = $this->pvpunitario * $this->cantidad;

        if (!$this->floatcmp($this->pvptotal, $total, FS_NF0, TRUE)) {
            $this->new_error_msg("Error en el valor de pvptotal de la línea " . $this->referencia . " de la factura. Valor correcto: " . $total);
            return FALSE;
        } else if (!$this->floatcmp($this->pvpsindto, $totalsindto, FS_NF0, TRUE)) {
            $this->new_error_msg("Error en el valor de pvpsindto de la línea " . $this->referencia . " de la factura. Valor correcto: " . $totalsindto);
            return FALSE;
        }

        return TRUE;
    }

    public function save()
    {
        if ($this->test()) {
            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name . " SET idtpv_comanda = " . $this->var2str($this->idtpv_comanda)
                    . ", referencia = " . $this->var2str($this->referencia)
                    . ", codcombinacion = " . $this->var2str($this->codcombinacion)
                    . ", descripcion = " . $this->var2str($this->descripcion)
                    . ", cantidad = " . $this->var2str($this->cantidad)
                    . ", pvpunitario = " . $this->var2str($this->pvpunitario)
                    . ", pvpsindto = " . $this->var2str($this->pvpsindto)
                    . ", dtopor = " . $this->var2str($this->dtopor)
                    . ", pvptotal = " . $this->var2str($this->pvptotal)
                    . ", codimpuesto = " . $this->var2str($this->codimpuesto)
                    . ", iva = " . $this->var2str($this->iva)
                    . ", recargo = " . $this->var2str($this->recargo)
                    . ", irpf = " . $this->var2str($this->irpf)
                    . "  WHERE idlinea = " . $this->var2str($this->idlinea) . ";";

                return $this->db->exec($sql);
            }

            $sql = "INSERT INTO " . $this->table_name . " (idtpv_comanda,referencia,codcombinacion,
               descripcion,cantidad,pvpunitario,pvpsindto,dtopor,pvptotal,codimpuesto,iva,recargo,irpf)
               VALUES (" . $this->var2str($this->idtpv_comanda) .
                "," . $this->var2str($this->referencia) .
                "," . $this->var2str($this->codcombinacion) .
                "," . $this->var2str($this->descripcion) .
                "," . $this->var2str($this->cantidad) .
                "," . $this->var2str($this->pvpunitario) .
                "," . $this->var2str($this->pvpsindto) .
                "," . $this->var2str($this->dtopor) .
                "," . $this->var2str($this->pvptotal) .
                "," . $this->var2str($this->codimpuesto) .
                "," . $this->var2str($this->iva) .
                "," . $this->var2str($this->recargo) .
                "," . $this->var2str($this->irpf) . ");";

            if ($this->db->exec($sql)) {
                $this->idlinea = $this->db->lastval();
                return TRUE;
            }
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM " . $this->table_name . " WHERE idlinea = " . $this->var2str($this->idlinea) . ";");
    }

    public function all_from_comanda($id)
    {
        $linlist = array();

        $data = $this->db->select("SELECT * FROM " . $this->table_name . " WHERE idtpv_comanda = " . $this->var2str($id) . " ORDER BY idlinea ASC;");
        if ($data) {
            foreach ($data as $l) {
                $linlist[] = new linea_comanda($l);
            }
        }

        return $linlist;
    }
}
