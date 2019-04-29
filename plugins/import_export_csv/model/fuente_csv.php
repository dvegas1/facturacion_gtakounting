<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2015-2019, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Description of fuente_csv
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class fuente_csv extends fs_model
{

    public $id;
    public $url;
    public $protocolo;
    public $usuario;
    public $password;
    public $ultima_comprobacion;
    public $estado;
    public $separador;
    public $codalmacen;
    public $codproveedor;
    public $col_codproveedor;
    public $col_ref_prov;
    public $col_ref;
    public $col_desc;
    public $col_iva;
    public $col_precio;
    public $col_precio_compra;
    public $col_dto_compra;
    public $col_precio_coste;
    public $col_precio_tarifa1;
    public $col_precio_tarifa2;
    public $col_precio_tarifa3;
    public $col_stock;
    public $col_nostock;
    public $col_stockmin;
    public $col_stockmax;
    public $col_ventasinstock;
    public $col_barras;
    public $col_fabricante;
    public $col_familia;
    public $col_equivalencia;
    public $col_secompra;
    public $col_sevende;
    public $col_bloqueado;
    public $col_publico;
    public $col_observaciones;
    public $col_factualizado;
    public $col_url_img;
    public $col_id_prestashop;
    public $col_ps_activo;
    public $col_desc_corta;
    public $col_desc_larga;
    public $col_ps_anchura;
    public $col_ps_altura;
    public $col_ps_profundidad;
    public $col_ps_peso;
    public $col_ps_gastos_envio;
    public $col_ps_redireccion;
    public $col_ps_redireccion_id;
    public $col_ps_precio;
    public $col_ps_oferta;
    public $col_ps_oferta_desde;
    public $col_ps_oferta_hasta;
    public $col_ps_txt_no_disponible;
    public $col_meta_title;
    public $col_meta_descrip;
    public $col_meta_keys;
    public $col_ps_factualizado;
    public $codificacion;
    public $cron;
    public $cron_hour;
    public $codimpuesto;
    public $compra_con_iva;
    public $con_iva;
    public $pvp_max;
    public $sufijo;
    public $perfil;
    public $col_partnumber;

    public function __construct($data = FALSE)
    {
        parent::__construct('fuentes_csv');
        if ($data) {
            $this->id = $this->intval($data['id']);
            $this->url = $data['url'];
            $this->protocolo = $data['protocolo'];
            $this->usuario = $data['usuario'];
            $this->password = $data['password'];
            $this->ultima_comprobacion = $this->intval($data['ultima_comprobacion']);
            $this->estado = $data['estado'];
            $this->separador = $data['separador'];
            $this->codalmacen = $data['codalmacen'];
            $this->codproveedor = $data['codproveedor'];
            $this->col_codproveedor = $data['col_codproveedor'];
            $this->col_ref_prov = $data['col_ref_prov'];
            $this->col_ref = $data['col_ref'];
            $this->col_desc = $data['col_desc'];
            $this->col_iva = $data['col_iva'];
            $this->col_precio = $data['col_precio'];
            $this->col_precio_compra = $data['col_precio_compra'];
            $this->col_dto_compra = $data['col_dto_compra'];
            $this->col_precio_coste = $data['col_precio_coste'];
            $this->col_precio_tarifa1 = $data['col_precio_tarifa1'];
            $this->col_precio_tarifa2 = $data['col_precio_tarifa2'];
            $this->col_precio_tarifa3 = $data['col_precio_tarifa3'];
            $this->col_stock = $data['col_stock'];
            $this->col_nostock = $data['col_nostock'];
            $this->col_stockmin = $data['col_stockmin'];
            $this->col_stockmax = $data['col_stockmax'];
            $this->col_ventasinstock = $data['col_ventasinstock'];
            $this->col_barras = $data['col_barras'];
            $this->col_fabricante = $data['col_fabricante'];
            $this->col_familia = $data['col_familia'];
            $this->col_equivalencia = $data['col_equivalencia'];
            $this->col_partnumber = $data['col_partnumber'];
            $this->col_secompra = $data['col_secompra'];
            $this->col_sevende = $data['col_sevende'];
            $this->col_bloqueado = $data['col_bloqueado'];
            $this->col_publico = $data['col_publico'];
            $this->col_observaciones = $data['col_observaciones'];
            $this->col_factualizado = $data['col_factualizado'];
            $this->col_url_img = $data['col_url_img'];

            $this->col_id_prestashop = $data['col_id_prestashop'];
            $this->col_ps_activo = $data['col_ps_activo'];
            $this->col_desc_corta = $data['col_desc_corta'];
            $this->col_desc_larga = $data['col_desc_larga'];
            $this->col_ps_anchura = $data['col_ps_anchura'];
            $this->col_ps_altura = $data['col_ps_altura'];
            $this->col_ps_profundidad = $data['col_ps_profundidad'];
            $this->col_ps_peso = $data['col_ps_peso'];
            $this->col_ps_gastos_envio = $data['col_ps_gastos_envio'];
            $this->col_ps_redireccion = $data['col_ps_redireccion'];
            $this->col_ps_redireccion_id = $data['col_ps_redireccion_id'];
            $this->col_ps_precio = $data['col_ps_precio'];
            $this->col_ps_oferta = $data['col_ps_oferta'];
            $this->col_ps_oferta_desde = $data['col_ps_oferta_desde'];
            $this->col_ps_oferta_hasta = $data['col_ps_oferta_hasta'];
            $this->col_ps_txt_no_disponible = $data['col_ps_txt_no_disponible'];
            $this->col_meta_title = $data['col_meta_title'];
            $this->col_meta_descrip = $data['col_meta_descrip'];
            $this->col_meta_keys = $data['col_meta_keys'];
            $this->col_ps_factualizado = $data['col_ps_factualizado'];
            $this->codificacion = $data['codificacion'];
            $this->cron = $this->str2bool($data['cron']);
            $this->cron_hour = $this->intval($data['cron_hour']);
            $this->codimpuesto = $data['codimpuesto'];
            $this->compra_con_iva = $this->str2bool($data['compra_con_iva']);
            $this->con_iva = $this->str2bool($data['con_iva']);
            $this->pvp_max = $this->str2bool($data['pvp_max']);
            $this->sufijo = $data['sufijo'];
            $this->perfil = intval($data['perfil']);
        } else {
            $this->id = NULL;
            $this->url = '';
            $this->protocolo = 'http';
            $this->usuario = '';
            $this->password = '';
            $this->ultima_comprobacion = NULL;
            $this->estado = 'sin comprobar';
            $this->separador = ';';
            $this->codalmacen = $this->default_items->codalmacen();
            $this->codproveedor = NULL;
            $this->col_codproveedor = '';
            $this->col_ref_prov = '';
            $this->col_ref = '';
            $this->col_desc = '';
            $this->col_iva = '';
            $this->col_precio = '';
            $this->col_precio_compra = '';
            $this->col_dto_compra = '';
            $this->col_precio_coste = '';
            $this->col_precio_tarifa1 = '';
            $this->col_precio_tarifa2 = '';
            $this->col_precio_tarifa3 = '';
            $this->col_stock = '';
            $this->col_nostock = '';
            $this->col_stockmin = '';
            $this->col_stockmax = '';
            $this->col_ventasinstock = '';
            $this->col_barras = '';
            $this->col_fabricante = '';
            $this->col_familia = '';
            $this->col_equivalencia = '';
            $this->col_partnumber = '';
            $this->col_secompra = '';
            $this->col_sevende = '';
            $this->col_bloqueado = '';
            $this->col_publico = '';
            $this->col_observaciones = '';
            $this->col_factualizado = '';
            $this->col_url_img = '';

            $this->col_id_prestashop = '';
            $this->col_ps_activo = '';
            $this->col_desc_corta = '';
            $this->col_desc_larga = '';
            $this->col_ps_anchura = '';
            $this->col_ps_altura = '';
            $this->col_ps_profundidad = '';
            $this->col_ps_peso = '';
            $this->col_ps_gastos_envio = '';
            $this->col_ps_redireccion = '';
            $this->col_ps_redireccion_id = '';
            $this->col_ps_precio = '';
            $this->col_ps_oferta = '';
            $this->col_ps_oferta_desde = '';
            $this->col_ps_oferta_hasta = '';
            $this->col_ps_txt_no_disponible = '';
            $this->col_meta_title = '';
            $this->col_meta_descrip = '';
            $this->col_meta_keys = '';
            $this->col_ps_factualizado = '';
            $this->codificacion = 0;
            $this->cron = TRUE;
            $this->cron_hour = NULL;
            $this->codimpuesto = NULL;
            $this->compra_con_iva = FALSE;
            $this->con_iva = FALSE;
            $this->pvp_max = FALSE;
            $this->sufijo = '';
            $this->perfil = 1;
        }
    }

    protected function install()
    {
        return '';
    }

    public function url()
    {
        if (is_null($this->id)) {
            return 'index.php?page=importador_articulos_csv';
        }

        return 'index.php?page=importador_articulos_csv&id=' . $this->id;
    }

    public function timesince()
    {
        if (!is_null($this->ultima_comprobacion)) {
            $time = time() - $this->ultima_comprobacion;

            if ($time <= 60) {
                return 'hace ' . round($time / 60, 0) . ' segundos';
            } else if (60 < $time && $time <= 3600) {
                return 'hace ' . round($time / 60, 0) . ' minutos';
            } else if (3600 < $time && $time <= 86400) {
                return 'hace ' . round($time / 3600, 0) . ' horas';
            } else if (86400 < $time && $time <= 604800) {
                return 'hace ' . round($time / 86400, 0) . ' dias';
            } else if (604800 < $time && $time <= 2592000) {
                return 'hace ' . round($time / 604800, 0) . ' semanas';
            } else if (2592000 < $time && $time <= 29030400) {
                return 'hace ' . round($time / 2592000, 0) . ' meses';
            } else if ($time > 29030400) {
                return 'hace más de un año';
            }
        }

        return 'fecha desconocida';
    }

    public function get($id)
    {
        $data = $this->db->select("SELECT * FROM fuentes_csv WHERE id = " . $this->var2str($id) . ";");
        if ($data) {
            return new fuente_csv($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM fuentes_csv WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function save()
    {
        if ($this->exists()) {
            $sql = "UPDATE fuentes_csv SET url = " . $this->var2str($this->url) .
                ", protocolo = " . $this->var2str($this->protocolo) .
                ", usuario = " . $this->var2str($this->usuario) .
                ", password = " . $this->var2str($this->password) .
                ", ultima_comprobacion = " . $this->var2str($this->ultima_comprobacion) .
                ", estado = " . $this->var2str($this->estado) .
                ", separador = " . $this->var2str($this->separador) .
                ", codalmacen = " . $this->var2str($this->codalmacen) .
                ", codproveedor = " . $this->var2str($this->codproveedor) .
                ", col_codproveedor = " . $this->var2str($this->col_codproveedor) .
                ", col_ref_prov = " . $this->var2str($this->col_ref_prov) .
                ", col_ref = " . $this->var2str($this->col_ref) .
                ", col_desc = " . $this->var2str($this->col_desc) .
                ", col_iva = " . $this->var2str($this->col_iva) .
                ", col_precio = " . $this->var2str($this->col_precio) .
                ", col_precio_compra = " . $this->var2str($this->col_precio_compra) .
                ", col_dto_compra = " . $this->var2str($this->col_dto_compra) .
                ", col_precio_coste = " . $this->var2str($this->col_precio_coste) .
                ", col_precio_tarifa1 = " . $this->var2str($this->col_precio_tarifa1) .
                ", col_precio_tarifa2 = " . $this->var2str($this->col_precio_tarifa2) .
                ", col_precio_tarifa3 = " . $this->var2str($this->col_precio_tarifa3) .
                ", col_stock = " . $this->var2str($this->col_stock) .
                ", col_nostock = " . $this->var2str($this->col_nostock) .
                ", col_stockmin = " . $this->var2str($this->col_stockmin) .
                ", col_stockmax = " . $this->var2str($this->col_stockmax) .
                ", col_ventasinstock = " . $this->var2str($this->col_ventasinstock) .
                ", col_barras = " . $this->var2str($this->col_barras) .
                ", col_fabricante = " . $this->var2str($this->col_fabricante) .
                ", col_familia = " . $this->var2str($this->col_familia) .
                ", col_equivalencia = " . $this->var2str($this->col_equivalencia) .
                ", col_partnumber = " . $this->var2str($this->col_partnumber) .
                ", col_secompra = " . $this->var2str($this->col_secompra) .
                ", col_sevende = " . $this->var2str($this->col_sevende) .
                ", col_bloqueado = " . $this->var2str($this->col_bloqueado) .
                ", col_publico = " . $this->var2str($this->col_publico) .
                ", col_observaciones = " . $this->var2str($this->col_observaciones) .
                ", col_factualizado = " . $this->var2str($this->col_factualizado) .
                ", col_url_img = " . $this->var2str($this->col_url_img) .
                ", col_id_prestashop = " . $this->var2str($this->col_id_prestashop) .
                ", col_ps_activo = " . $this->var2str($this->col_ps_activo) .
                ", col_desc_corta = " . $this->var2str($this->col_desc_corta) .
                ", col_desc_larga = " . $this->var2str($this->col_desc_larga) .
                ", col_ps_anchura = " . $this->var2str($this->col_ps_anchura) .
                ", col_ps_altura = " . $this->var2str($this->col_ps_altura) .
                ", col_ps_profundidad = " . $this->var2str($this->col_ps_profundidad) .
                ", col_ps_peso = " . $this->var2str($this->col_ps_peso) .
                ", col_ps_gastos_envio = " . $this->var2str($this->col_ps_gastos_envio) .
                ", col_ps_redireccion = " . $this->var2str($this->col_ps_redireccion) .
                ", col_ps_redireccion_id = " . $this->var2str($this->col_ps_redireccion_id) .
                ", col_ps_precio = " . $this->var2str($this->col_ps_precio) .
                ", col_ps_oferta = " . $this->var2str($this->col_ps_oferta) .
                ", col_ps_oferta_desde = " . $this->var2str($this->col_ps_oferta_desde) .
                ", col_ps_oferta_hasta = " . $this->var2str($this->col_ps_oferta_hasta) .
                ", col_ps_txt_no_disponible = " . $this->var2str($this->col_ps_txt_no_disponible) .
                ", col_meta_title = " . $this->var2str($this->col_meta_title) .
                ", col_meta_descrip = " . $this->var2str($this->col_meta_descrip) .
                ", col_meta_keys = " . $this->var2str($this->col_meta_keys) .
                ", col_ps_factualizado = " . $this->var2str($this->col_ps_factualizado) .
                ", codificacion = " . $this->var2str($this->codificacion) .
                ", cron = " . $this->var2str($this->cron) .
                ", cron_hour = " . $this->var2str($this->cron_hour) .
                ", codimpuesto = " . $this->var2str($this->codimpuesto) .
                ", compra_con_iva = " . $this->var2str($this->compra_con_iva) .
                ", con_iva = " . $this->var2str($this->con_iva) .
                ", pvp_max = " . $this->var2str($this->pvp_max) .
                ", sufijo = " . $this->var2str($this->sufijo) .
                ", perfil = " . $this->var2str($this->perfil) .
                " WHERE id = " . $this->var2str($this->id) . ";";

            return $this->db->exec($sql);
        }

        $sql = "INSERT INTO fuentes_csv (url,protocolo,usuario,password,ultima_comprobacion,estado,separador,codalmacen,
            codproveedor,col_codproveedor,col_ref_prov,col_ref,col_desc,col_iva,col_precio,col_precio_compra,col_dto_compra,
            col_precio_coste,col_precio_tarifa1,col_precio_tarifa2,col_precio_tarifa3,col_stock,col_nostock,
            col_stockmin,col_stockmax,col_ventasinstock,col_barras,col_fabricante,col_familia,col_equivalencia
            ,col_partnumber,col_secompra,col_sevende,col_bloqueado,col_publico,col_observaciones,col_factualizado,col_url_img
            ,col_id_prestashop,col_ps_activo,col_desc_corta,col_desc_larga,col_ps_anchura,col_ps_altura,col_ps_profundidad,
            col_ps_peso,col_ps_gastos_envio,col_ps_redireccion,col_ps_redireccion_id,col_ps_precio,col_ps_oferta,
            col_ps_oferta_desde,col_ps_oferta_hasta,col_ps_txt_no_disponible,col_meta_title,col_meta_descrip,
            col_meta_keys,col_ps_factualizado,codificacion,cron,cron_hour,codimpuesto,compra_con_iva,con_iva,pvp_max,
            sufijo,perfil) VALUES (" . $this->var2str($this->url) .
            "," . $this->var2str($this->protocolo) .
            "," . $this->var2str($this->usuario) .
            "," . $this->var2str($this->password) .
            "," . $this->var2str($this->ultima_comprobacion) .
            "," . $this->var2str($this->estado) .
            "," . $this->var2str($this->separador) .
            "," . $this->var2str($this->codalmacen) .
            "," . $this->var2str($this->codproveedor) .
            "," . $this->var2str($this->col_codproveedor) .
            "," . $this->var2str($this->col_ref_prov) .
            "," . $this->var2str($this->col_ref) .
            "," . $this->var2str($this->col_desc) .
            "," . $this->var2str($this->col_iva) .
            "," . $this->var2str($this->col_precio) .
            "," . $this->var2str($this->col_precio_compra) .
            "," . $this->var2str($this->col_dto_compra) .
            "," . $this->var2str($this->col_precio_coste) .
            "," . $this->var2str($this->col_precio_tarifa1) .
            "," . $this->var2str($this->col_precio_tarifa2) .
            "," . $this->var2str($this->col_precio_tarifa3) .
            "," . $this->var2str($this->col_stock) .
            "," . $this->var2str($this->col_nostock) .
            "," . $this->var2str($this->col_stockmin) .
            "," . $this->var2str($this->col_stockmax) .
            "," . $this->var2str($this->col_ventasinstock) .
            "," . $this->var2str($this->col_barras) .
            "," . $this->var2str($this->col_fabricante) .
            "," . $this->var2str($this->col_familia) .
            "," . $this->var2str($this->col_equivalencia) .
            "," . $this->var2str($this->col_partnumber) .
            "," . $this->var2str($this->col_secompra) .
            "," . $this->var2str($this->col_sevende) .
            "," . $this->var2str($this->col_bloqueado) .
            "," . $this->var2str($this->col_publico) .
            "," . $this->var2str($this->col_observaciones) .
            "," . $this->var2str($this->col_factualizado) .
            "," . $this->var2str($this->col_url_img) .
            "," . $this->var2str($this->col_id_prestashop) .
            "," . $this->var2str($this->col_ps_activo) .
            "," . $this->var2str($this->col_desc_corta) .
            "," . $this->var2str($this->col_desc_larga) .
            "," . $this->var2str($this->col_ps_anchura) .
            "," . $this->var2str($this->col_ps_altura) .
            "," . $this->var2str($this->col_ps_profundidad) .
            "," . $this->var2str($this->col_ps_peso) .
            "," . $this->var2str($this->col_ps_gastos_envio) .
            "," . $this->var2str($this->col_ps_redireccion) .
            "," . $this->var2str($this->col_ps_redireccion_id) .
            "," . $this->var2str($this->col_ps_precio) .
            "," . $this->var2str($this->col_ps_oferta) .
            "," . $this->var2str($this->col_ps_oferta_desde) .
            "," . $this->var2str($this->col_ps_oferta_hasta) .
            "," . $this->var2str($this->col_ps_txt_no_disponible) .
            "," . $this->var2str($this->col_meta_title) .
            "," . $this->var2str($this->col_meta_descrip) .
            "," . $this->var2str($this->col_meta_keys) .
            "," . $this->var2str($this->col_ps_factualizado) .
            "," . $this->var2str($this->codificacion) .
            "," . $this->var2str($this->cron) .
            "," . $this->var2str($this->cron_hour) .
            "," . $this->var2str($this->codimpuesto) .
            "," . $this->var2str($this->compra_con_iva) .
            "," . $this->var2str($this->con_iva) .
            "," . $this->var2str($this->pvp_max) .
            "," . $this->var2str($this->sufijo) .
            "," . $this->var2str($this->perfil) . ");";

        if ($this->db->exec($sql)) {
            $this->id = $this->db->lastval();
            return TRUE;
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM fuentes_csv WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     * Devuelve todas las fuentes.
     * @return \fuente_csv
     */
    public function all()
    {
        $ilist = [];

        $data = $this->db->select("SELECT * FROM fuentes_csv ORDER BY url ASC;");
        if ($data) {
            foreach ($data as $d) {
                $ilist[] = new fuente_csv($d);
            }
        }

        return $ilist;
    }
}
