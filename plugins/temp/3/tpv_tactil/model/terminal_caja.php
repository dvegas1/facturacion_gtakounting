<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos Garcia Gomez  neorazorx@gmail.com
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */
require_once 'plugins/facturacion_base/model/core/terminal_caja.php';

/**
 * Description of terminal_caja
 *
 * @author carlos
 */
class terminal_caja extends FacturaScripts\model\terminal_caja
{

    public $nombre;
    public $comandologo;
    public $cambiar_agente;
    public $forzar_pin;

    public function __construct($data = FALSE)
    {
        parent::__construct($data);
        if ($data) {
            $this->nombre = $data['nombre'];
            if ($this->nombre == '') {
                $this->nombre = 'Terminal ' . $this->id;
            }

            $this->comandologo = '';
            if (isset($data['comandologo'])) {
                $this->comandologo = $data['comandologo'];
            }

            $this->cambiar_agente = $this->str2bool($data['cambiar_agente']);
            $this->forzar_pin = $this->str2bool($data['forzar_pin']);
        } else {
            $this->nombre = NULL;
            $this->comandologo = '';
            $this->cambiar_agente = TRUE;
            $this->forzar_pin = FALSE;
        }
    }

    public function disponible()
    {
        if ($this->db->select("SELECT * FROM tpv_arqueos WHERE abierta AND idterminal = " . $this->var2str($this->id) . ";")) {
            return FALSE;
        }

        return TRUE;
    }

    public function logo()
    {
        if (!$this->sin_comandos && strlen($this->comandologo) > 1) {
            $aux = explode('.', $this->comandologo);
            if ($aux) {
                foreach ($aux as $a) {
                    $this->tickets .= chr($a);
                }
            }
            $this->tickets .= "\n";
        }
    }

    public function save()
    {
        if (parent::save()) {
            $sql = "UPDATE cajas_terminales SET nombre = " . $this->var2str($this->nombre) .
                ", comandologo = " . $this->var2str($this->comandologo) .
                ", cambiar_agente = " . $this->var2str($this->cambiar_agente) .
                ", forzar_pin = " . $this->var2str($this->forzar_pin) .
                "  WHERE id = " . $this->var2str($this->id) . ";";

            return $this->db->exec($sql);
        }

        return FALSE;
    }

