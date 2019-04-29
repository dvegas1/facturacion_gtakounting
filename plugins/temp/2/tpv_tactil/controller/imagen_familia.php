<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016-2017, Carlos García Gómez. All Rights Reserved.
 * @copyright 2016-2017, Jorge Casal Lopez. All Rights Reserved.
 */

/**
 * Description of imagen_familia
 *
 * @author carlos
 */
class imagen_familia extends fs_controller
{

    public $familia;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Imagen familia', 'ventas', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->share_extensions();

        $this->familia = FALSE;
        if (isset($_REQUEST['cod'])) {
            $fam = new familia();
            $this->familia = $fam->get($_REQUEST['cod']);
        }

        if ($this->familia) {
            if (isset($_POST['imagen']) && is_uploaded_file($_FILES['fimagen']['tmp_name'])) {
                $filename = str_replace('/', '__', $this->familia->codfamilia);

                if (!file_exists("tmp/" . FS_TMP_NAME . "images/familias")) {
                    mkdir("tmp/" . FS_TMP_NAME . "images/familias", 0777, TRUE);
                } else if (file_exists("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".png")) {
                    unlink("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".png");
                } else if (file_exists("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".jpg")) {
                    unlink("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".jpg");
                }

                if (substr(strtolower($_FILES['fimagen']['name']), -3) == 'png') {
                    copy($_FILES['fimagen']['tmp_name'], "tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".png");
                } else {
                    copy($_FILES['fimagen']['tmp_name'], "tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".jpg");
                }

                $this->new_message('Imagen guardada correctamente.');
            }
        } else {
            $this->new_error_msg('Familia no encontrada.', 'error', FALSE, FALSE);
        }
    }

    private function share_extensions()
    {
        $fsext = new fs_extension();
        $fsext->name = 'imagen_familia';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_familia';
        $fsext->type = 'tab';
        $fsext->text = '<span class="glyphicon glyphicon-picture"></span><span class="hidden-xs">&nbsp; Imagen</span>';
        $fsext->save();
    }

    public function url()
    {
        if ($this->familia) {
            return parent::url() . '&cod=' . $this->familia->codfamilia;
        }

        return parent::url();
    }
}
