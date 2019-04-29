<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of tpv_arqueo
 *
 * @author carlos
 */
class tpv_arqueo extends fs_model
{

    public $abierta;
    public $b5;
    public $b10;
    public $b20;
    public $b50;
    public $b100;
    public $b200;
    public $b500;
    public $b1000;
    public $b2000;
    public $b5000;
    public $b10000;
    public $b20000;
    public $b50000;
    public $b100000;
    public $diadesde;
    public $diahasta;
    public $idasiento;
    public $idtpv_arqueo;
    public $inicio;
    public $m001;
    public $m002;
    public $m005;
    public $m010;
    public $m020;
    public $m050;
    public $m1;
    public $m2;
    public $m50;
    public $m100;
    public $m200;
    public $m500;
    public $m1000;
    public $nogenerarasiento;
    public $ptoventa;
    public $sacadodecaja;
    public $totalcaja;
    public $totalmov;
    public $totaltarjeta;
    public $totalvale;
    public $idterminal;
    public $codagente;
    public $agente;
    private static $agentes;

    public function __construct($data = FALSE)
    {
        parent::__construct('tpv_arqueos');

        if (!isset(self::$agentes)) {
            self::$agentes = array();
        }

        if ($data) {
            $this->abierta = $this->str2bool($data['abierta']);
            $this->b5 = intval($data['b5']);
            $this->b10 = intval($data['b10']);
            $this->b20 = intval($data['b20']);
            $this->b50 = intval($data['b50']);
            $this->b100 = intval($data['b100']);
            $this->b200 = intval($data['b200']);
            $this->b500 = intval($data['b500']);
            $this->b1000 = intval($data['b1000']);
            $this->b2000 = intval($data['b2000']);
            $this->b5000 = intval($data['b5000']);
            $this->b10000 = intval($data['b10000']);
            $this->b20000 = intval($data['b20000']);
            $this->b50000 = intval($data['b50000']);
            $this->b100000 = intval($data['b100000']);
            $this->diadesde = date('d-m-Y', strtotime($data['diadesde']));

            $this->diahasta = NULL;
            if (!is_null($data['diahasta'])) {
                $this->diahasta = date('d-m-Y', strtotime($data['diahasta']));
            }

            $this->idasiento = $this->intval($data['idasiento']);
            $this->idtpv_arqueo = $data['idtpv_arqueo'];
            $this->inicio = floatval($data['inicio']);
            $this->m001 = intval($data['m001']);
            $this->m002 = intval($data['m002']);
            $this->m005 = intval($data['m005']);
            $this->m010 = intval($data['m010']);
            $this->m020 = intval($data['m020']);
            $this->m050 = intval($data['m050']);
            $this->m1 = intval($data['m1']);
            $this->m2 = intval($data['m2']);
            $this->m50 = intval($data['m50']);
            $this->m100 = intval($data['m100']);
            $this->m200 = intval($data['m200']);
            $this->m500 = intval($data['m500']);
            $this->m1000 = intval($data['m1000']);
            $this->nogenerarasiento = $this->str2bool($data['nogenerarasiento']);
            $this->ptoventa = $data['ptoventa'];
            $this->sacadodecaja = floatval($data['sacadodecaja']);
            $this->totalcaja = floatval($data['totalcaja']);
            $this->totalmov = floatval($data['totalmov']);
            $this->totaltarjeta = floatval($data['totaltarjeta']);
            $this->totalvale = floatval($data['totalvale']);

            $this->idterminal = $this->intval($data['idterminal']);

            $this->codagente = NULL;
            if (!is_null($data['codagente'])) {
                $this->codagente = $data['codagente'];
                foreach (self::$agentes as $ag) {
                    if ($ag->codagente == $this->codagente) {
                        $this->agente = $ag;
                        break;
                    }
                }

                if (!isset($this->agente)) {
                    $ag = new agente();
                    $this->agente = $ag->get($this->codagente);
                    self::$agentes[] = $this->agente;
                }
            }
        } else {
            $this->abierta = TRUE;
            $this->b5 = 0;
            $this->b10 = 0;
            $this->b20 = 0;
            $this->b50 = 0;
            $this->b100 = 0;
            $this->b200 = 0;
            $this->b500 = 0;
            $this->b1000 = 0;
            $this->b2000 = 0;
            $this->b5000 = 0;
            $this->b10000 = 0;
            $this->b20000 = 0;
            $this->b50000 = 0;
            $this->b100000 = 0;
            $this->diadesde = date('d-m-Y');
            $this->diahasta = NULL;
            $this->idasiento = NULL;
            $this->idtpv_arqueo = NULL;
            $this->inicio = 0;
            $this->m001 = 0;
            $this->m002 = 0;
            $this->m005 = 0;
            $this->m010 = 0;
            $this->m020 = 0;
            $this->m050 = 0;
            $this->m1 = 0;
            $this->m2 = 0;
            $this->nogenerarasiento = FALSE;
            $this->ptoventa = NULL;
            $this->sacadodecaja = 0;
            $this->totalcaja = 0;
            $this->totalmov = 0;
            $this->totaltarjeta = 0;
            $this->totalvale = 0;

            $this->idterminal = NULL;
            $this->codagente = NULL;
            $this->agente = NULL;
        }
    }

