<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of tpv_comanda
 *
 * @author carlos
 */
class tpv_comanda extends fs_model
{

    /**
     * Clave primaria.
     * @var type 
     */
    public $idtpv_comanda;

    /**
     * ID del arqueo relacionado.
     * @var type 
     */
    public $idtpv_arqueo;

    /**
     * ID de la factura relacionada.
     * @var type 
     */
    public $idfactura;
    public $cifnif;
    public $ciudad;
    public $codalmacen;
    public $codcliente;
    public $coddir;

    /**
     * Forma de pago
     * @var type 
     */
    public $codpago;

    /**
     * Importe pagado con la primera forma de pago
     * @var type 
     */
    public $totalpago;

    /**
     * Segunda forma de pago
     * @var type 
     */
    public $codpago2;

    /**
     * Importe pagado con la segunda forma de pago
     * @var type 
     */
    public $totalpago2;
    public $numero2;
    public $observaciones;
    public $codpais;
    public $codpostal;
    public $direccion;
    public $fecha;
    public $hora;
    public $neto;
    public $nombrecliente;
    public $provincia;
    public $total;
    public $totaliva;
    public $ultentregado;
    public $ultcambio;

    public function __construct($data = FALSE)
    {
        parent::__construct('tpv_comandas');
        if ($data) {
            $this->idtpv_comanda = $this->intval($data['idtpv_comanda']);
            $this->idtpv_arqueo = $data['idtpv_arqueo'];
            $this->idfactura = $this->intval($data['idfactura']);
            $this->cifnif = $data['cifnif'];
            $this->ciudad = $data['ciudad'];
            $this->codalmacen = $data['codalmacen'];
            $this->codcliente = $data['codcliente'];
            $this->coddir = $data['coddir'];

            $this->codpago = $data['codpago'];
            $this->totalpago = floatval($data['totalpago']);
            $this->codpago2 = $data['codpago2'];
            $this->totalpago2 = floatval($data['totalpago2']);

            $this->numero2 = $data['numero2'];
            $this->observaciones = $data['observaciones'];
            $this->codpais = $data['codpais'];
            $this->codpostal = $data['codpostal'];
            $this->direccion = $data['direccion'];
            $this->fecha = date('d-m-Y', strtotime($data['fecha']));
            $this->hora = $data['hora'];
            $this->neto = floatval($data['neto']);
            $this->nombrecliente = $data['nombrecliente'];
            $this->provincia = $data['provincia'];
            $this->total = floatval($data['total']);
            $this->totaliva = floatval($data['totaliva']);
            $this->ultentregado = floatval($data['ultentregado']);
            $this->ultcambio = floatval($data['ultcambio']);
        } else {
            $this->idtpv_comanda = NULL;
            $this->idtpv_arqueo = NULL;
            $this->idfactura = NULL;
            $this->cifnif = NULL;
            $this->ciudad = NULL;
            $this->codalmacen = NULL;
            $this->codcliente = NULL;
            $this->coddir = NULL;

            $this->codpago = NULL;
            $this->totalpago = 0;
            $this->codpago2 = NULL;
            $this->totalpago2 = 0;

            $this->numero2 = NULL;
            $this->observaciones = NULL;
            $this->codpais = NULL;
            $this->codpostal = NULL;
            $this->direccion = NULL;
            $this->fecha = date('d-m-Y');
            $this->hora = date('H:i:s');
            $this->neto = 0;
            $this->nombrecliente = NULL;
            $this->provincia = NULL;
            $this->total = 0;
            $this->totaliva = 0;
            $this->ultentregado = 0;
            $this->ultcambio = 0;
        }
    }

    protected function install()
    {
        return '';
    }

    public function url()
    {
        if ($this->idfactura) {
            return 'index.php?page=ventas_factura&id=' . $this->idfactura;
        }

        return 'index.php?page=tpv_caja&arqueo=' . $this->idtpv_arqueo;
    }

