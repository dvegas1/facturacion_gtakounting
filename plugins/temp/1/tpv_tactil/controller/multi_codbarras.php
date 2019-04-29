<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of multi_codbarras
 *
 * @author carlos
 */
class multi_codbarras extends fs_controller
{

    public $referencia;
    public $codigos;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Códigos de barras', 'ventas', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->share_extension();
        $artic_codbar = new articulo_codbarras();

        $this->referencia = NULL;
        if (isset($_REQUEST['ref'])) {
            $this->referencia = $_REQUEST['ref'];
        }

        if (isset($_POST['id'])) {
            $acb2 = $artic_codbar->get($_POST['id']);
            if ($acb2) {
                $acb2->codbarras = $_POST['codbarras'];

                if ($acb2->save()) {
                    $this->new_message('Datos modificados correctamente.');
                } else {
                    $this->new_error_msg('Error al guardar los datos.');
                }
            }
        } else if (isset($_POST['codbarras'])) {
            $artic_codbar->referencia = $this->referencia;
            $artic_codbar->codbarras = $_POST['codbarras'];

            if ($artic_codbar->save()) {
                $this->new_message('Datos guardados correctamente.');
            } else {
                $this->new_error_msg('Error al guardar los datos.');
            }
        } else if (isset($_GET['delete'])) {
            $acb2 = $artic_codbar->get($_GET['delete']);
            if ($acb2) {
                if ($acb2->delete()) {
                    $this->new_message('Datos eliminados correctamente.');
                } else {
                    $this->new_error_msg('Error al eliminar los datos.');
                }
            }
        }

        $this->codigos = $artic_codbar->all_from_ref($this->referencia);
    }

    private function share_extension()
    {
        /// añadimos la pestaña en ventas_articulo
        $fsext = new fs_extension();
        $fsext->name = 'multi_codbar';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_articulo';
        $fsext->type = 'tab';
        $fsext->text = '<span class="glyphicon glyphicon-barcode" aria-hidden="true"></span>';
        $fsext->save();

        /// añadimos la función para buscar en el tpv genérico
        $fsext2 = new fs_extension();
        $fsext2->name = 'tpv_recambios_function';
        $fsext2->from = __CLASS__;
        $fsext2->to = 'tpv_recambios';
        $fsext2->type = 'function';
        $fsext2->text = 'tpv_recambios_new_search';
        $fsext2->params = 'new_search';
        $fsext2->save();
    }
}
