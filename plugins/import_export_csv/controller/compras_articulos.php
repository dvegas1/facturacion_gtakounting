<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Description of compras_articulos
 *
 * @author carlos
 */
class compras_articulos extends fs_controller
{

    public $almacen;
    public $codproveedor;
    public $compras_setup;
    public $constock;
    public $filas_carrito;
    public $mostrar;
    public $offset;
    public $orden;
    public $proveedores;
    public $resultados;
    public $total_resultados;
    public $url_recarga;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Artículos', 'compras');
    }

    protected function private_core()
    {
        $this->share_extensions();

        $this->mostrar = 'resultados';
        $this->almacen = new almacen();

        $this->filas_carrito = [];
        $carrito = new fila_carrito();

        /// cargamos la configuración
        $fsvar = new fs_var();
        $this->compras_setup = $fsvar->array_get(
            array(
            'iecsv_artpedido' => FALSE,
            'iecsv_act_art' => FALSE,
            'iecsv_act_art_precios' => FALSE,
            'iecsv_act_art_precio' => 'coste',
            'iecsv_act_art_stock' => FALSE,
            'iecsv_act_art_alm' => 'ALG',
            ), FALSE
        );

        if (isset($_POST['opciones'])) {
            $this->compras_setup['iecsv_artpedido'] = ( isset($_POST['iecsv_artpedido']) ? 1 : 0 );
            $this->compras_setup['iecsv_act_art'] = ( isset($_POST['iecsv_act_art']) ? 1 : 0 );
            $this->compras_setup['iecsv_act_art_precios'] = ( isset($_POST['iecsv_act_art_precios']) ? 1 : 0 );
            $this->compras_setup['iecsv_act_art_precio'] = $_POST['iecsv_act_art_precio'];
            $this->compras_setup['iecsv_act_art_stock'] = ( isset($_POST['iecsv_act_art_stock']) ? 1 : 0 );
            $this->compras_setup['iecsv_act_art_alm'] = $_POST['iecsv_act_art_alm'];

            if ($fsvar->array_save($this->compras_setup)) {
                $this->new_message('Opciones guardadas correctamente.');
            } else
                $this->new_error_msg('Error al guardar los datos.');
        }
        else if (isset($_GET['megamod'])) {
            if ($_GET['megamod'] == 'refproveedor') {
                $this->megamod_refproveedor();
            } else if ($_GET['megamod'] == 'onlyarticulos') {
                $this->megamod_onlyarticulos();
            }
        } else if (isset($_REQUEST['carrito'])) {
            $this->mostrar = 'carrito';

            if ($_REQUEST['carrito'] == 'add' AND isset($_GET['id'])) {
                // En caso de agregar un elemento al carrito
                $artp0 = new articulo_proveedor();
                $artp = $artp0->get($_GET['id']);
                if ($artp) {
                    $fila = $carrito->get_by_idarticulop($_GET['id']);
                    if ($fila) {
                        $fila->cantidad++;
                        if ($fila->save()) {
                            $this->new_message('Artículo añadido correctamente al carrito.');
                        } else {
                            $this->new_error_msg('Error al añadir el artículos carrito.');
                        }
                    } else {
                        $carrito->nick = $this->user->nick;
                        $carrito->idarticulop = $artp->id;
                        if ($carrito->save()) {
                            $this->new_message('Artículo añadido correctamente al carrito.');
                        } else {
                            $this->new_error_msg('Error al añadir el artículos carrito.');
                        }
                    }
                }
            } else if ($_REQUEST['carrito'] == 'pedidoscli') {
                $this->pedidoscli2carrito();
            } else if ($_REQUEST['carrito'] == 'limpiar') {
                $carrito->limpiar_carrito($this->user->nick);
                $this->new_message('Carrito vaciado.');
            } else if (isset($_POST['realizarpedido'])) {
                $this->guardar_pedido();

                if ($_POST['realizarpedido'] == 'TRUE') {
                    // Se pueden dar dos casos: el de realizar un pedido o el de simplemente guardar los cambios del carrito
                    if (class_exists('pedido_proveedor')) {
                        $this->realizar_pedidos();
                    } else {
                        $this->new_error_msg('No tienes instalado el plugin <b>presupuestos_y_pedidos</b>.');
                    }
                }
            }
        } else if (isset($_GET['nuevo'])) {
            $this->nuevo_articulo($_GET['nuevo']);
        } else if (isset($_GET['editar'])) {
            $this->editar_articulo();
        } else if (isset($_GET['delete'])) {
            $this->delete_articulop();
        }

        $this->codproveedor = '';
        if (isset($_REQUEST['codproveedor'])) {
            $this->codproveedor = $_REQUEST['codproveedor'];
        }

        $this->orden = 'pvpmin';
        if (isset($_REQUEST['orden'])) {
            $this->orden = $_REQUEST['orden'];
        }

        $this->constock = isset($_REQUEST['constock']);

        $this->offset = 0;
        if (isset($_REQUEST['offset'])) {
            $this->offset = intval($_REQUEST['offset']);
        }

        $proveedor = new proveedor();
        $this->proveedores = $proveedor->all_full();
        $this->search_articulos_prov();

        $this->filas_carrito = $carrito->all_from_nick($this->user->nick);
    }

    private function search_articulos_prov()
    {
        $this->resultados = [];
        $ap = new articulo_proveedor();

        $sql1 = '';
        $query = $ap->no_html(trim(mb_strtolower($this->query, 'UTF8')));
        if ($query != '') {
            $sql1 .= " (lower(refproveedor) LIKE '%" . $query . "%' "
                . "OR lower(referencia) LIKE '%" . $query . "%' "
                . "OR lower(partnumber) LIKE '%" . $query . "%' "
                . "OR lower(codbarras) = '" . $query . "' "
                . "OR lower(descripcion) LIKE '%" . $query . "%')";
        } else {
            $sql1 .= " 1=1";
        }

        if ($this->codproveedor != '') {
            $sql1 .= " AND codproveedor = " . $ap->var2str($this->codproveedor);
        }

        if ($this->constock) {
            $sql1 .= " AND (stock > 0 OR nostock)";
        }

        $sql2 = "";
        if ($this->orden == 'pvpmin') {
            $sql2 = " ORDER BY precio ASC";
        } else if ($this->orden == 'pvpmax') {
            $sql2 = " ORDER BY precio DESC";
        } else if ($this->orden == 'stockmax') {
            $sql2 = " ORDER BY stock DESC";
        } else {
            $sql2 = " ORDER BY referencia ASC";
        }

        /// obtenemos el total
        $this->total_resultados = 0;
        $data = $this->db->select("SELECT COUNT(*) as total FROM articulosprov WHERE" . $sql1);
        if ($data) {
            $this->total_resultados = intval($data[0]['total']);
        }

        /// obtenemos los resultados
        $data = $this->db->select_limit("SELECT * FROM articulosprov WHERE" . $sql1 . $sql2, FS_ITEM_LIMIT, $this->offset);
        if ($data) {
            foreach ($data as $d) {
                $this->resultados[] = new articulo_proveedor($d);
            }
        }
    }

    public function paginas()
    {
        $url = $this->url() . '&query=' . $this->query
            . '&codproveedor=' . $this->codproveedor
            . '&orden=' . $this->orden;

        if ($this->constock) {
            $url .= '&constock=TRUE';
        }

        $paginas = [];
        $i = 0;
        $num = 0;
        $actual = 1;
        $total = $this->total_resultados;

        /// añadimos todas la página
        while ($num < $total) {
            $paginas[$i] = array(
                'url' => $url . "&offset=" . ($i * FS_ITEM_LIMIT),
                'num' => $i + 1,
                'actual' => ($num == $this->offset)
            );

            if ($num == $this->offset) {
                $actual = $i;
            }

            $i++;
            $num += FS_ITEM_LIMIT;
        }

        /// ahora descartamos
        foreach ($paginas as $j => $value) {
            $enmedio = intval($i / 2);

            /**
             * descartamos todo excepto la primera, la última, la de enmedio,
             * la actual, las 5 anteriores y las 5 siguientes
             */
            if (($j > 1 AND $j < $actual - 5 AND $j != $enmedio) OR ( $j > $actual + 5 AND $j < $i - 1 AND $j != $enmedio)) {
                unset($paginas[$j]);
            }
        }

        if (count($paginas) > 1) {
            return $paginas;
        } else {
            return [];
        }
    }

    public function get_nombre_proveedor($cod)
    {
        $nombre = '-';

        foreach ($this->proveedores as $prov) {
            if ($prov->codproveedor == $cod) {
                $nombre = $prov->nombre;
                break;
            }
        }

        return $nombre;
    }

    private function megamod_refproveedor()
    {
        $cambios = 0;

        $offset2 = 0;
        if (isset($_GET['offset2'])) {
            $offset2 = intval($_GET['offset2']);
        }

        $data = $this->db->select_limit("SELECT * FROM articulosprov WHERE referencia IS NOT NULL", 200, $offset2);
        if ($data) {
            foreach ($data as $d) {
                if (!is_null($d['refproveedor']) AND $d['refproveedor'] != '' AND $d['refproveedor'] != $d['referencia']) {
                    $this->db->exec("UPDATE articulos SET equivalencia = " . $this->empresa->var2str($d['refproveedor']) .
                        " WHERE referencia = " . $this->empresa->var2str($d['referencia']) . ";");
                    $cambios++;
                }
            }

            $this->url_recarga = $this->url() . '&megamod=refproveedor&offset2=' . ($offset2 + 200);
            $this->new_message($cambios . ' cambios realizados. Recargando... &nbsp; <i class="fa fa-refresh fa-spin"></i>');
        } else
            $this->new_advice('Proceso finalizado.');
    }

    private function megamod_onlyarticulos()
    {
        if ($this->db->exec("DELETE FROM articulosprov WHERE referencia IS NULL;")) {
            if ($this->db->exec("DELETE FROM articulosprov WHERE referencia NOT IN (SELECT referencia FROM articulos);")) {
                $this->new_message('Se han eliminado todos los datos de artículos de proveedor que no estaban en tu catálogo.');
            }
        }
    }

    private function pedidoscli2carrito()
    {
        $artp0 = new articulo_proveedor();
        $carrito = new fila_carrito();

        $nuevos = 0;
        $ped0 = new pedido_cliente();
        foreach ($ped0->all_ptealbaran() as $ped) {
            foreach ($ped->get_lineas() as $lin) {
                if ($lin->referencia) {
                    $fila = $carrito->get_by_ref($lin->referencia);
                    if ($fila) {
                        $stock = $fila->get_stockfis();
                        if ($lin->cantidad > $stock) {
                            $fila->cantidad += $lin->cantidad - $stock;
                            $fila->save();
                        }
                    } else {
                        $encontrado = FALSE;
                        foreach ($artp0->all_from_ref($lin->referencia) as $artp) {
                            $fila = new fila_carrito();
                            $fila->nick = $this->user->nick;
                            $fila->idarticulop = $artp->id;

                            $stock = $fila->get_stockfis();
                            if ($lin->cantidad > $stock) {
                                $fila->cantidad = $lin->cantidad - $stock;
                                $fila->save();
                                $nuevos++;
                            }

                            $encontrado = TRUE;
                        }

                        if (!$encontrado) {
                            $this->new_message('No se ha encontrado ningún proveedor para el artículo ' . $lin->referencia);
                        }
                    }
                }
            }
        }

        $this->new_message($nuevos . ' artículos añadidos al carrito.');
    }

    private function guardar_pedido()
    {
        $idElementos = [];
        foreach ($_POST as $key => $value) {
            // Buscamos objetos dentro de POST que sean del estilo "idfila_5" donde el 5 es el número de la línea
            if (strpos($key, 'idfila_') === 0) {
                // Extraemos el número de la fila
                $nombreFila = explode("_", $key);
                $idElementos[] = $nombreFila[1];
            }
        }

        // Limpiamos el carrito anterior de la BD
        $filaCarritoAux = new fila_carrito();
        $filaCarritoAux->limpiar_carrito($this->user->nick);

        // Cargamos la información del POST en objetos
        for ($i = 0; $i < count($idElementos); $i++) {
            $filaCarrito = new fila_carrito();
            $filaCarrito->id = intval($_POST["idfila_" . $idElementos[$i]]);
            $filaCarrito->nick = $this->user->nick;
            $filaCarrito->idarticulop = intval($_POST["idarticulopro_" . $idElementos[$i]]);
            $filaCarrito->cantidad = intval($_POST["cantidad_" . $idElementos[$i]]);
            $filaCarrito->save();
        }
    }

    private function realizar_pedidos()
    {
        $numFilasCarrito = intval($_POST['filascarrito']);

        // Agrupamos por proveedor
        $envios = [];
        $filaCarritoAux = new fila_carrito();
        foreach ($filaCarritoAux->all_from_nick($this->user->nick) as $fila) {
            // Si ya tenemos un array con la misma clave del ID del proveedor se agrega ahí
            if (isset($envios[$fila->articulop->codproveedor])) {
                $envios[$fila->articulop->codproveedor][] = $fila;
            } else {
                // De lo contrario creamos otro array
                $envios[$fila->articulop->codproveedor] = array($fila);
            }
        }

        $div0 = new divisa();
        $eje0 = new ejercicio();
        $prov0 = new proveedor();

        // Recorremos todos los envíos para cada uno de los proveedores
        $continuar = TRUE;
        foreach ($envios as $codProveedor => $enviosProveedor) {
            if ($continuar) {
                // TODO : necesitamos controlar las peticiones dobles
                // Cargamos la información general para el pedido
                $pedido = new pedido_proveedor();

                $proveedor = $prov0->get($codProveedor);
                $pedido->codproveedor = $proveedor->codproveedor;
                $pedido->nombre = $proveedor->razonsocial;
                $pedido->cifnif = $proveedor->cifnif;
                if (!$proveedor->codserie) {
                    $pedido->codserie = $this->empresa->codserie;
                } else {
                    $pedido->codserie = $proveedor->codserie;
                }

                $pedido->codpago = $proveedor->codpago;

                $ejercicio = $eje0->get_by_fecha($pedido->fecha);
                if ($ejercicio) {
                    $pedido->codejercicio = $ejercicio->codejercicio;
                }

                $pedido->codalmacen = $this->empresa->codalmacen;
                $pedido->coddivisa = $this->empresa->coddivisa;

                $divisa = $div0->get($this->empresa->coddivisa);
                if ($divisa) {
                    $pedido->tasaconv = $divisa->tasaconv;
                }

                $pedido->codagente = $this->user->codagente;

                if ($pedido->save()) {
                    // Agregamos los artículos al pedido
                    foreach ($enviosProveedor as $envio) {
                        if ($continuar) {
                            $linea = new linea_pedido_proveedor();
                            $linea->idpedido = $pedido->idpedido;
                            $linea->referencia = $envio->articulop->referencia;
                            $linea->descripcion = $envio->articulop->descripcion;
                            $linea->cantidad = $envio->cantidad;
                            $linea->pvpunitario = $envio->articulop->precio;
                            $linea->dtopor = $envio->articulop->dto;

                            /// creamos el artículo si no existe:
                            if ($this->compras_setup['iecsv_artpedido']) {
                                if (!$envio->articulop->get_articulo()) {
                                    $linea->referencia = $this->nuevo_articulo($envio->idarticulop, FALSE);
                                }
                            }

                            $linea->codimpuesto = $envio->articulop->codimpuesto;
                            $linea->iva = $envio->articulop->get_iva();
                            $linea->pvpsindto = ($linea->pvpunitario * $linea->cantidad);
                            $linea->pvptotal = $linea->pvpsindto * (100 - $linea->dtopor) / 100;

                            if ($linea->save()) {
                                // Actualizamos los totales del pedido
                                $pedido->neto += $linea->pvptotal;
                                $pedido->totaliva += ($linea->pvptotal * $linea->iva / 100);
                                $pedido->totalirpf += ($linea->pvptotal * $linea->irpf / 100);
                                $pedido->totalrecargo += ($linea->pvptotal * $linea->recargo / 100);
                            } else {
                                $this->new_error_msg("¡Imposible guardar la linea con referencia: " . $linea->referencia);
                                $continuar = FALSE;
                            }
                        }
                    }

                    if ($continuar) {
                        // Realizamos las operaciones de redondeo
                        $pedido->neto = round($pedido->neto, FS_NF0);
                        $pedido->totaliva = round($pedido->totaliva, FS_NF0);
                        $pedido->totalirpf = round($pedido->totalirpf, FS_NF0);
                        $pedido->totalrecargo = round($pedido->totalrecargo, FS_NF0);
                        $pedido->total = $pedido->neto + $pedido->totaliva - $pedido->totalirpf + $pedido->totalrecargo;

                        // Guardamos el pedido finalmente
                        if ($pedido->save()) {
                            $this->new_message("<a href='" . $pedido->url() . "'>" . ucfirst(FS_PEDIDO) . "</a> guardado correctamente.");
                            $filaCarritoAux->limpiar_carrito($this->user->nick);
                        } else {
                            $this->new_error_msg("¡Imposible actualizar el <a href='" . $pedido->url() . "'>" . FS_PEDIDO . "</a>!");
                        }
                    } else if ($pedido->delete()) {
                        $this->new_message(ucfirst(FS_PEDIDO) . " eliminado correctamente.");
                    } else {
                        $this->new_error_msg("¡Imposible eliminar el <a href='" . $pedido->url() . "'>" . FS_PEDIDO . "</a>!");
                    }
                } else {
                    $this->new_error_msg("¡Imposible guardar el " . FS_PEDIDO . "!");
                }
            }
        }
    }

    private function nuevo_articulo($id, $header = TRUE)
    {
        $apropiedadesc = new compras_art_prop();
        $aprov = new articulo_proveedor();
        $articulo = new articulo();
        $ap = new articulo_propiedad();

        $articulop = $aprov->get($id);
        if ($articulop) {
            /// tenemos referencia o generamos una nueva?
            if (isset($articulop->referencia)) {
                $articulo->referencia = $articulop->referencia;
            } else
                $articulo->referencia = $articulo->get_new_referencia();

            if (isset($articulop->descripcion)) {
                $articulo->descripcion = $articulop->descripcion;
            }

            if (isset($articulop->precio)) {
                if ($this->compras_setup['iecsv_act_art_precio'] == 'pvp') {
                    $articulo->pvp = $articulop->precio;
                } else {
                    $articulo->preciocoste = $articulop->precio;
                }
            }

            if (isset($articulop->codbarras)) {
                $articulo->codbarras = $articulop->codbarras;
            }

            if (isset($articulop->partnumber)) {
                $articulo->partnumber = $articulop->partnumber;
            }

            if (isset($articulop->descripcion)) {
                $articulo->descripcion = $articulop->descripcion;
            }

            $articulo->equivalencia = $articulop->refproveedor;

            if ($articulo->save()) {
                /// actualizamos el artículo proveedor
                $articulop->referencia = $articulo->referencia;
                $articulop->save();

                /// datos prestashop
                $apropiedades = [];
                if ($apropiedadesc->simple_get($articulop->id, 'desc_corta') != '') {
                    $apropiedades['ps_desc_corta'] = $apropiedadesc->simple_get($articulop->id, 'desc_corta');
                }
                if ($apropiedadesc->simple_get($articulop->id, 'desc_larga') != '') {
                    $apropiedades['ps_desc_larga'] = $apropiedadesc->simple_get($articulop->id, 'desc_larga');
                }
                if ($apropiedadesc->simple_get($articulop->id, 'ps_anchura') != '') {
                    $apropiedades['ps_anchura'] = $apropiedadesc->simple_get($articulop->id, 'ps_anchura');
                }
                if ($apropiedadesc->simple_get($articulop->id, 'ps_altura') != '') {
                    $apropiedades['ps_altura'] = $apropiedadesc->simple_get($articulop->id, 'ps_altura');
                }
                if ($apropiedadesc->simple_get($articulop->id, 'ps_profundidad') != '') {
                    $apropiedades['ps_profundidad'] = $apropiedadesc->simple_get($articulop->id, 'ps_profundidad');
                }
                if ($apropiedadesc->simple_get($articulop->id, 'ps_peso') != '') {
                    $apropiedades['ps_peso'] = $apropiedadesc->simple_get($articulop->id, 'ps_peso');
                }
                if ($apropiedades) {
                    $ap->array_save($articulo->referencia, $apropiedades);
                }

                /// cambiamos a la página del artículo si se pide:
                if ($header) {
                    header('location: ' . $articulo->url());
                }
            } else
                $this->new_error_msg("¡Error al crear el articulo!");
        }

        return $articulo->referencia;
    }

    private function editar_articulo()
    {
        $sql1 = "UPDATE articulosprov SET referencia = " . $this->empresa->var2str($_POST['edit_referencia'])
            . ", descripcion = " . $this->empresa->var2str($this->empresa->no_html($_POST['edit_descripcion']))
            . "  WHERE id = " . $this->empresa->var2str($_GET['editar']) . ";";

        if ($this->db->exec($sql1)) {
            $this->new_message('Articulo modificado correctamente.');
        }
    }

    private function delete_articulop()
    {
        $artp0 = new articulo_proveedor();
        $articulop = $artp0->get($_GET['delete']);
        if ($articulop) {
            if ($articulop->delete()) {
                $this->new_message('Artículo de proveedor eliminado correctamente.');
            } else {
                $this->new_error_msg('Imposible eliminar el artículo de proveedor.');
            }
        } else {
            $this->new_error_msg('Artículo de proveedor no encontrado.');
        }
    }

    private function share_extensions()
    {
        $fsext = new fs_extension();
        $fsext->name = 'pedidoscli';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_pedidos';
        $fsext->type = 'button';
        $fsext->text = '<span class="glyphicon glyphicon-shopping-cart"></span>'
            . '<span class="hidden-xs">&nbsp; Comprar</span>';
        $fsext->params = '&carrito=pedidoscli';
        $fsext->save();
    }
}
