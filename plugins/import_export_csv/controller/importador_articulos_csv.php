<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/../lib/core_importador.php';

/**
 * Description of importador_csv
 *
 * @author Carlos García Gómez
 */
class importador_articulos_csv extends fs_controller
{

    public $almacen;
    public $col_disponibles;
    public $detectado;
    public $fuente_csv;
    public $impuesto;
    public $listado;
    public $prestashop;
    public $proveedor;
    public $proveedores;
    public $resultado;
    public $tarifas;
    public $url_recarga;
    public $compras_prop;
    private $core_importador;
    private $tmp_path;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Megaimportador CSV', 'admin', FALSE, FALSE);
    }

    protected function private_core()
    {
        $fuente = new fuente_csv();
        $this->almacen = new almacen();
        $this->col_disponibles = [];
        $this->core_importador = new core_importador();
        $this->detectado = [];
        $this->fuente_csv = FALSE;
        $this->impuesto = new impuesto();
        $this->listado = FALSE;
        $this->prestashop = in_array('prestashop', $GLOBALS['plugins']);
        $this->proveedor = new proveedor();
        $this->proveedores = $this->proveedor->all_full();
        $this->resultado = FALSE;
        $this->url_recarga = FALSE;

        $this->ini_filters();
        $this->check_tmp_path();

        if (isset($_REQUEST['id'])) {
            $this->fuente_csv = $fuente->get($_REQUEST['id']);
        } else if (isset($_POST['csv_protocolo'])) {
            $this->nueva_fuente();
        } else if (isset($_GET['delete'])) {
            $this->eliminar_fuente();
        }

        if ($this->fuente_csv) {
            $this->template = 'fuente_csv';

            if (isset($_POST['csv_protocolo'])) {
                $this->modificar_fuente();
            } else if (isset($_GET['empezar'])) {
                $this->empezar();
            } else if (isset($_GET['pprocesar'])) {
                $this->pprocesar();
            } else {
                $this->resultado = $this->get_url_contents(TRUE);
                $this->detectar_resultados();
            }
        } else {
            $this->listado = $fuente->all();
        }
    }

    private function ini_filters()
    {
        if (isset($_GET['offset2'])) {
            $this->core_importador->offset = intval($_GET['offset2']);
        }

        if (isset($_GET['empezar']) && isset($_GET['nuevos'])) {
            $this->core_importador->status['nuevos'] = intval($_GET['nuevos']);
            $this->core_importador->status['nuevosp'] = intval($_GET['nuevosp']);
            $this->core_importador->status['actualizados'] = intval($_GET['actualizados']);
            $this->core_importador->status['actualizadosp'] = intval($_GET['actualizadosp']);
        }

        if (class_exists('tarifa_articulo')) {
            $tarifa = new tarifa();
            $this->tarifas = $tarifa->all();
        } else {
            $this->tarifas = [];
        }
    }

    private function check_tmp_path()
    {
        $this->tmp_path = getcwd() . '/tmp/' . FS_TMP_NAME . 'fuentes_csv/';
        if (!file_exists($this->tmp_path) && !mkdir($this->tmp_path)) {
            $this->new_error_msg('Error al crear la carpeta tmp/fuentes_csv/');
        }
    }

    private function nueva_fuente()
    {
        $error = FALSE;
        $fuente = new fuente_csv();

        if (is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            if (copy($_FILES['csv_file']['tmp_name'], $this->tmp_path . $_FILES['csv_file']['name'])) {
                $fuente->url = $this->tmp_path . $_FILES['csv_file']['name'];
                $fuente->protocolo = 'file';
                $fuente->cron = FALSE;
            } else {
                $this->new_error_msg('Ha habido un error al cargar el archivo.');
                $error = TRUE;
            }
        } else {
            $fuente->protocolo = $_POST['csv_protocolo'];
            $fuente->url = $_POST['csv_url'];

            /// muchos usuarios añaden el protocolo (http://) a la url, no lo necesitamos
            if (substr($fuente->url, 0, strlen($fuente->protocolo . '://')) == $fuente->protocolo . '://') {
                $fuente->url = substr($fuente->url, strlen($fuente->protocolo . '://'));
            }

            $fuente->usuario = $_POST['csv_user'];
            $fuente->password = $_POST['csv_pass'];
        }

        if ($error) {
            /// ya se muestran los errores, no hay que hacer nada más, simplemente no guardar
        } else if ($fuente->save()) {
            $this->new_message('Fuente guardada correctamente.');
            header('Location: ' . $fuente->url());
        } else {
            $this->new_error_msg('Error al guardar la fuente.');
        }
    }

    private function eliminar_fuente()
    {
        $fuente = new fuente_csv();
        $fuente2 = $fuente->get($_GET['delete']);
        if ($fuente2) {
            if ($fuente2->delete()) {
                if ($fuente2->protocolo == 'file') {
                    /// eliminamos el archivo
                    unlink($fuente2->url);
                }

                $this->new_message('Fuente eliminada correctamente.');
            } else {
                $this->new_error_msg('Error al eliminar la fuente.');
            }
        } else {
            $this->new_error_msg('Fuente no encontrada.');
        }
    }

    private function modificar_fuente()
    {
        $this->fuente_csv->protocolo = $_POST['csv_protocolo'];
        $this->fuente_csv->url = $_POST['csv_url'];

        if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            copy($_FILES['csv_file']['tmp_name'], $this->tmp_path . $_FILES['csv_file']['name']);

            if ($this->fuente_csv->protocolo == 'file') {
                /// eliminamos el archivo anterior
                unlink($this->fuente_csv->url);
            }

            $this->fuente_csv->url = $this->tmp_path . $_FILES['csv_file']['name'];
        }

        $this->fuente_csv->usuario = $_POST['csv_user'];
        $this->fuente_csv->password = $_POST['csv_pass'];
        $this->fuente_csv->separador = $_POST['csv_separador'];
        $this->fuente_csv->codificacion = $_POST['csv_codificacion'];
        $this->fuente_csv->cron = isset($_POST['cron']);
        $this->fuente_csv->cron_hour = NULL;
        if ($_POST['cron_hour'] != '' && is_numeric($_POST['cron_hour'])) {
            $this->fuente_csv->cron_hour = intval($_POST['cron_hour']);
        } elseif($_POST['cron_hour'] !== '' && strpos($_POST['cron_hour'], ':') !== false) {
            $aux = explode(':', $_POST['cron_hour']);
            $this->fuente_csv->cron_hour = intval($aux[0]);
        }

        $this->fuente_csv->perfil = intval($_POST['perfil']);
        $this->fuente_csv->codimpuesto = $_POST['codimpuesto'];
        $this->fuente_csv->codalmacen = $_POST['codalmacen'];

        $this->fuente_csv->codproveedor = NULL;
        $this->fuente_csv->col_codproveedor = '';
        $this->fuente_csv->col_ref_prov = '';
        $this->fuente_csv->col_precio_compra = '';
        $this->fuente_csv->col_dto_compra = '';
        $this->fuente_csv->compra_con_iva = FALSE;
        if (isset($_POST['codproveedor'])) {
            if ($_POST['codproveedor'] != '') {
                $this->fuente_csv->codproveedor = $_POST['codproveedor'];
            }

            if (isset($_POST['col_codproveedor'])) {
                $this->fuente_csv->col_codproveedor = $_POST['col_codproveedor'];
            }

            $this->fuente_csv->col_ref_prov = $_POST['col_ref_prov'];
            $this->fuente_csv->col_precio_compra = $_POST['col_precio_compra'];
            $this->fuente_csv->col_dto_compra = $_POST['col_dto_compra'];
            $this->fuente_csv->compra_con_iva = isset($_POST['compra_con_iva']);
        }

        $this->fuente_csv->col_ref = '';
        if (isset($_POST['col_ref'])) {
            $this->fuente_csv->col_ref = $_POST['col_ref'];
        }

        $this->fuente_csv->sufijo = $_POST['sufijo'];
        $this->fuente_csv->col_desc = $_POST['col_desc'];
        $this->fuente_csv->col_iva = $_POST['col_iva'];
        $this->fuente_csv->col_precio = $_POST['col_precio'];
        $this->fuente_csv->pvp_max = isset($_POST['pvp_max']);
        $this->fuente_csv->con_iva = isset($_POST['con_iva']);
        $this->fuente_csv->col_stock = $_POST['col_stock'];
        $this->fuente_csv->col_nostock = $_POST['col_nostock'];

        $this->fuente_csv->col_barras = isset($_POST['col_barras']) ? $_POST['col_barras'] : '';
        $this->fuente_csv->col_partnumber = isset($_POST['col_partnumber']) ? $_POST['col_partnumber'] : '';
        $this->fuente_csv->col_precio_coste = isset($_POST['col_precio_coste']) ? $_POST['col_precio_coste'] : '';
        $this->fuente_csv->col_precio_tarifa1 = isset($_POST['col_precio_tarifa1']) ? $_POST['col_precio_tarifa1'] : '';
        $this->fuente_csv->col_precio_tarifa2 = isset($_POST['col_precio_tarifa2']) ? $_POST['col_precio_tarifa2'] : '';
        $this->fuente_csv->col_precio_tarifa3 = isset($_POST['col_precio_tarifa3']) ? $_POST['col_precio_tarifa3'] : '';

        if (isset($_POST['col_stockmin'])) {
            $this->fuente_csv->col_stockmin = $_POST['col_stockmin'];
            $this->fuente_csv->col_stockmax = $_POST['col_stockmax'];
            $this->fuente_csv->col_ventasinstock = $_POST['col_ventasinstock'];
            $this->fuente_csv->col_barras = $_POST['col_barras'];
            $this->fuente_csv->col_fabricante = $_POST['col_fabricante'];
            $this->fuente_csv->col_familia = $_POST['col_familia'];
            $this->fuente_csv->col_equivalencia = $_POST['col_equivalencia'];
            $this->fuente_csv->col_secompra = $_POST['col_secompra'];
            $this->fuente_csv->col_sevende = $_POST['col_sevende'];
            $this->fuente_csv->col_bloqueado = $_POST['col_bloqueado'];
            $this->fuente_csv->col_publico = $_POST['col_publico'];
            $this->fuente_csv->col_observaciones = $_POST['col_observaciones'];
            $this->fuente_csv->col_factualizado = $_POST['col_factualizado'];
            $this->fuente_csv->col_url_img = $_POST['col_url_img'];
        } else {
            $this->fuente_csv->col_stockmin = '';
            $this->fuente_csv->col_stockmax = '';
            $this->fuente_csv->col_ventasinstock = '';
            $this->fuente_csv->col_fabricante = '';
            $this->fuente_csv->col_familia = '';
            $this->fuente_csv->col_equivalencia = '';
            $this->fuente_csv->col_secompra = '';
            $this->fuente_csv->col_sevende = '';
            $this->fuente_csv->col_bloqueado = '';
            $this->fuente_csv->col_publico = '';
            $this->fuente_csv->col_observaciones = '';
            $this->fuente_csv->col_factualizado = '';
            $this->fuente_csv->col_url_img = '';
        }

        if (isset($_POST['col_desc_corta'])) {
            $this->fuente_csv->col_desc_corta = $_POST['col_desc_corta'];
            $this->fuente_csv->col_desc_larga = $_POST['col_desc_larga'];
            $this->fuente_csv->col_meta_title = $_POST['col_meta_title'];
            $this->fuente_csv->col_meta_descrip = $_POST['col_meta_descrip'];
            $this->fuente_csv->col_meta_keys = $_POST['col_meta_keys'];
            $this->fuente_csv->col_ps_precio = $_POST['col_ps_precio'];
            $this->fuente_csv->col_ps_oferta = $_POST['col_ps_oferta'];
            $this->fuente_csv->col_ps_oferta_desde = $_POST['col_ps_oferta_desde'];
            $this->fuente_csv->col_ps_oferta_hasta = $_POST['col_ps_oferta_hasta'];
            $this->fuente_csv->col_ps_anchura = $_POST['col_ps_anchura'];
            $this->fuente_csv->col_ps_altura = $_POST['col_ps_altura'];
            $this->fuente_csv->col_ps_profundidad = $_POST['col_ps_profundidad'];
            $this->fuente_csv->col_ps_peso = $_POST['col_ps_peso'];
            $this->fuente_csv->col_ps_gastos_envio = $_POST['col_ps_gastos_envio'];
            $this->fuente_csv->col_ps_txt_no_disponible = $_POST['col_ps_txt_no_disponible'];
            $this->fuente_csv->col_ps_activo = $_POST['col_ps_activo'];
            $this->fuente_csv->col_ps_redireccion = $_POST['col_ps_redireccion'];
            $this->fuente_csv->col_ps_redireccion_id = $_POST['col_ps_redireccion_id'];
            $this->fuente_csv->col_id_prestashop = $_POST['col_id_prestashop'];
            $this->fuente_csv->col_ps_factualizado = $_POST['col_ps_factualizado'];
        } else {
            $this->fuente_csv->col_desc_corta = '';
            $this->fuente_csv->col_desc_larga = '';
            $this->fuente_csv->col_meta_title = '';
            $this->fuente_csv->col_meta_descrip = '';
            $this->fuente_csv->col_meta_keys = '';
            $this->fuente_csv->col_ps_precio = '';
            $this->fuente_csv->col_ps_oferta = '';
            $this->fuente_csv->col_ps_oferta_desde = '';
            $this->fuente_csv->col_ps_oferta_hasta = '';
            $this->fuente_csv->col_ps_anchura = '';
            $this->fuente_csv->col_ps_altura = '';
            $this->fuente_csv->col_ps_profundidad = '';
            $this->fuente_csv->col_ps_peso = '';
            $this->fuente_csv->col_ps_gastos_envio = '';
            $this->fuente_csv->col_ps_txt_no_disponible = '';
            $this->fuente_csv->col_ps_activo = '';
            $this->fuente_csv->col_ps_redireccion = '';
            $this->fuente_csv->col_ps_redireccion_id = '';
            $this->fuente_csv->col_id_prestashop = '';
            $this->fuente_csv->col_ps_factualizado = '';
        }

        if ($this->fuente_csv->save()) {
            $this->new_message('Fuente guardada correctamente.');

            $this->resultado = $this->get_url_contents(TRUE);
            $this->detectar_resultados();
        } else {
            $this->new_error_msg('Error al guardar la fuente.');
        }
    }

    private function empezar()
    {
        $this->core_importador->procesar_fuente($this->fuente_csv);
        $this->url_recarga = $this->core_importador->url_recarga;

        foreach ($this->core_importador->errors as $err) {
            $this->new_error_msg($err);
        }

        foreach ($this->core_importador->messages as $msg) {
            $this->new_message($msg);
        }

        foreach ($this->core_importador->advices as $adv) {
            $this->new_advice($adv);
            $this->resultado = $this->get_url_contents(TRUE);
            $this->detectar_resultados();
        }
    }

    private function pprocesar()
    {
        $this->core_importador->fuente_csv = $this->fuente_csv;
        $this->core_importador->post_procesar();
        $this->url_recarga = $this->core_importador->url_recarga;

        foreach ($this->core_importador->errors as $err) {
            $this->new_error_msg($err);
        }

        foreach ($this->core_importador->messages as $msg) {
            $this->new_message($msg);
        }

        foreach ($this->core_importador->advices as $adv) {
            $this->new_advice($adv);
            $this->resultado = $this->get_url_contents(TRUE);
            $this->detectar_resultados();
        }
    }

    public function get_nombre_proveedor($cod)
    {
        $prov = $this->proveedor->get($cod);
        if ($prov) {
            return $prov->nombre;
        }

        return '-';
    }

    public function get_url_contents($parcial = FALSE)
    {
        $filePath = $this->fuente_csv->url;
        if ($this->fuente_csv->protocolo != 'file') {
            $url = $this->fuente_csv->protocolo . '://';
            if ($this->fuente_csv->usuario != '' && $this->fuente_csv->password != '') {
                $url .= $this->fuente_csv->usuario . ':' . urlencode($this->fuente_csv->password) . '@';
            }
            $url .= $this->fuente_csv->url;

            $filePath = $this->tmp_path . $this->fuente_csv->id . '_' . date('YmdHm') . '.csv';
            if (!file_exists($filePath)) {
                fs_file_download($url, $filePath);
            }
        }

        if (file_exists($filePath)) {
            if ($parcial) {
                $data = file_get_contents($filePath, FALSE, NULL, 0, 4096);
            } else {
                $data = file_get_contents($filePath);
            }

            if ($this->fuente_csv->codificacion == '1') {
                return mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true));
            } else if ($this->fuente_csv->codificacion == '15') {
                return mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-15', true));
            }

            return $data;
        }

        $this->new_error_msg('Archivo no encontrado: ' . $this->fuente_csv->url);
        return '';
    }

    public function detectar_contenido()
    {
        $detectado = [];
        $num = 0;

        foreach (preg_split('/\n|\r\n?/', $this->resultado) as $aux) {
            $linea = $this->core_importador->custom_explode($this->fuente_csv->separador, $aux);
            if ($num < 50) {
                $detectado[] = $linea;
                $num++;
            } else {
                break;
            }
        }

        return $detectado;
    }

    private function detectar_resultados()
    {
        $plinea = FALSE;
        $num = 0;

        foreach (preg_split('/\n|\r\n?/', $this->resultado) as $aux) {
            $linea = $this->core_importador->custom_explode($this->fuente_csv->separador, $aux);

            if (!$plinea) {
                $plinea = $linea;

                if (count($plinea) == 1) {
                    $this->new_error_msg('Sólo se encuentra una columna ¿Separador equivocado?');
                } else {
                    foreach ($plinea as $col) {
                        if ($col) {
                            $this->col_disponibles[] = $col;
                        }
                    }
                }
            } else if (count($linea) == count($plinea)) {
                $nlinea = [];

                foreach ($plinea as $i => $pl) {
                    if ($pl == $this->fuente_csv->col_codproveedor && $this->fuente_csv->col_codproveedor != '') {
                        $nlinea['proveedor'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ref_prov && $this->fuente_csv->col_ref_prov != '') {
                        $nlinea['refprov'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ref && $this->fuente_csv->col_ref != '') {
                        $nlinea['referencia'] = str_replace(' ', '_', $linea[$i] . $this->fuente_csv->sufijo);
                    }

                    if ($pl == $this->fuente_csv->col_desc && $this->fuente_csv->col_desc != '') {
                        $nlinea['descripcion'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_iva && $this->fuente_csv->col_iva != '') {
                        $nlinea['iva'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_compra && $this->fuente_csv->col_precio_compra != '') {
                        $nlinea['precio_compra'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_dto_compra && $this->fuente_csv->col_dto_compra != '') {
                        $nlinea['dto_compra'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_coste && $this->fuente_csv->col_precio_coste != '') {
                        $nlinea['precio_coste'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio && $this->fuente_csv->col_precio != '') {
                        $nlinea['precio_venta'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa1 && $this->fuente_csv->col_precio_tarifa1 != '') {
                        $nlinea['precio_tarifa1'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa2 && $this->fuente_csv->col_precio_tarifa2 != '') {
                        $nlinea['precio_tarifa2'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa3 && $this->fuente_csv->col_precio_tarifa3 != '') {
                        $nlinea['precio_tarifa3'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stock && $this->fuente_csv->col_stock != '') {
                        $nlinea['stock'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_nostock && $this->fuente_csv->col_nostock != '') {
                        $nlinea['nostock'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stockmin && $this->fuente_csv->col_stockmin != '') {
                        $nlinea['stockmin'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stockmax && $this->fuente_csv->col_stockmax != '') {
                        $nlinea['stockmax'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ventasinstock && $this->fuente_csv->col_ventasinstock != '') {
                        $nlinea['ventasinstock'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_barras && $this->fuente_csv->col_barras != '') {
                        $nlinea['codbarras'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_fabricante && $this->fuente_csv->col_fabricante != '') {
                        $nlinea['codfabricante'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_familia && $this->fuente_csv->col_familia != '') {
                        $nlinea['codfamilia'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_equivalencia && $this->fuente_csv->col_equivalencia != '') {
                        $nlinea['equivalencia'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_partnumber && $this->fuente_csv->col_partnumber != '') {
                        $nlinea['partnumber'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_secompra && $this->fuente_csv->col_secompra != '') {
                        $nlinea['secompra'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_sevende && $this->fuente_csv->col_sevende != '') {
                        $nlinea['sevende'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_bloqueado && $this->fuente_csv->col_bloqueado != '') {
                        $nlinea['bloqueado'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_publico && $this->fuente_csv->col_publico != '') {
                        $nlinea['publico'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_observaciones && $this->fuente_csv->col_observaciones != '') {
                        $nlinea['observaciones'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_factualizado && $this->fuente_csv->col_factualizado != '') {
                        $nlinea['factualizado'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_url_img && $this->fuente_csv->col_url_img != '') {
                        $nlinea['url_img'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_desc_corta && $this->fuente_csv->col_desc_corta != '') {
                        $nlinea['desc_corta'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_desc_larga && $this->fuente_csv->col_desc_larga != '') {
                        $nlinea['desc_larga'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_meta_title && $this->fuente_csv->col_meta_title != '') {
                        $nlinea['meta_title'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_meta_descrip && $this->fuente_csv->col_meta_descrip != '') {
                        $nlinea['meta_description'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_meta_keys && $this->fuente_csv->col_meta_keys != '') {
                        $nlinea['meta_keywords'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_precio && $this->fuente_csv->col_ps_precio != '') {
                        $nlinea['ps_precio'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_oferta && $this->fuente_csv->col_ps_oferta != '') {
                        $nlinea['ps_oferta'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_oferta_desde && $this->fuente_csv->col_ps_oferta_desde != '') {
                        $nlinea['ps_oferta_desde'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_oferta_hasta && $this->fuente_csv->col_ps_oferta_hasta != '') {
                        $nlinea['ps_oferta_hasta'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_anchura && $this->fuente_csv->col_ps_anchura != '') {
                        $nlinea['ps_anchura'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_altura && $this->fuente_csv->col_ps_altura != '') {
                        $nlinea['ps_altura'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_profundidad && $this->fuente_csv->col_ps_profundidad != '') {
                        $nlinea['ps_profundidad'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_peso && $this->fuente_csv->col_ps_peso != '') {
                        $nlinea['ps_peso'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_gastos_envio && $this->fuente_csv->col_ps_gastos_envio != '') {
                        $nlinea['ps_gastos_envio'] = $this->core_importador->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_txt_no_disponible && $this->fuente_csv->col_ps_txt_no_disponible != '') {
                        $nlinea['ps_txt_no_disponible'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_activo && $this->fuente_csv->col_ps_activo != '') {
                        $nlinea['ps_activo'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_redireccion && $this->fuente_csv->col_ps_redireccion != '') {
                        $nlinea['ps_redireccion'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_redireccion_id && $this->fuente_csv->col_ps_redireccion_id != '') {
                        $nlinea['ps_redireccion_id'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_id_prestashop && $this->fuente_csv->col_id_prestashop != '') {
                        $nlinea['id_prestashop'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_factualizado && $this->fuente_csv->col_ps_factualizado != '') {
                        $nlinea['ps_factualizado'] = $linea[$i];
                    }
                }

                if ($num < 50) {
                    $this->detectado[] = $nlinea;
                }
            }

            if ($num < 50) {
                $num++;
            } else {
                break;
            }
        }
    }

    public function separadores()
    {
        return array(';', ',', '|', "\t");
    }

    public function codificaciones()
    {
        return array(
            '0' => 'UTF8',
            '1' => 'ISO-8859-1',
            '15' => 'ISO-8859-15'
        );
    }
}