    public function disponibles()
    {
        $tlist = array();

        $sql = "SELECT * FROM cajas_terminales WHERE id NOT IN "
            . "(SELECT idterminal as id FROM tpv_arqueos WHERE abierta AND idterminal IS NOT NULL)"
            . " ORDER BY id ASC;";
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $tlist[] = new \terminal_caja($d);
            }
        }

        return $tlist;
    }

    /**
     * A partir de una factura añade un ticket a la cola de impresión de este terminal.
     * @param \factura_cliente $factura
     * @param \empresa $empresa
     * @param type $imprimir_descripciones
     * @param type $imprimir_observaciones
     */
    public function imprimir_ticket_tactil(&$factura, &$empresa, $efectivo = NULL, $tarjeta = NULL, $cambio = NULL, $texto_fin = '')
    {
        $this->logo();

        $linea_iguales = '';
        for ($i = 0; $i < $this->anchopapel; $i++) {
            $linea_iguales .= '=';
        }
        $this->add_linea($linea_iguales . "\n");

        $this->add_linea($this->sanitize($empresa->nombrecorto) . "\n");
        $this->add_linea(
            $this->sanitize($empresa->direccion) . "\nCP: " . $empresa->codpostal
            . ' - ' . $this->sanitize($empresa->ciudad) . "\n"
        );

        if ($empresa->telefono) {
            $this->add_linea('Tlf: ' . $empresa->telefono . "\n\n");
        }

        $this->add_linea($this->sanitize($empresa->nombre) . '  ');
        $this->add_linea(FS_CIFNIF . ': ' . $empresa->cifnif . "\n");

        $this->add_linea($linea_iguales . "\n");

        $this->add_linea(
            $this->center_text(ucfirst(FS_FACTURA_SIMPLIFICADA) . ' ' . $factura->codigo) . "\n" .
            $this->center_text($factura->fecha . ' ' . $factura->hora) . "\n" .
            $this->center_text($this->nombre) . "\n\n"
        );

        $this->add_linea("Cliente: " . $this->sanitize($factura->nombrecliente) . "\n");
        if ($factura->cifnif) {
            $this->add_linea(FS_CIFNIF . $factura->cifnif . "\n");
        }
        if ($factura->direccion) {
            $direccion = $factura->direccion;
            if ($factura->codpostal) {
                $direccion .= ' - CP: ' . $factura->codpostal;
            }
            if ($factura->ciudad) {
                $direccion .= ' ' . $factura->ciudad;
            }
            if ($factura->provincia) {
                $direccion .= ' (' . $factura->provincia . ')';
            }

            $this->add_linea("Direccion: " . $direccion . "\n");
        }
        $this->add_linea("\n");

        $width = $this->anchopapel - 15;
        $this->add_linea(
            sprintf("%3s", "Ud.") . " " .
            sprintf("%-" . $width . "s", "Articulo") . " " .
            sprintf("%10s", "TOTAL") . "\n"
        );
        $this->add_linea(
            sprintf("%3s", "---") . " " .
            sprintf("%-" . $width . "s", substr("--------------------------------------------------------", 0, $width - 1)) . " " .
            sprintf("%10s", "----------") . "\n"
        );
        $impuestos = array();
        $totales = array();
        $num_articulos = 0;
        foreach ($factura->get_lineas() as $col) {
            /// desglosamos las bases y el iva
            if (!in_array($col->iva, $impuestos)) {
                $impuestos[] = $col->iva;
                $totales[$col->iva] = array(
                    'neto' => $col->pvptotal,
                    'iva' => $col->iva,
                    'totaliva' => $col->pvptotal * $col->iva / 100,
                    're' => $col->recargo,
                    'totalre' => $col->pvptotal * $col->recargo / 100,
                );
            } else {
                $totales[$col->iva]['neto'] += $col->pvptotal;
                $totales[$col->iva]['totaliva'] += $col->pvptotal * $col->iva / 100;
                $totales[$col->iva]['totalre'] += $col->pvptotal * $col->recargo / 100;
            }

            $num_articulos += $col->cantidad;

            $this->add_linea(
                sprintf("%3s", $col->cantidad) . " " . $this->sanitize($col->referencia) . ":\n"
            );
            $this->add_linea(
                '    ' . substr($this->sanitize($col->descripcion), 0, $this->anchopapel - 4) . "\n"
            );
            $this->add_linea(
                '    ' . sprintf("%-" . $width . "s", 'PVP: ' . $this->show_numero($col->pvpunitario * (100 + $col->iva) / 100)) . " " .
                sprintf("%10s", $this->show_numero($col->total_iva())) . "\n"
            );
        }

        $this->add_linea($linea_iguales . "\n");
        $this->add_linea(
            'TOTAL A PAGAR: ' . sprintf("%" . ($this->anchopapel - 15) . "s", $this->show_precio($factura->total, $factura->coddivisa)) . "\n"
        );
        $this->add_linea($linea_iguales . "\n");

        $entregado = 0;
        if (!is_null($efectivo)) {
            $entregado = floatval($efectivo);
            $this->add_linea(
                'Efectivo: ' .
                sprintf('%' . ($this->anchopapel - 10) . 's', $this->show_precio($entregado, $factura->coddivisa)) .
                "\n"
            );
        }

        if (!is_null($tarjeta)) {
            $this->add_linea(
                "Tarjeta: " .
                sprintf('%' . ($this->anchopapel - 9) . 's', $this->show_precio(floatval($tarjeta), $factura->coddivisa)) .
                "\n"
            );
            $entregado += floatval($tarjeta);
        }

        if (!is_null($cambio)) {
            $this->add_linea(
                "Total entregado: " .
                sprintf('%' . ($this->anchopapel - 17) . 's', $this->show_precio($entregado, $factura->coddivisa)) .
                "\n"
            );
            $this->add_linea(
                "Cambio: " .
                sprintf('%' . ($this->anchopapel - 8) . 's', $this->show_precio(floatval($cambio), $factura->coddivisa)) .
                "\n"
            );
        }

        $agente = new agente();
        $age0 = $agente->get($factura->codagente);
        if ($age0) {
            $this->add_linea(
                "Le atendio: " .
                sprintf('%' . ($this->anchopapel - 12) . 's', $age0->nombre) .
                "\n"
            );
        }

        $this->add_linea(
            "Num. de articulos: " .
            sprintf('%' . ($this->anchopapel - 19) . 's', $this->show_numero($num_articulos, 0)) .
            "\n"
        );

        $this->add_linea($linea_iguales . "\n");

        /// imprimimos los impuestos desglosados
        $this->add_linea(
            'TIPO   BASE    ' . FS_IVA . '    RE' .
            sprintf('%' . ($this->anchopapel - 24) . 's', 'TOTAL') .
            "\n"
        );
        foreach ($impuestos as $imp) {
            $total = $totales[$imp]['neto'] + $totales[$imp]['totaliva'] + $totales[$imp]['totalre'];
            $this->add_linea(
                sprintf("%-6s", $imp . '%') . ' ' .
                sprintf("%-7s", $this->show_numero($totales[$imp]['neto'])) . ' ' .
                sprintf("%-6s", $this->show_numero($totales[$imp]['totaliva'])) . ' ' .
                sprintf("%-6s", $this->show_numero($totales[$imp]['totalre'])) . ' ' .
                sprintf('%' . ($this->anchopapel - 29) . 's', $this->show_numero($total)) .
                "\n"
            );
        }

        $this->add_linea(
            "\n\n" .
            $this->center_text('** Gracias por su visita **') .
            "\n\n" .
            $this->center_text($this->sanitize($texto_fin))
        );

        $this->add_linea("\n\n\n\n\n\n\n");
        $this->cortar_papel();
    }

    public function imprimir_ticket_tactil_regalo(&$factura, &$empresa, $texto_fin = '')
    {
        $this->logo();

        $linea_iguales = '';
        for ($i = 0; $i < $this->anchopapel; $i++) {
            $linea_iguales .= '=';
        }
        $this->add_linea($linea_iguales . "\n");

        $this->add_linea($this->sanitize($empresa->nombrecorto) . "\n");
        $this->add_linea(
            $this->sanitize($empresa->direccion) . "\nCP: " . $empresa->codpostal
            . ' - ' . $this->sanitize($empresa->ciudad) . "\n"
        );

        if ($empresa->telefono) {
            $this->add_linea('Tlf: ' . $empresa->telefono . "\n\n");
        }

        $this->add_linea($this->sanitize($empresa->nombre) . '  ');
        $this->add_linea(FS_CIFNIF . ': ' . $empresa->cifnif . "\n");

        $this->add_linea($linea_iguales . "\n");

        $this->add_linea(
            $this->center_text(ucfirst(FS_FACTURA_SIMPLIFICADA) . ' ' . $factura->codigo) . "\n" .
            $this->center_text($factura->fecha . ' ' . $factura->hora) . "\n" .
            $this->center_text($this->nombre) . "\n\n"
        );

        $width = $this->anchopapel - 15;
        $this->add_linea(
            sprintf("%3s", "Ud.") . " " .
            sprintf("%-" . $width . "s", "Articulo") . " " .
            sprintf("%10s", "TOTAL") . "\n"
        );
        $this->add_linea(
            sprintf("%3s", "---") . " " .
            sprintf("%-" . $width . "s", substr("--------------------------------------------------------", 0, $width - 1)) . " " .
            sprintf("%10s", "----------") . "\n"
        );
        foreach ($factura->get_lineas() as $col) {
            $this->add_linea(
                sprintf("%3s", $col->cantidad) . " " . $this->sanitize($col->referencia) . ":\n"
            );
            $this->add_linea(
                '    ' . substr($this->sanitize($col->descripcion), 0, $this->anchopapel - 4) . "\n"
            );
            $this->add_linea(
                '    ' . sprintf("%-" . $width . "s", '-') . " " . sprintf("%10s", '-') . "\n"
            );
        }

        $agente = new agente();
        $age0 = $agente->get($factura->codagente);
        if ($age0) {
            $this->add_linea(
                "Le atendio: " .
                sprintf('%' . ($this->anchopapel - 12) . 's', $age0->nombre) .
                "\n"
            );
        }

        $this->add_linea($linea_iguales . "\n");
        $this->add_linea($this->center_text('TICKET REGALO') . "\n");
        $this->add_linea($linea_iguales . "\n");

        $this->add_linea(
            "\n\n" .
            $this->center_text('** Gracias por su visita **') .
            "\n\n" .
            $this->center_text($this->sanitize($texto_fin))
        );

        $this->add_linea("\n\n\n\n\n\n\n");
        $this->cortar_papel();
    }

    /**
     * A partir de una comanda añade un ticket de preimpresión a la cola de impresión.
     * @param tpv_comanda $comanda
     * @param empresa $empresa
     */
    public function preimprimir_ticket_tactil(&$comanda, &$empresa)
    {
        $this->logo();

        $linea_iguales = '';
        for ($i = 0; $i < $this->anchopapel; $i++) {
            $linea_iguales .= '=';
        }
        $this->add_linea($linea_iguales . "\n");
        $this->add_linea($this->center_text('PRE-IMPRESION') . "\n");
        $this->add_linea($this->center_text('NO ES UNA FACTURA') . "\n");
        $this->add_linea($linea_iguales . "\n");

        $width = $this->anchopapel - 15;
        $this->add_linea(
            sprintf("%3s", "Ud.") . " " .
            sprintf("%-" . $width . "s", "Articulo") . " " .
            sprintf("%10s", "TOTAL") . "\n"
        );
        $this->add_linea(
            sprintf("%3s", "---") . " " .
            sprintf("%-" . $width . "s", substr("--------------------------------------------------------", 0, $width - 1)) . " " .
            sprintf("%10s", "----------") . "\n"
        );
        $impuestos = array();
        $totales = array();
        $num_articulos = 0;
        foreach ($comanda->get_lineas() as $col) {
            /// desglosamos las bases y el iva
            if (!in_array($col->iva, $impuestos)) {
                $impuestos[] = $col->iva;
                $totales[$col->iva] = array(
                    'neto' => $col->pvptotal,
                    'iva' => $col->iva,
                    'totaliva' => $col->pvptotal * $col->iva / 100,
                    're' => $col->recargo,
                    'totalre' => $col->pvptotal * $col->recargo / 100,
                );
            } else {
                $totales[$col->iva]['neto'] += $col->pvptotal;
                $totales[$col->iva]['totaliva'] += $col->pvptotal * $col->iva / 100;
                $totales[$col->iva]['totalre'] += $col->pvptotal * $col->recargo / 100;
            }

            $num_articulos += $col->cantidad;

            $this->add_linea(
                sprintf("%3s", $col->cantidad) . " " . $this->sanitize($col->referencia) . ":\n"
            );
            $this->add_linea(
                '    ' . substr($this->sanitize($col->descripcion), 0, $this->anchopapel - 4) . "\n"
            );
            $this->add_linea(
                '    ' . sprintf("%-" . $width . "s", 'PVP: ' . $this->show_numero($col->pvpunitario * (100 + $col->iva) / 100)) . " " .
                sprintf("%10s", $this->show_numero($col->total_iva())) . "\n"
            );
        }

        $this->add_linea($linea_iguales . "\n");
        $this->add_linea(
            'TOTAL A PAGAR: ' . sprintf("%" . ($this->anchopapel - 15) . "s", $this->show_precio($comanda->total, $empresa->coddivisa))
        );
        $this->add_linea($linea_iguales . "\n");

        $this->add_linea("\n\n\n\n\n\n\n");
        $this->cortar_papel();
    }
}
