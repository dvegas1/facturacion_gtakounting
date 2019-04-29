<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of tpv_tactil_wizard
 *
 * @author carlos
 */
class tpv_tactil_wizard extends fs_controller
{

    public $familia;
    public $forma_pago;
    private $fsvar;
    public $tpv_config;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Asistente', 'TPV', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->fsvar = new fs_var();
        
        if ($this->cluf_ok()) {
            $this->check_menu();

            $this->familia = new familia();
            $this->forma_pago = new forma_pago();
            $this->cargar_config();

            if (isset($_POST['pago_efectivo'])) {
                $this->guardar_config();
            }
        } else {
            $this->template = 'tpv_tactil_cluf';
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
        
        if ($this->fsvar->array_save($this->tpv_config)) {
            $this->new_message('Datos guardados correctamente.');
            header('Location: index.php?page=tpv_tactil');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

    private function cluf_ok()
    {
        if (isset($_GET['cluf_ok'])) {
            $this->fsvar->simple_save('tpv_tactil_cluf', '1');
            return TRUE;
        } else if ($this->fsvar->simple_get('tpv_tactil_cluf')) {
            return TRUE;
        }
        
        return FALSE;
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

    private function check_menu()
    {
        if (!$this->page->get('tpv_tactil')) {
            if (file_exists(__DIR__)) {
                /// activamos las páginas del plugin
                foreach (scandir(__DIR__) as $f) {
                    if (is_string($f) AND strlen($f) > 0 AND ! is_dir($f) AND $f != __CLASS__ . '.php') {
                        $page_name = substr($f, 0, -4);

                        require_once __DIR__ . '/' . $f;
                        $new_fsc = new $page_name();

                        if (!$new_fsc->page->save()) {
                            $this->new_error_msg("Imposible guardar la página " . $page_name);
                        }

                        unset($new_fsc);
                    }
                }
            } else {
                $this->new_error_msg('No se encuentra el directorio ' . __DIR__);
            }

            $this->load_menu(TRUE);
        }
    }
}