    protected function install()
    {
        return '';
    }

    public function url()
    {
        if (is_null($this->idtpv_arqueo)) {
            return 'index.php?page=tpv_caja';
        }

        return 'index.php?page=tpv_caja&arqueo=' . $this->idtpv_arqueo;
    }

    public function total_contado()
    {
        $total = $this->m001 * 0.01;
        $total += $this->m002 * 0.02;
        $total += $this->m005 * 0.05;
        $total += $this->m010 * 0.10;
        $total += $this->m020 * 0.20;
        $total += $this->m050 * 0.50;
        $total += $this->m1;
        $total += $this->m2 * 2;
        $total += $this->m50 * 50;
        $total += $this->m100 * 100;
        $total += $this->m200 * 200;
        $total += $this->m500 * 500;
        $total += $this->m1000 * 1000;
        $total += $this->b5 * 5;
        $total += $this->b10 * 10;
        $total += $this->b20 * 20;
        $total += $this->b50 * 50;
        $total += $this->b100 * 100;
        $total += $this->b200 * 200;
        $total += $this->b500 * 500;
        $total += $this->b1000 * 1000;
        $total += $this->b2000 * 2000;
        $total += $this->b5000 * 5000;
        $total += $this->b10000 * 10000;
        $total += $this->b20000 * 20000;
        $total += $this->b50000 * 50000;
        $total += $this->b100000 * 100000;

        return $total;
    }

    public function num_tickets()
    {
        $sql = "SELECT COUNT(*) as num FROM tpv_comandas WHERE idfactura AND idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo);
        $data = $this->db->select($sql);
        if ($data) {
            return intval($data[0]['num']);
        }

        return 0;
    }

    public function num_articulos()
    {
        $sql = "SELECT DISTINCT referencia FROM tpv_lineascomanda WHERE idtpv_comanda IN "
            . "(SELECT idtpv_comanda FROM tpv_comandas WHERE idfactura AND idtpv_arqueo = "
            . $this->var2str($this->idtpv_arqueo) . ');';

        $data = $this->db->select($sql);
        if ($data) {
            return count($data);
        }

        return 0;
    }

    public function count_articulos()
    {
        $sql = "SELECT SUM(cantidad) as cantidad FROM tpv_lineascomanda WHERE idtpv_comanda IN "
            . "(SELECT idtpv_comanda FROM tpv_comandas WHERE idfactura AND idtpv_arqueo = "
            . $this->var2str($this->idtpv_arqueo) . ');';

        $data = $this->db->select($sql);
        if ($data) {
            return floatval($data[0]['cantidad']);
        }

        return 0;
    }

