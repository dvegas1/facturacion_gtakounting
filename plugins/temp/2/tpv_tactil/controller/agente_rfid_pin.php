<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016-2017, Carlos García Gómez. All Rights Reserved.
 * @copyright 2016-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of agente_rfid_pin
 *
 * @author carlos
 */
class agente_rfid_pin extends fs_controller
{

    public $agente;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Editar agente', 'admin', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->share_extensions();

        $this->agente = FALSE;
        if (isset($_REQUEST['cod'])) {
            $ag = new agente();
            $this->agente = $ag->get($_REQUEST['cod']);
        }

        if ($this->agente) {
            if (isset($_POST['pin'])) {
                $this->agente->pin = $_POST['pin'];
                $this->agente->rfid = $_POST['rfid'];

                if ($this->agente->save()) {
                    $this->new_message('Datos guardados correctamente.');
                } else {
                    $this->new_error_msg('Error al guardar los datos.');
                }
            }
        } else {
            $this->new_error_msg('Empleado no encontrado.', 'error', FALSE, FALSE);
        }
    }

    private function share_extensions()
    {
        $fsext = new fs_extension();
        $fsext->name = 'tab_rfid_pin_agente';
        $fsext->from = __CLASS__;
        $fsext->to = 'admin_agente';
        $fsext->type = 'tab';
        $fsext->text = '<span class="glyphicon glyphicon-lock"></span> &nbsp; PIN y RFID';
        $fsext->save();
    }

    public function url()
    {
        if ($this->agente) {
            return parent::url() . '&cod=' . $this->agente->codagente;
        }

        return parent::url();
    }
}
