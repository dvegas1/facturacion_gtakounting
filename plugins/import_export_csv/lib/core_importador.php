<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2016-2019, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Description of core_importador
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class core_importador
{

    /**
     * Almcena los consejos generados.
     *
     * @var array
     */
    public $advices = [];

    /**
     * Almacena los errores generados.
     *
     * @var array
     */
    public $errors = [];

    /**
     * Almacena la fuente a tratar.
     *
     * @var \fuente_csv
     */
    public $fuente_csv;

    /**
     * límite de artículos a procesar.
     *
     * @var int
     */
    public $limit;

    /**
     * Límite de artículos a post-procesar.
     *
     * @var int
     */
    public $limit2;

    /**
     * Almacena los mensajes generados.
     *
     * @var array
     */
    public $messages = [];

    /**
     *
     * @var int
     */
    public $next_offset;

    /**
     *
     * @var int
     */
    public $offset;

    /**
     *
     * @var array
     */
    public $status;

    /**
     *
     * @var string
     */
    public $url_recarga;

    /**
     *
     * @var \articulo
     */
    private $articulo;

    /**
     *
     * @var \articulo_proveedor
     */
    private $articulo_prov;

    /**
     *
     * @var \articulo_propiedad
     */
    private $articulo_prop;

    /**
     *
     * @var string
     */
    private $last_codfabricante;

    /**
     *
     * @var string
     */
    private $last_codfamilia;

    /**
     *
     * @var \impuesto
     */
    private $last_impuesto;

    /**
     *
     * @var \tarifa[]
     */
    private $tarifas;

    public function __construct()
    {
        $this->advices = [];
        $this->articulo = new articulo();
        $this->articulo_prop = new articulo_propiedad();
        $this->articulo_prov = new articulo_proveedor();
        $this->errors = [];
        $this->fuente_csv = FALSE;
        $this->limit = 500;
        $this->limit2 = 20;
        $this->messages = [];
        $this->offset = 0;
        $this->next_offset = 0;

        $this->last_codfabricante = NULL;
        $this->last_codfamilia = NULL;
        $this->last_impuesto = new impuesto();

        $this->status = array(
            'nuevos' => 0,
            'nuevosp' => 0,
            'actualizados' => 0,
            'actualizadosp' => 0
        );

        $tarifa = new tarifa();
        $this->tarifas = $tarifa->all();

        $this->url_recarga = FALSE;
    }

    /**
     * 
     * @param string $url
     * @param string $codificacion
     *
     * @return string
     */
    public function curl_get_contents($url, $codificacion = FALSE)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($codificacion == '1') {
            return mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true));
        } else if ($codificacion == '15') {
            return mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-15', true));
        }

        return $data;
    }

    /**
     * Devuelve un array con los resultados después de partir una cadena usando
     * el separador $separador.
     * Tiene en cuenta los casos en que la subcadena empieza por comillas y
     * contiene el separadr dentro, como cuando exportas de excel o libreoffice:
     * columna1;"columna2;esto sigue siendo la columna 2";columna3
     * 
     * @param string $separador
     * @param string $texto
     *
     * @return array
     */
    public function custom_explode($separador, $texto)
    {
        $seplist = [];

        $aux = explode($separador, $texto);
        if ($aux) {
            $agrupar = '';

            foreach ($aux as $a) {
                if ($agrupar != '') {
                    /// continuamos agrupando
                    $agrupar .= $separador . $a;

                    if (mb_substr($a, -1) == '"') {
                        /// terminamos de agrupar
                        $seplist[] = trim(mb_substr($agrupar, 0, -1));
                        $agrupar = '';
                    }
                } else if (mb_substr($a, 0, 1) == '"' && mb_substr($a, -1) != '"') {
                    /// empezamos a agrupar
                    $agrupar = mb_substr($a, 1);
                } else if (mb_substr($a, 0, 1) == '"' && mb_substr($a, -1) == '"') {
                    $seplist[] = trim(mb_substr($a, 1, -1));
                } else {
                    $seplist[] = trim($a);
                }
            }
        }

        /// eliminamos caracteres problemáticos
        foreach ($seplist as $i => $value) {
            $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
            $seplist[$i] = preg_replace($regex, '$1', $value);
        }

        return $seplist;
    }

    /**
     * 
     * @param string $f
     *
     * @return float
     */
    public function custom_floatval($f)
    {
        return floatval(str_replace(',', '.', $f));
    }

    /**
     * Procesa una fuente e importa los artículos.
     *
     * @param fuente_csv $fu
     */
    public function procesar_fuente(&$fu)
    {
        $this->fuente_csv = $fu;

        if ($this->fuente_csv->protocolo == 'file') {
            $this->messages[] = "Comprobando: " . $this->fuente_csv->url;
            $data = file_get_contents($this->fuente_csv->url);
            if ($this->fuente_csv->codificacion == '1') {
                $data = mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true));
            } else if ($this->fuente_csv->codificacion == '15') {
                $data = mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-15', true));
            }
        } else {
            $url = $this->fuente_csv->protocolo . '://';
            if ($this->fuente_csv->usuario != '' && $this->fuente_csv->password != '') {
                $url .= $this->fuente_csv->usuario . ':' . urlencode($this->fuente_csv->password) . '@';
            }
            $url .= $this->fuente_csv->url;

            $this->messages[] = "Comprobando: " . $url;
            $data = $this->curl_get_contents($url, $this->fuente_csv->codificacion);
        }

        $this->fuente_csv->estado = 'error';
        if ($data) {
            $this->fuente_csv->estado = 'correcto';
            $this->procesar_articulos($data);
        } else {
            $this->errors[] = 'No se ha podido leer la fuente.';
        }

        $this->fuente_csv->ultima_comprobacion = time();
        $this->fuente_csv->save();
    }

    /**
     * 
     * @return bool
     */
    public function post_procesar()
    {
        $continuar = FALSE;

        $this->next_offset = $this->offset;
        foreach ($this->articulo->all($this->offset, $this->limit2) as $art) {
            $aprops = $this->articulo_prop->array_get($art->referencia);
            if (isset($aprops['url_img'])) {
                $this->descargar_imagen($art, $aprops['url_img']);
                $this->articulo_prop->simple_delete($art->referencia, 'url_img');
            }

            $continuar = TRUE;
            $this->next_offset++;
        }

        if ($continuar) {
            if ($this->fuente_csv) {
                $this->url_recarga = $this->fuente_csv->url() . '&pprocesar=TRUE&offset2=' . $this->next_offset;
                $this->messages[] = 'Recargando... &nbsp; <i class="fa fa-refresh fa-spin"></i>';
            }
        } else {
            $this->advices[] = 'Terminado <span class="glyphicon glyphicon-ok"></span>';
            $this->next_offset = 0;
        }

        return $continuar;
    }

    private function get_codfabricante($txt)
    {
        if ($txt == '') {
            return NULL;
        }

        $fab0 = new fabricante();
        $code = preg_replace('/[^(\x20-\x7F)]*/', '', $fab0->no_html($txt));
        $codfabricante = (strlen($code) > 8) ? substr($code, 0, 8) : $code;

        /// ¿Existe ya el fabricante?
        if ($codfabricante == $this->last_codfabricante) {
            return $this->last_codfabricante;
        }

        $fabricante = $fab0->get($codfabricante);
        if ($fabricante) {
            $this->last_codfabricante = $fabricante->codfabricante;
            return $fabricante->codfabricante;
        }

        /// creamos el fabricante
        $fab0->codfabricante = $codfabricante;
        $fab0->nombre = $txt;
        if ($fab0->save()) {
            $this->last_codfabricante = $fab0->codfabricante;
            return $this->last_codfabricante;
        }

        return NULL;
    }

    private function get_codfamilia($txt)
    {
        if ($txt == '') {
            return NULL;
        }

        $fam0 = new familia();
        $code = preg_replace('/[^(\x20-\x7F)]*/', '', $fam0->no_html($txt));
        $codfamilia = (strlen($code) > 8) ? substr($code, 0, 8) : $code;

        /// ¿Existe ya la familia?
        if ($codfamilia == $this->last_codfamilia) {
            return $this->last_codfamilia;
        }

        $familia = $fam0->get($codfamilia);
        if ($familia) {
            $this->last_codfamilia = $familia->codfamilia;
            return $familia->codfamilia;
        }

        /// creamos la familia
        $fam0->codfamilia = $codfamilia;
        $fam0->descripcion = $txt;
        if ($fam0->save()) {
            $this->last_codfamilia = $fam0->codfamilia;
            return $this->last_codfamilia;
        }

        return NULL;
    }

    private function procesar_articulos(&$data)
    {
        $impuesto = $this->last_impuesto->get($this->fuente_csv->codimpuesto);
        if ($impuesto) {
            $this->last_impuesto = $impuesto;
        }

        $plinea = FALSE;
        $numlinea = 0;

        $fin = TRUE;
        foreach (preg_split('/\n|\r\n?/', $data) as $aux) {
            $linea = $this->custom_explode($this->fuente_csv->separador, $aux);

            if (!$plinea) {
                $plinea = $linea;
            } else if ($numlinea >= $this->offset + $this->limit) {
                $this->next_offset = $this->offset + $this->limit;
                $this->url_recarga = $this->fuente_csv->url() . '&empezar=TRUE&offset2=' . $this->next_offset
                    . '&nuevos=' . $this->status['nuevos'] . '&nuevosp=' . $this->status['nuevosp']
                    . '&actualizados=' . $this->status['actualizados'] . '&actualizadosp=' . $this->status['actualizadosp'];
                $fin = FALSE;
                break;
            } else if (count($linea) == count($plinea) && $numlinea >= $this->offset) {
                $nlinea = array(
                    'codproveedor' => NULL,
                    'ref_prov' => NULL,
                    'ref' => NULL,
                    'desc' => NULL,
                    'codimpuesto' => $this->last_impuesto->codimpuesto,
                    'precio' => NULL,
                    'precio_compra' => NULL,
                    'dto_compra' => NULL,
                    'precio_coste' => NULL,
                    'precio_tarifa1' => NULL,
                    'precio_tarifa2' => NULL,
                    'precio_tarifa3' => NULL,
                    'stock' => NULL,
                    'nostock' => NULL,
                    'stockmin' => NULL,
                    'stockmax' => NULL,
                    'ventasinstock' => NULL,
                    'barras' => NULL,
                    'fabricante' => NULL,
                    'familia' => NULL,
                    'equivalencia' => NULL,
                    'partnumber' => NULL,
                    'secompra' => NULL,
                    'sevende' => NULL,
                    'bloqueado' => NULL,
                    'publico' => NULL,
                    'observaciones' => NULL,
                    'factualizado' => NULL,
                    'url_img' => NULL,
                    'id_prestashop' => NULL,
                    'ps_activo' => NULL,
                    'desc_corta' => NULL,
                    'desc_larga' => NULL,
                    'ps_anchura' => NULL,
                    'ps_altura' => NULL,
                    'ps_profundidad' => NULL,
                    'ps_peso' => NULL,
                    'ps_gastos_envio' => NULL,
                    'ps_redireccion' => NULL,
                    'ps_redireccion_id' => NULL,
                    'ps_precio' => NULL,
                    'ps_oferta' => NULL,
                    'ps_oferta_desde' => NULL,
                    'ps_oferta_hasta' => NULL,
                    'ps_txt_no_disponible' => NULL,
                    'meta_title' => NULL,
                    'meta_description' => NULL,
                    'meta_keywords' => NULL,
                    'ps_factualizado' => NULL,
                );

                foreach ($plinea as $i => $pl) {
                    if ($pl == $this->fuente_csv->col_codproveedor && $this->fuente_csv->col_codproveedor != '') {
                        $nlinea['codproveedor'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ref_prov && $this->fuente_csv->col_ref_prov != '') {
                        $nlinea['ref_prov'] = substr($linea[$i], 0, 18);
                    }

                    if ($pl == $this->fuente_csv->col_ref && $this->fuente_csv->col_ref != '') {
                        $nlinea['ref'] = substr(str_replace(' ', '_', $linea[$i] . $this->fuente_csv->sufijo), 0, 18);
                    }

                    if ($pl == $this->fuente_csv->col_desc && $this->fuente_csv->col_desc != '') {
                        $nlinea['desc'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_iva && $this->fuente_csv->col_iva != '') {
                        $iva = $this->custom_floatval($linea[$i]);
                        if ($iva != $this->last_impuesto->iva) {
                            $impuesto = $this->last_impuesto->get_by_iva($iva);
                            if ($impuesto) {
                                $nlinea['codimpuesto'] = $impuesto->codimpuesto;
                                $this->last_impuesto = $impuesto;
                            }
                        }
                    }

                    if ($pl == $this->fuente_csv->col_precio_compra && $this->fuente_csv->col_precio_compra != '') {
                        $nlinea['precio_compra'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_dto_compra && $this->fuente_csv->col_dto_compra != '') {
                        $nlinea['dto_compra'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_coste && $this->fuente_csv->col_precio_coste != '') {
                        $nlinea['precio_coste'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio && $this->fuente_csv->col_precio != '') {
                        $nlinea['precio'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa1 && $this->fuente_csv->col_precio_tarifa1 != '') {
                        $nlinea['precio_tarifa1'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa2 && $this->fuente_csv->col_precio_tarifa2 != '') {
                        $nlinea['precio_tarifa2'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_precio_tarifa3 && $this->fuente_csv->col_precio_tarifa3 != '') {
                        $nlinea['precio_tarifa3'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stock && $this->fuente_csv->col_stock != '') {
                        $nlinea['stock'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_nostock && $this->fuente_csv->col_nostock != '') {
                        $nlinea['nostock'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stockmin && $this->fuente_csv->col_stockmin != '') {
                        $nlinea['stockmin'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_stockmax && $this->fuente_csv->col_stockmax != '') {
                        $nlinea['stockmax'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ventasinstock && $this->fuente_csv->col_ventasinstock != '') {
                        $nlinea['ventasinstock'] = intval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_barras && $this->fuente_csv->col_barras != '') {
                        $nlinea['barras'] = substr($linea[$i], 0, 18);
                    }

                    if ($pl == $this->fuente_csv->col_fabricante && $this->fuente_csv->col_fabricante != '') {
                        $nlinea['fabricante'] = $this->get_codfabricante($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_familia && $this->fuente_csv->col_familia != '') {
                        $nlinea['familia'] = $this->get_codfamilia($linea[$i]);
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
                        $nlinea['ps_oferta'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_oferta_desde && $this->fuente_csv->col_ps_oferta_desde != '') {
                        $nlinea['ps_oferta_desde'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_oferta_hasta && $this->fuente_csv->col_ps_oferta_hasta != '') {
                        $nlinea['ps_oferta_hasta'] = $linea[$i];
                    }

                    if ($pl == $this->fuente_csv->col_ps_anchura && $this->fuente_csv->col_ps_anchura != '') {
                        $nlinea['ps_anchura'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_altura && $this->fuente_csv->col_ps_altura != '') {
                        $nlinea['ps_altura'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_profundidad && $this->fuente_csv->col_ps_profundidad != '') {
                        $nlinea['ps_profundidad'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_peso && $this->fuente_csv->col_ps_peso != '') {
                        $nlinea['ps_peso'] = $this->custom_floatval($linea[$i]);
                    }

                    if ($pl == $this->fuente_csv->col_ps_gastos_envio && $this->fuente_csv->col_ps_gastos_envio != '') {
                        $nlinea['ps_gastos_envio'] = $this->custom_floatval($linea[$i]);
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

                $continuar = TRUE;
                if ($this->fuente_csv->perfil == 1) {
                    $continuar = $this->procesar_perfil_1($nlinea);
                } else if ($this->fuente_csv->perfil == 2) {
                    $continuar = $this->procesar_perfil_2($nlinea);
                } else if ($this->fuente_csv->perfil == 3) {
                    $continuar = $this->procesar_perfil_3($nlinea);
                }

                if (!$continuar) {
                    $this->errors[] = 'Ha habido un error al procesar el archivo.';
                    break;
                }
            }

            $numlinea++;
        }

        $this->messages[] = $this->status['nuevos'] . ' artículos añadidos. ' . $this->status['actualizados'] . ' actualizados.';
        $this->messages[] = $this->status['nuevosp'] . ' artículos de proveedor añadidos. ' . $this->status['actualizadosp'] . ' actualizados.';

        if ($fin) {
            $this->messages[] = 'Recargando... &nbsp; <i class="fa fa-refresh fa-spin"></i>';
            $this->url_recarga = $this->fuente_csv->url() . '&pprocesar=TRUE';
            $this->next_offset = 0;
        } else {
            $this->messages[] = 'Recargando... &nbsp; <i class="fa fa-refresh fa-spin"></i>';
        }
    }

    /**
     * Añadir artículos a mi catálogo y actualizar los que ya tengo.
     * @param array $linea
     *
     * @return bool
     */
    private function procesar_perfil_1($linea)
    {
        $continuar = TRUE;

        if (!is_null($linea['ref'])) {
            $articulo = $this->articulo->get($linea['ref']);
            if ($articulo) {
                $this->status['actualizados'] ++;
            } else {
                $articulo = new articulo();
                $articulo->referencia = $linea['ref'];
                $this->status['nuevos'] ++;
            }

            if (!is_null($linea['desc'])) {
                $articulo->descripcion = $linea['desc'];
            }

            $articulo->codimpuesto = $linea['codimpuesto'];

            if (!is_null($linea['precio_coste'])) {
                $articulo->costemedio = $articulo->preciocoste = $linea['precio_coste'];
            }

            if (is_numeric($linea['precio'])) {
                $pvp = $linea['precio'];
                if ($this->fuente_csv->pvp_max) {
                    if ($this->fuente_csv->con_iva) {
                        $pvp = max(array($pvp, $articulo->pvp_iva()));
                    } else {
                        $pvp = max(array($pvp, $articulo->pvp));
                    }
                }

                if ($this->fuente_csv->con_iva) {
                    $articulo->set_pvp_iva($pvp);
                } else {
                    $articulo->set_pvp($pvp);
                }
            }

            foreach (['stockmin', 'stockmax', 'equivalencia', 'partnumber', 'observaciones', 'factualizado'] as $key) {
                if (!is_null($linea[$key])) {
                    $articulo->{$key} = $linea[$key];
                }
            }

            foreach (['nostock', 'secompra', 'sevende', 'bloqueado', 'publico'] as $key) {
                if (!is_null($linea[$key])) {
                    $articulo->{$key} = (bool) $linea[$key];
                }
            }

            if (!is_null($linea['ventasinstock'])) {
                $articulo->controlstock = (bool) $linea['ventasinstock'];
            }

            if (!is_null($linea['barras'])) {
                $articulo->codbarras = $linea['barras'];
            }

            if (!is_null($linea['fabricante'])) {
                $articulo->codfabricante = ($linea['fabricante'] != '') ? $linea['fabricante'] : NULL;
            }

            if (!is_null($linea['familia'])) {
                $articulo->codfamilia = ($linea['familia'] != '') ? $linea['familia'] : NULL;
            }

            if ($articulo->save()) {
                if (!is_null($linea['stock'])) {
                    $articulo->set_stock($this->fuente_csv->codalmacen, $linea['stock']);
                }

                if (class_exists('tarifa_articulo')) {
                    $this->procesar_tarifas($linea['ref'], $linea);
                }

                $aprops = [];
                $posibles = array(
                    'url_img', 'id_prestashop', 'ps_activo', 'desc_corta', 'desc_larga', 'ps_anchura',
                    'ps_altura', 'ps_profundidad', 'ps_peso', 'ps_gastos_envio', 'ps_redireccion',
                    'ps_redireccion_id', 'ps_precio', 'ps_oferta', 'ps_oferta_desde', 'ps_oferta_hasta',
                    'ps_txt_no_disponible', 'meta_title', 'meta_description', 'meta_keywords', 'ps_factualizado'
                );

                foreach ($posibles as $pos) {
                    if (!is_null($linea[$pos])) {
                        $aprops[$pos] = $linea[$pos];
                    }
                }

                if ($aprops) {
                    $this->articulo_prop->array_save($articulo->referencia, $aprops);
                }
            } else {
                $continuar = FALSE;
            }
        } else {
            $continuar = FALSE;
        }

        return $continuar;
    }

    /**
     * Solamente actualizar los artículos de mi catálogo: no se añadirán artículos nuevos.
     * Además se guarda los datos del proveedor.
     *
     * @param array $linea
     *
     * @return bool
     */
    private function procesar_perfil_2($linea)
    {
        $continuar = TRUE;

        $ref = $linea['ref'];
        $codproveedor = $this->fuente_csv->codproveedor;

        /// el codproveedor lo podemos tener o de la fuene o de la línea.
        if (!is_null($linea['codproveedor'])) {
            if (is_numeric($linea['codproveedor'])) {
                $codproveedor = $linea['codproveedor'];
            } else {
                foreach ($this->proveedores as $pro) {
                    if ($pro->nombre == $linea['codproveedor']) {
                        $codproveedor = $pro->codproveedor;
                        break;
                    }
                }
            }
        }

        /**
         * Si nos proporcionan una ref_prov y un proveedor, buscamos el artículo
         * relacionado para actualizarlo.
         */
        if (!is_null($linea['ref_prov']) && !is_null($codproveedor)) {
            $artp = $this->articulo_prov->get_by($ref, $codproveedor, $linea['ref_prov']);
            if ($artp) {
                $ref = $artp->referencia;

                if (!is_null($linea['precio_compra'])) {
                    if ($this->fuente_csv->compra_con_iva) {
                        $artp->precio = round((100 * $linea['precio_compra']) / (100 + $artp->get_iva()), FS_NF0 + 2);
                    } else {
                        $artp->precio = $linea['precio_compra'];
                    }
                }

                if (!is_null($linea['dto_compra'])) {
                    $artp->dto = $linea['dto_compra'];
                }

                if (!is_null($linea['precio_compra']) || !is_null($linea['dto_compra'])) {
                    $artp->save();
                    $this->status['actualizadosp'] ++;
                }
            }
        }

        if (!is_null($ref)) {
            $articulo = $this->articulo->get($ref);
            if ($articulo) {
                $this->status['actualizados'] ++;

                if (!is_null($linea['desc'])) {
                    $articulo->descripcion = $linea['desc'];
                }

                if (!is_null($linea['precio_coste'])) {
                    $articulo->costemedio = $articulo->preciocoste = $linea['precio_coste'];
                }

                if (is_numeric($linea['precio'])) {
                    $pvp = $linea['precio'];
                    if ($this->fuente_csv->pvp_max) {
                        if ($this->fuente_csv->con_iva) {
                            $pvp = max(array($pvp, $articulo->pvp_iva()));
                        } else {
                            $pvp = max(array($pvp, $articulo->pvp));
                        }
                    }

                    if ($this->fuente_csv->con_iva) {
                        $articulo->set_pvp_iva($pvp);
                    } else {
                        $articulo->set_pvp($pvp);
                    }
                }

                foreach (['stockmin', 'stockmax', 'equivalencia', 'partnumber', 'observaciones', 'factualizado'] as $key) {
                    if (!is_null($linea[$key])) {
                        $articulo->{$key} = $linea[$key];
                    }
                }

                foreach (['nostock', 'secompra', 'sevende', 'bloqueado', 'publico'] as $key) {
                    if (!is_null($linea[$key])) {
                        $articulo->{$key} = (bool) $linea[$key];
                    }
                }

                if (!is_null($linea['ventasinstock'])) {
                    $articulo->controlstock = (bool) $linea['ventasinstock'];
                }

                if (!is_null($linea['barras'])) {
                    $articulo->codbarras = $linea['barras'];
                }

                if (!is_null($linea['fabricante'])) {
                    $articulo->codfabricante = ($linea['fabricante'] != '') ? $linea['fabricante'] : NULL;
                }

                if (!is_null($linea['familia'])) {
                    $articulo->codfamilia = ($linea['familia'] != '') ? $linea['familia'] : NULL;
                }

                if ($articulo->save()) {
                    if (!is_null($linea['stock'])) {
                        $articulo->set_stock($this->fuente_csv->codalmacen, $linea['stock']);
                    }

                    if (class_exists('tarifa_articulo')) {
                        $this->procesar_tarifas($linea['ref'], $linea);
                    }

                    $aprops = [];
                    $posibles = array(
                        'url_img', 'id_prestashop', 'ps_activo', 'desc_corta', 'desc_larga', 'ps_anchura',
                        'ps_altura', 'ps_profundidad', 'ps_peso', 'ps_gastos_envio', 'ps_redireccion',
                        'ps_redireccion_id', 'ps_precio', 'ps_oferta', 'ps_oferta_desde', 'ps_oferta_hasta',
                        'ps_txt_no_disponible', 'meta_title', 'meta_description', 'meta_keywords', 'ps_factualizado'
                    );

                    foreach ($posibles as $pos) {
                        if (!is_null($linea[$pos])) {
                            $aprops[$pos] = $linea[$pos];
                        }
                    }

                    if ($aprops) {
                        $this->articulo_prop->array_save($articulo->referencia, $aprops);
                    }
                } else {
                    $continuar = FALSE;
                }
            }
        }

        return $continuar;
    }

    /**
     * Actualizar el catálogo del proveedor, accesible desde Compras > Artículos.
     * 
     * @param array $linea
     *
     * @return bool
     */
    private function procesar_perfil_3($linea)
    {
        $continuar = TRUE;

        /// podemos obtener el proveedor de la fuente o de la linea.
        $codproveedor = $this->fuente_csv->codproveedor;
        if (!is_null($linea['codproveedor'])) {
            if (is_numeric($linea['codproveedor'])) {
                $codproveedor = $linea['codproveedor'];
            } else {
                foreach ($this->proveedores as $pro) {
                    if ($pro->nombre == $linea['codproveedor']) {
                        $codproveedor = $pro->codproveedor;
                        break;
                    }
                }
            }
        }

        if (!is_null($linea['ref_prov']) && !is_null($codproveedor)) {
            $articulop = $this->articulo_prov->get_by($linea['ref'], $codproveedor, $linea['ref_prov']);
            if ($articulop) {
                $this->status['actualizadosp'] ++;
            } else {
                $articulop = new articulo_proveedor();
                $articulop->codproveedor = $codproveedor;
                $articulop->referencia = $linea['ref'];
                $articulop->refproveedor = $linea['ref_prov'];
                $this->status['nuevosp'] ++;
            }

            $articulop->codimpuesto = $linea['codimpuesto'];

            if (!is_null($linea['desc'])) {
                $articulop->descripcion = $linea['desc'];
            }

            if (!is_null($linea['barras'])) {
                $articulop->codbarras = $linea['barras'];
            }

            if (!is_null($linea['partnumber'])) {
                $articulop->partnumber = $linea['partnumber'];
            }

            if (is_numeric($linea['stock'])) {
                $articulop->stock = $linea['stock'];
                $articulop->nostock = FALSE;
            } else {
                $articulop->stock = 0;
                $articulop->nostock = TRUE;
            }

            if (is_numeric($linea['precio_compra'])) {
                $articulop->precio = $linea['precio_compra'];
                if ($this->fuente_csv->compra_con_iva) {
                    $articulop->precio = round((100 * $linea['precio_compra']) / (100 + $articulop->get_iva()), FS_NF0 + 2);
                }
            }

            if (is_numeric($linea['dto_compra'])) {
                $articulop->dto = $linea['dto_compra'];
            }

            if ($articulop->save()) {
                $aprops = [];
                $posibles = array(
                    'url_img', 'desc_corta', 'desc_larga', 'ps_anchura', 'ps_altura', 'ps_profundidad', 'ps_peso',
                    'ps_gastos_envio', 'ps_precio', 'meta_title', 'meta_description', 'meta_keywords'
                );

                foreach ($posibles as $pos) {
                    if (!is_null($linea[$pos])) {
                        $aprops[$pos] = $linea[$pos];
                    }
                }

                if ($aprops) {
                    $this->compras_prop->array_save($articulop->id, $aprops);
                }
            } else {
                $continuar = FALSE;
            }
        } else {
            $continuar = FALSE;

            if (is_null($codproveedor)) {
                $this->errors[] = 'Ningún proveedor encontrado. Se necesita especificar'
                    . ' el proveedor de la fuente para actualizar el catálogo del proveedor.';
            }
        }

        return $continuar;
    }

    private function procesar_tarifas($ref, $linea)
    {
        $tara0 = new tarifa_articulo();

        foreach ($this->tarifas as $i => $tar) {
            if (!is_null($linea['precio_tarifa' . ($i + 1)])) {
                $tara = $tara0->get_by($ref, $tar->codtarifa);
                if (!$tara) {
                    $tara = new tarifa_articulo();
                    $tara->codtarifa = $tar->codtarifa;
                    $tara->referencia = $ref;
                }
                $tara->pvp = $linea['precio_tarifa' . ($i + 1)];
                $tara->save();
            }
        }
    }

    /**
     * Descarga y asigna una imagen a un artículo.
     * @param \articulo $art
     * @param string    $url
     */
    private function descargar_imagen(&$art, $url)
    {
        if ($url) {
            /// reemplazamos los espacios
            $url = str_replace(' ', '%20', $url);

            $data = $this->curl_get_contents($url);
            if ($data) {
                if (substr(strtolower($url), -3) == 'png') {
                    $art->set_imagen($data, TRUE);
                } else if (substr(strtolower($url), -3) == 'jpg') {
                    $art->set_imagen($data, FALSE);
                } else if (substr(strtolower($url), -4) == 'jpeg') {
                    $art->set_imagen($data, FALSE);
                }

                $this->messages[] = 'Imagen ' . $url . ' descargada.';
            }
        }
    }
}