    public function desglose_formas_pago()
    {
        $lista = array();

        $sql = "SELECT codpago,SUM(totalpago) as total FROM tpv_comandas WHERE idfactura IS NOT NULL AND idtpv_arqueo = "
            . $this->var2str($this->idtpv_arqueo) . " GROUP BY codpago ORDER BY total DESC;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[$d['codpago']] = floatval($d['total']);
            }
        }

        $sql = "SELECT codpago2,SUM(totalpago2) as total FROM tpv_comandas WHERE idfactura IS NOT NULL AND idtpv_arqueo = "
            . $this->var2str($this->idtpv_arqueo) . " GROUP BY codpago2 ORDER BY total DESC;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                if (is_null($d['codpago2'])) {
                    /// nada
                } else if (isset($lista[$d['codpago2']])) {
                    $lista[$d['codpago2']] += floatval($d['total']);
                } else {
                    $lista[$d['codpago2']] = floatval($d['total']);
                }
            }
        }

        return $lista;
    }

    public function desglose_familias()
    {
        $lista = array();
        $sql = "SELECT a.codfamilia,l.referencia,l.cantidad FROM tpv_lineascomanda l, articulos a WHERE l.referencia = a.referencia"
            . " AND idtpv_comanda IN (SELECT idtpv_comanda FROM tpv_comandas"
            . " WHERE idfactura AND idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo) . ');';

        $data = $this->db->select($sql);
        if ($data) {
            $familia = new familia();

            foreach ($data as $d) {
                if (is_null($d['codfamilia'])) {
                    /// nada
                } else if (!isset($lista[$d['codfamilia']])) {
                    $lista[$d['codfamilia']] = array(
                        'nombre' => '-',
                        'cantidad' => floatval($d['cantidad'])
                    );

                    $fam = $familia->get($d['codfamilia']);
                    if ($fam) {
                        $lista[$d['codfamilia']]['nombre'] = $fam->descripcion;
                    }
                } else {
                    $lista[$d['codfamilia']]['cantidad'] += floatval($d['cantidad']);
                }
            }
        }

        return $lista;
    }

    public function monedas($codpais = 'ESP')
    {
        $monedas = array();

        switch ($codpais) {
            case 'COL':
                $monedas = array(
                    'm50' => array('valor' => 50, 'total' => $this->m50),
                    'm100' => array('valor' => 100, 'total' => $this->m100),
                    'm200' => array('valor' => 200, 'total' => $this->m200),
                    'm500' => array('valor' => 500, 'total' => $this->m500),
                    'm1000' => array('valor' => 1000, 'total' => $this->m1000),
                );
                break;

            default:
                $monedas = array(
                    'm001' => array('valor' => .01, 'total' => $this->m001),
                    'm002' => array('valor' => .02, 'total' => $this->m002),
                    'm005' => array('valor' => .05, 'total' => $this->m005),
                    'm010' => array('valor' => .1, 'total' => $this->m010),
                    'm020' => array('valor' => .2, 'total' => $this->m020),
                    'm050' => array('valor' => .5, 'total' => $this->m050),
                    'm1' => array('valor' => 1, 'total' => $this->m1),
                    'm2' => array('valor' => 2, 'total' => $this->m2),
                );
                break;
        }

        return $monedas;
    }

    public function billetes($codpais = 'ESP')
    {
        $billetes = array();

        switch ($codpais) {
            case 'COL':
                $billetes = array(
                    'b1000' => array('valor' => 1000, 'total' => $this->b1000),
                    'b2000' => array('valor' => 2000, 'total' => $this->b2000),
                    'b5000' => array('valor' => 5000, 'total' => $this->b5000),
                    'b10000' => array('valor' => 10000, 'total' => $this->b10000),
                    'b20000' => array('valor' => 20000, 'total' => $this->b20000),
                    'b50000' => array('valor' => 50000, 'total' => $this->b50000),
                    'b100000' => array('valor' => 100000, 'total' => $this->b100000),
                );
                break;

            default:
                $billetes = array(
                    'b5' => array('valor' => 5, 'total' => $this->b5),
                    'b10' => array('valor' => 10, 'total' => $this->b10),
                    'b20' => array('valor' => 20, 'total' => $this->b20),
                    'b50' => array('valor' => 50, 'total' => $this->b50),
                    'b100' => array('valor' => 100, 'total' => $this->b100),
                    'b200' => array('valor' => 200, 'total' => $this->b200),
                    'b500' => array('valor' => 500, 'total' => $this->b500),
                );
                break;
        }

        return $billetes;
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM tpv_arqueos WHERE idtpv_arqueo = " . $this->var2str($id) . ";");
        if ($data) {
            return new tpv_arqueo($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->idtpv_arqueo)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM tpv_arqueos WHERE idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo) . ";");
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE tpv_arqueos SET abierta = " . $this->var2str($this->abierta) .
                ", b5 = " . $this->var2str($this->b5) .
                ", b10 = " . $this->var2str($this->b10) .
                ", b20 = " . $this->var2str($this->b20) .
                ", b50 = " . $this->var2str($this->b50) .
                ", b100 = " . $this->var2str($this->b100) .
                ", b200 = " . $this->var2str($this->b200) .
                ", b500 = " . $this->var2str($this->b500) .
                ", b1000 = " . $this->var2str($this->b1000) .
                ", b2000 = " . $this->var2str($this->b2000) .
                ", b5000 = " . $this->var2str($this->b5000) .
                ", b10000 = " . $this->var2str($this->b10000) .
                ", b20000 = " . $this->var2str($this->b20000) .
                ", b50000 = " . $this->var2str($this->b50000) .
                ", b100000 = " . $this->var2str($this->b100000) .
                ", codagente = " . $this->var2str($this->codagente) .
                ", diadesde = " . $this->var2str($this->diadesde) .
                ", diahasta = " . $this->var2str($this->diahasta) .
                ", idasiento = " . $this->var2str($this->idasiento) .
                ", idterminal = " . $this->var2str($this->idterminal) .
                ", inicio = " . $this->var2str($this->inicio) .
                ", m001 = " . $this->var2str($this->m001) .
                ", m002 = " . $this->var2str($this->m002) .
                ", m005 = " . $this->var2str($this->m005) .
                ", m010 = " . $this->var2str($this->m010) .
                ", m020 = " . $this->var2str($this->m020) .
                ", m050 = " . $this->var2str($this->m050) .
                ", m1 = " . $this->var2str($this->m1) .
                ", m2 = " . $this->var2str($this->m2) .
                ", m50 = " . $this->var2str($this->m50) .
                ", m100 = " . $this->var2str($this->m100) .
                ", m200 = " . $this->var2str($this->m200) .
                ", m500 = " . $this->var2str($this->m500) .
                ", m1000 = " . $this->var2str($this->m1000) .
                ", nogenerarasiento = " . $this->var2str($this->nogenerarasiento) .
                ", ptoventa = " . $this->var2str($this->ptoventa) .
                ", sacadodecaja = " . $this->var2str($this->sacadodecaja) .
                ", totalcaja = " . $this->var2str($this->totalcaja) .
                ", totalmov = " . $this->var2str($this->totalmov) .
                ", totaltarjeta = " . $this->var2str($this->totaltarjeta) .
                ", totalvale = " . $this->var2str($this->totalvale) .
                "  WHERE idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo) . ";";

            return $this->db->exec($sql);
        }

        $this->idtpv_arqueo = '00000001';
        $sql = "SELECT MAX(" . $this->db->sql_to_int('idtpv_arqueo') . ") as id FROM " . $this->table_name . ";";
        $data = $this->db->select($sql);
        if ($data) {
            $this->idtpv_arqueo = sprintf('%08s', 1 + intval($data[0]['id']));
        }

        $sql = "INSERT INTO tpv_arqueos (abierta,codagente,diadesde,diahasta,idasiento,idterminal,inicio,totalcaja,"
            . "nogenerarasiento,b5,b10,b20,b50,b100,b200,b500,b1000,b2000,b5000,b10000,b20000,b50000,b100000,"
            . "m001,m002,m005,m010,m020,m050,m1,m2,m50,m100,m200,m500,m1000,idtpv_arqueo)"
            . " VALUES (" . $this->var2str($this->abierta) .
            "," . $this->var2str($this->codagente) .
            "," . $this->var2str($this->diadesde) .
            "," . $this->var2str($this->diahasta) .
            "," . $this->var2str($this->idasiento) .
            "," . $this->var2str($this->idterminal) .
            "," . $this->var2str($this->inicio) .
            "," . $this->var2str($this->totalcaja) .
            "," . $this->var2str($this->nogenerarasiento) .
            "," . $this->var2str($this->b5) .
            "," . $this->var2str($this->b10) .
            "," . $this->var2str($this->b20) .
            "," . $this->var2str($this->b50) .
            "," . $this->var2str($this->b100) .
            "," . $this->var2str($this->b200) .
            "," . $this->var2str($this->b500) .
            "," . $this->var2str($this->b1000) .
            "," . $this->var2str($this->b2000) .
            "," . $this->var2str($this->b5000) .
            "," . $this->var2str($this->b10000) .
            "," . $this->var2str($this->b20000) .
            "," . $this->var2str($this->b50000) .
            "," . $this->var2str($this->b100000) .
            "," . $this->var2str($this->m001) .
            "," . $this->var2str($this->m002) .
            "," . $this->var2str($this->m005) .
            "," . $this->var2str($this->m010) .
            "," . $this->var2str($this->m020) .
            "," . $this->var2str($this->m050) .
            "," . $this->var2str($this->m1) .
            "," . $this->var2str($this->m2) .
            "," . $this->var2str($this->m50) .
            "," . $this->var2str($this->m100) .
            "," . $this->var2str($this->m200) .
            "," . $this->var2str($this->m500) .
            "," . $this->var2str($this->m1000) .
            "," . $this->var2str($this->idtpv_arqueo) . ");";

        return $this->db->exec($sql);
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM tpv_arqueos WHERE idtpv_arqueo = " . $this->var2str($this->idtpv_arqueo) . ";");
    }

    public function all($offset = 0)
    {
        $alist = array();

        $data = $this->db->select_limit("SELECT * FROM tpv_arqueos ORDER BY idtpv_arqueo DESC", FS_ITEM_LIMIT, $offset);
        if ($data) {
            foreach ($data as $d) {
                $alist[] = new tpv_arqueo($d);
            }
        }

        return $alist;
    }

    public function total_arqueos()
    {
        $total = 0;

        $data = $this->db->select("SELECT COUNT(idtpv_arqueo) as total FROM tpv_arqueos;");
        if ($data) {
            $total = intval($data[0]['total']);
        }

        return $total;
    }

    public function all_by_agente($codagente, $offset = 0, $limit = FS_ITEM_LIMIT)
    {
        $alist = array();
        $sql = "SELECT * FROM " . $this->table_name . " WHERE codagente = " .
            $this->var2str($codagente) . " ORDER BY idtpv_arqueo DESC";

        $data = $this->db->select_limit($sql, $limit, $offset);
        if ($data) {
            foreach ($data as $d) {
                $alist[] = new tpv_arqueo($d);
            }
        }

        return $alist;
    }
}
