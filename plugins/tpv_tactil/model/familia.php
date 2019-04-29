<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2013-2017  Carlos Garcia Gomez  neorazorx@gmail.com
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */
require_once 'plugins/facturacion_base/model/core/familia.php';

/**
 * Una familia o categoría de artículos.
 * 
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class familia extends FacturaScripts\model\familia
{

    public function imagen_url()
    {
        $filename = str_replace('/', '__', $this->codfamilia);

        if (file_exists("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".png")) {
            return FS_PATH . "tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".png";
        } else if (file_exists("tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".jpg")) {
            return FS_PATH . "tmp/" . FS_TMP_NAME . "images/familias/" . $filename . ".jpg";
        }

        return FS_PATH . 'plugins/tpv_tactil/view/img/folder.png';
    }
}