    public function get_lineas()
    {
        $lineac = new linea_comanda();
        return $lineac->all_from_comanda($this->idtpv_comanda);
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM tpv_comandas WHERE idtpv_comanda = " . $this->var2str($id) . ";");
        if ($data) {
            return new tpv_comanda($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->idtpv_comanda)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM tpv_comandas WHERE idtpv_comanda = " . $this->var2str($this->idtpv_comanda) . ";");
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE tpv_comandas SET idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo)
                . ", idfactura = " . $this->var2str($this->idfactura)
                . ", cifnif = " . $this->var2str($this->cifnif)
                . ", ciudad = " . $this->var2str($this->ciudad)
                . ", codalmacen = " . $this->var2str($this->codalmacen)
                . ", codcliente = " . $this->var2str($this->codcliente)
                . ", coddir = " . $this->var2str($this->coddir)
                . ", codpago = " . $this->var2str($this->codpago)
                . ", totalpago = " . $this->var2str($this->totalpago)
                . ", codpago2 = " . $this->var2str($this->codpago2)
                . ", totalpago2 = " . $this->var2str($this->totalpago2)
                . ", numero2 = " . $this->var2str($this->numero2)
                . ", observaciones = " . $this->var2str($this->observaciones)
                . ", codpais = " . $this->var2str($this->codpais)
                . ", codpostal = " . $this->var2str($this->codpostal)
                . ", direccion = " . $this->var2str($this->direccion)
                . ", fecha = " . $this->var2str($this->fecha)
                . ", hora = " . $this->var2str($this->hora)
                . ", neto = " . $this->var2str($this->neto)
                . ", nombrecliente = " . $this->var2str($this->nombrecliente)
                . ", provincia = " . $this->var2str($this->provincia)
                . ", total = " . $this->var2str($this->total)
                . ", totaliva = " . $this->var2str($this->totaliva)
                . ", ultentregado = " . $this->var2str($this->ultentregado)
                . ", ultcambio = " . $this->var2str($this->ultcambio)
                . " WHERE idtpv_comanda = " . $this->var2str($this->idtpv_comanda) . ";";

            return $this->db->exec($sql);
        }

        $sql = "INSERT INTO tpv_comandas (idtpv_arqueo,idfactura,cifnif,ciudad,codalmacen,"
            . "codcliente,coddir,codpago,totalpago,codpago2,totalpago2,codpais,codpostal,"
            . "direccion,fecha,hora,neto,nombrecliente,provincia,total,totaliva,"
            . "ultentregado,ultcambio,numero2,observaciones) VALUES (" .
            $this->var2str($this->idtpv_arqueo) . "," .
            $this->var2str($this->idfactura) . "," .
            $this->var2str($this->cifnif) . "," .
            $this->var2str($this->ciudad) . "," .
            $this->var2str($this->codalmacen) . "," .
            $this->var2str($this->codcliente) . "," .
            $this->var2str($this->coddir) . "," .
            $this->var2str($this->codpago) . "," .
            $this->var2str($this->totalpago) . "," .
            $this->var2str($this->codpago2) . "," .
            $this->var2str($this->totalpago2) . "," .
            $this->var2str($this->codpais) . "," .
            $this->var2str($this->codpostal) . "," .
            $this->var2str($this->direccion) . "," .
            $this->var2str($this->fecha) . "," .
            $this->var2str($this->hora) . "," .
            $this->var2str($this->neto) . "," .
            $this->var2str($this->nombrecliente) . "," .
            $this->var2str($this->provincia) . "," .
            $this->var2str($this->total) . "," .
            $this->var2str($this->totaliva) . "," .
            $this->var2str($this->ultentregado) . "," .
            $this->var2str($this->ultcambio) . "," .
            $this->var2str($this->numero2) . "," .
            $this->var2str($this->observaciones) . ");";

        if ($this->db->exec($sql)) {
            $this->idtpv_comanda = $this->db->lastval();
            return TRUE;
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM tpv_comandas WHERE idtpv_comanda = " . $this->var2str($this->idtpv_comanda) . ";");
    }

    public function all($offset = 0, $limit = FS_ITEM_LIMIT)
    {
        $clist = array();
        $sql = "SELECT * FROM tpv_comandas ORDER BY idtpv_comanda DESC";

        $data = $this->db->select_limit($sql, $limit, $offset);
        if ($data) {
            foreach ($data as $d) {
                $clist[] = new tpv_comanda($d);
            }
        }

        return $clist;
    }

    public function all_from_arqueo($ida)
    {
        $clist = array();
        $sql = "SELECT * FROM tpv_comandas WHERE idtpv_arqueo = " . $this->var2str($ida)
            . " ORDER BY idtpv_comanda DESC;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $clist[] = new tpv_comanda($d);
            }
        }

        return $clist;
    }

    public function all_desde($desde, $hasta)
    {
        $clist = array();
        $sql = "SELECT * FROM " . $this->table_name .
            " WHERE fecha >= " . $this->var2str($desde) . " AND fecha <= " . $this->var2str($hasta) .
            " ORDER BY idtpv_comanda ASC;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $clist[] = new tpv_comanda($d);
            }
        }

        return $clist;
    }
}
