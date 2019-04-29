<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017  Carlos Garcia Gomez  neorazorx@gmail.com
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */
require_once 'plugins/facturacion_base/extras/fbase_controller.php';

class tpv_caja extends fbase_controller
{

    public $almacen;
    public $arqueo;
    public $familia;
    public $forma_pago;
    private $fsvar;
    public $movimientos;
    public $offset;
    public $resultados;
    public $serie;
    public $terminal;
    public $terminales;
    public $total_arqueos;
    public $tpv_config;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Arqueos y configuración', 'TPV', FALSE, TRUE);
    }

    protected function private_core()
    {
        parent::private_core();

        $this->almacen = new almacen();
        $this->arqueo = FALSE;
        $this->familia = new familia();
        $this->forma_pago = new forma_pago();
        $this->fsvar = new fs_var();
        $this->movimientos = array();
        $this->serie = new serie();
        $this->terminal = new terminal_caja();
        $terminal = new terminal_caja();
        $tpv_arqueo = new tpv_arqueo();

        $this->cargar_config();

        if (isset($_REQUEST['buscar_referencia'])) {
            $this->buscar_referencia();
        } else if (isset($_POST['nuevot'])) {
            $this->nuevo_terminal();
        } else if (isset($_POST['idt'])) {
            $this->editar_terminal();
        } else if (isset($_GET['deletet'])) {
            $this->eliminar_terminal();
        } else if (isset($_GET['delete'])) {
            $this->eliminar_arqueo();
        } else if (isset($_GET['cerrar'])) {
            $this->cerrar_arqueo();
        } else if (isset($_REQUEST['arqueo'])) {
            /// Ver un arqueo
            $this->arqueo = $tpv_arqueo->get($_REQUEST['arqueo']);
        } else if (isset($_POST['tpv_config'])) {
            $this->guardar_config();
        }

        $this->offset = 0;
        if (isset($_GET['offset'])) {
            $this->offset = intval($_GET['offset']);
        }

        if ($this->arqueo) {
            $this->template = 'tpv_arqueo';

            $comanda = new tpv_comanda();
            $this->resultados = $comanda->all_from_arqueo($this->arqueo->idtpv_arqueo);

            $movimiento = new tpv_movimiento();
            $this->movimientos = $movimiento->all_from_arqueo($this->arqueo->idtpv_arqueo);
        } else {
            $this->total_arqueos = $tpv_arqueo->total_arqueos();
            $this->resultados = $tpv_arqueo->all($this->offset);
            $this->terminales = $terminal->all();
        }
    }

    private function cargar_config()
    {
        $this->tpv_config = array(
            'tpv_ref_varios' => '',
            'tpv_linea_libre' => 1,
            'tpv_familias' => FALSE,
            'tpv_volver_familias' => FALSE,
            'tpv_fpago_efectivo' => FALSE,
            'tpv_fpago_tarjeta' => FALSE,
            'tpv_texto_fin' => '',
            'tpv_preimprimir' => FALSE,
            'tpv_emails_z' => '',
        );
        $this->tpv_config = $this->fsvar->array_get($this->tpv_config, FALSE);
    }

    private function guardar_config()
    {
        $this->tpv_config['tpv_ref_varios'] = $_POST['ref_varios'];
        $this->tpv_config['tpv_linea_libre'] = isset($_POST['linea_libre']) ? 1 : 0;
        $this->tpv_config['tpv_volver_familias'] = isset($_POST['volver_familias']) ? 1 : 0;

        $this->tpv_config['tpv_familias'] = FALSE;
        if (isset($_POST['familia'])) {
            $this->tpv_config['tpv_familias'] = join(',', $_POST['familia']);
        }

        $this->tpv_config['tpv_fpago_efectivo'] = $_POST['pago_efectivo'];
        $this->tpv_config['tpv_fpago_tarjeta'] = FALSE;
        if ($_POST['pago_tarjeta'] != '') {
            $this->tpv_config['tpv_fpago_tarjeta'] = $_POST['pago_tarjeta'];
        }

        $this->tpv_config['tpv_texto_fin'] = $_POST['texto'];
        $this->tpv_config['tpv_preimprimir'] = isset($_POST['preimprimir']) ? 1 : 0;
        $this->tpv_config['tpv_emails_z'] = $_POST['emails_z'];

        if ($this->fsvar->array_save($this->tpv_config)) {
            $this->new_message('Datos guardados correctamente.');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

    private function cerrar_arqueo()
    {
        $tpv_arqueo = new tpv_arqueo();
        $caja2 = $tpv_arqueo->get($_GET['cerrar']);

        if (!$this->user->admin) {
            $this->new_error_msg("Tienes que ser administrador para poder cerrar cajas.");
        } else if ($caja2) {
            $caja2->abierta = FALSE;
            $caja2->diahasta = Date('d-m-Y');

            if ($caja2->save()) {
                $this->new_message("Caja cerrada correctamente.");
            } else {
                $this->new_error_msg("¡Imposible cerrar la caja!");
            }
        } else {
            $this->new_error_msg("Caja no encontrada.");
        }
    }

    private function eliminar_arqueo()
    {
        $tpv_arqueo = new tpv_arqueo();
        $tpv_comanda = new tpv_comanda();

        $arqueo2 = $tpv_arqueo->get($_GET['delete']);

        if (!$this->user->admin) {
            $this->new_error_msg("Tienes que ser administrador para poder eliminar arqueo.");
        } else if ($arqueo2) {
            if ($arqueo2->delete()) {
                $this->new_message("Arqueo eliminado correctamente.");

                /// eliminamos las comandas
                foreach ($tpv_comanda->all_from_arqueo($arqueo2->idtpv_arqueo) as $com) {
                    $com->delete();
                }
            } else {
                $this->new_error_msg("¡Imposible eliminar el arqueo!");
            }
        } else {
            $this->new_error_msg("Arqueo no encontrado.");
        }
    }

    private function nuevo_terminal()
    {
        $terminal = new terminal_caja();
        $terminal->nombre = $_POST['nombre'];
        $terminal->codalmacen = $_POST['codalmacen'];
        $terminal->codserie = $_POST['codserie'];

        if ($_POST['codcliente'] != '') {
            $terminal->codcliente = $_POST['codcliente'];
        }

        $terminal->anchopapel = intval($_POST['anchopapel']);
        $terminal->comandoapertura = $_POST['comandoapertura'];
        $terminal->comandocorte = $_POST['comandocorte'];
        $terminal->comandologo = $_POST['comandologo'];
        $terminal->num_tickets = intval($_POST['num_tickets']);
        $terminal->sin_comandos = isset($_POST['sin_comandos']);
        $terminal->cambiar_agente = isset($_POST['cambiar_agente']);
        $terminal->forzar_pin = isset($_POST['forzar_pin']);

        if ($terminal->save()) {
            $this->new_message('Terminal añadido correctamente. Ya puedes usarlo desde el TPV.');
            header('Location: index.php?page=tpv_tactil');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

    private function editar_terminal()
    {
        $terminal = new terminal_caja();

        $t2 = $terminal->get($_POST['idt']);
        if ($t2) {
            $t2->nombre = $_POST['nombre'];
            $t2->codalmacen = $_POST['codalmacen'];
            $t2->codserie = $_POST['codserie'];

            $t2->codcliente = NULL;
            if ($_POST['codcliente'] != '') {
                $t2->codcliente = $_POST['codcliente'];
            }

            $t2->anchopapel = intval($_POST['anchopapel']);
            $t2->comandoapertura = $_POST['comandoapertura'];
            $t2->comandocorte = $_POST['comandocorte'];
            $t2->comandologo = $_POST['comandologo'];
            $t2->num_tickets = intval($_POST['num_tickets']);
            $t2->sin_comandos = isset($_POST['sin_comandos']);
            $t2->cambiar_agente = isset($_POST['cambiar_agente']);
            $t2->forzar_pin = isset($_POST['forzar_pin']);

            if ($t2->save()) {
                $this->new_message('Datos guardados correctamente.');
            } else {
                $this->new_error_msg('Error al guardar los datos.');
            }
        } else {
            $this->new_error_msg('Terminal no encontrado.');
        }
    }

    private function eliminar_terminal()
    {
        $terminal = new terminal_caja();
        $t2 = $terminal->get($_GET['deletet']);

        if (!$this->user->admin) {
            $this->new_error_msg("Tienes que ser administrador para poder eliminar terminales.");
        } else if ($t2) {
            if ($t2->delete()) {
                $this->new_message('Terminal eliminado correctamente.');
            } else {
                $this->new_error_msg('Error al eliminar el terminal.');
            }
        } else {
            $this->new_error_msg('Terminal no encontrado.');
        }
    }

    public function anterior_url()
    {
        $url = '';

        if ($this->offset > 0) {
            $url = $this->url() . "&offset=" . ($this->offset - FS_ITEM_LIMIT);
        }

        return $url;
    }

    public function siguiente_url()
    {
        $url = '';

        if (count($this->resultados) == FS_ITEM_LIMIT) {
            $url = $this->url() . "&offset=" . ($this->offset + FS_ITEM_LIMIT);
        }

        return $url;
    }

    public function familia_checked($cod)
    {
        if ($this->tpv_config['tpv_familias']) {
            $aux = explode(',', $this->tpv_config['tpv_familias']);
            if ($aux) {
                return in_array($cod, $aux);
            }
        }

        return FALSE;
    }

    public function forma_pago_checked($cod)
    {
        if ($this->tpv_config['tpv_formas_pago']) {
            $aux = explode(',', $this->tpv_config['tpv_formas_pago']);
            if ($aux) {
                return in_array($cod, $aux);
            }
        }

        return FALSE;
    }

    private function buscar_referencia()
    {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        $articulo = new articulo();
        $json = array();
        foreach ($articulo->search($_REQUEST['buscar_referencia']) as $art) {
            $json[] = array('value' => $art->referencia, 'data' => $art->referencia);
        }

        header('Content-Type: application/json');
        echo json_encode(array('query' => $_REQUEST['buscar_referencia'], 'suggestions' => $json));
    }
}
