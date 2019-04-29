<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/lib/core_importador.php';

class cron_fuentes_csv
{

    private $compras_setup;

    public function __construct()
    {
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

        $core = new core_importador();
        $core->limit = 50000;
        $core->limit2 = 200;

        $pprocesar = FALSE;
        $fuente = new fuente_csv();
        foreach ($fuente->all() as $fu) {
            if ($fu->cron) {
                if (is_null($fu->cron_hour) OR $fu->cron_hour == date('H')) {
                    do {
                        $core->offset = $core->next_offset;
                        $core->procesar_fuente($fu);
                        echo '.';
                    } while ($core->next_offset != 0);
                }

                $pprocesar = TRUE;
            }
        }

        /// ejecutamos el post-procesado de las fuentes
        while ($pprocesar) {
            $core->offset = $core->next_offset;
            $pprocesar = $core->post_procesar();
            echo '*';
        }

        foreach ($core->errors as $err) {
            echo ' - ERROR - ' . $err . ' - ERROR - ';
        }

        $this->actualizar_articulos();
    }

    private function actualizar_articulos()
    {
        if ($this->compras_setup['iecsv_act_art']) {
            $art0 = new articulo();
            $artp0 = new articulo_proveedor();

            echo "\nActualizando articulos de tu catalogo (ver compras > articulos > opciones)...";

            $actualizados = 0;
            foreach ($artp0->all_con_ref() as $artp) {
                if ($artp->referencia) {
                    $articulo = $art0->get($artp->referencia);
                    if ($articulo) {
                        if ($this->compras_setup['iecsv_act_art_precios']) {
                            $precio = $artp->precio * (100 - $artp->dto) / 100;

                            if ($this->compras_setup['iecsv_act_art_precio'] == 'coste') {
                                $articulo->preciocoste = $articulo->costemedio = $precio;
                            } else {
                                $articulo->set_pvp($precio);
                            }
                        }

                        if ($this->compras_setup['iecsv_act_art_stock']) {
                            $articulo->set_stock($this->compras_setup['iecsv_act_art_alm'], $artp->stock);
                        }

                        $actualizados++;
                    }
                }
            }

            echo $actualizados . ' articulos actualizados.' . "\n";
        }
    }
}

if (!FS_DEMO) {
    new cron_fuentes_csv();
}
