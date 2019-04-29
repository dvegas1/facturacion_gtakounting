<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2015-2019, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/../lib/importador_simple.php';

/**
 * Description of ie_csv_home
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class ie_csv_home extends fs_controller
{

    public $almacen;
    public $codalmacen;
    public $contiene;
    public $familia;
    private $importador;
    public $impuesto;
    public $separador;
    public $serie;

    public function __construct($name = '', $title = 'Importar/exportar CSV', $folder = 'admin', $admin = FALSE, $shmenu = TRUE, $important = FALSE)
    {
        if ($name == '') {
            $name = __CLASS__;
        }

        parent::__construct($name, $title, $folder, $admin, $shmenu, $important);
    }

    /**
     * aqui se comprueba que todos los valores que se van a utilizar para exportar e importar
     */
    protected function private_core()
    {
        if ($this->cluf_ok()) {
            $this->codalmacen = isset($_REQUEST['codalmacen']) ? $_REQUEST['codalmacen'] : $this->empresa->codalmacen;
            $this->contiene = isset($_REQUEST['contiene']) ? $_REQUEST['contiene'] : 'articulos';
            $this->almacen = new almacen();
            $this->familia = new familia();
            $this->impuesto = new impuesto();
            $this->separador = isset($_REQUEST['separador']) ? $_REQUEST['separador'] : ';';
            $this->importador = new importador_simple($this->empresa, $this->separador, $this->db, $this->codalmacen);
            $this->serie = new serie();

            if (isset($_GET['export'])) {
                $this->export_procedures();
            } else if (isset($_GET['export_fam'])) {
                $this->exportar_articulos_familia($_GET['export_fam']);
            } else if (isset($_POST['contiene'])) {
                $this->import_procedures();
            } else {
                $this->check_menu();
                $this->share_extensions();
            }
        } else {
            $this->template = 'ie_csv_cluf';
        }
    }

    private function export_procedures()
    {
        if ($_GET['export'] == 'clientes') {
            $this->exportar_clientes();
        } else if ($_GET['export'] == 'contactos') {
            $this->exportar_contactos();
        } else if ($_GET['export'] == 'proveedores') {
            $this->exportar_proveedores();
        } else if ($_GET['export'] == 'fabricantes') {
            $this->exportar_fabricantes();
        } else if ($_GET['export'] == 'familias') {
            $this->exportar_familias();
        } else {
            $this->new_error_msg('Opción de exportación desconocida.');
        }
    }

    private function import_procedures()
    {
        if (is_uploaded_file($_FILES['fcsv']['tmp_name'])) {
            if ($_POST['contiene'] == 'clientes') {
                $this->importador->abrir_archivo("clientes");
            } else if ($_POST['contiene'] == 'contactos') {
                if (class_exists('crm_contacto')) {
                    $this->importador->abrir_archivo("contactos");
                } else {
                    $this->new_error_msg('No tienes instalado el plugin CRM.');
                }
            } else if ($_POST['contiene'] == 'proveedores') {
                $this->importador->abrir_archivo("proveedores");
            } else if ($_POST['contiene'] == 'articulos') {
                $this->importador->abrir_archivo("articulos");
            } else if ($_POST['contiene'] == 'familias') {
                $this->importador->abrir_archivo("familias");
            } else if ($_POST['contiene'] == 'fabricantes') {
                $this->importador->abrir_archivo("fabricantes");
            } else {
                $this->new_error_msg('Opción de importación desconocida.');
            }
        } else {
            $this->new_error_msg('No has seleccionado ningún archivo.');
        }
    }

    /**
     * aqui se comprueba si el usuario ha aceptado la licencia
     * @return boolean
     */
    private function cluf_ok()
    {
        $fsvar = new fs_var();

        if (isset($_GET['cluf_ok'])) {
            $fsvar->simple_save('ie_csv_cluf', '1');
            return TRUE;
        } else if ($fsvar->simple_get('ie_csv_cluf')) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * aqui se comprueba en que pagina del plugin estas
     */
    private function check_menu()
    {
        if (!file_exists(__DIR__)) {
            $this->new_error_msg('No se encuentra el directorio ' . __DIR__);
            return;
        }

        /// activamos las páginas del plugin
        foreach (fs_file_manager::scan_files(__DIR__, 'php') as $file_name) {
            if ($file_name == __CLASS__ . '.php') {
                continue;
            }

            $page_name = substr($file_name, 0, -4);
            require_once __DIR__ . '/' . $file_name;
            $new_fsc = new $page_name();

            if (!$new_fsc->page->save()) {
                $this->new_error_msg("Imposible guardar la página " . $page_name);
                break;
            }

            unset($new_fsc);
        }

        $this->load_menu(TRUE);
    }

    /**
     * aqui se exportan los clientes como archivo csv
     */
    private function exportar_clientes()
    {
        $this->template = FALSE;
        $cliente = new cliente();
        $cuenta_banco = new cuenta_banco_cliente();

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"clientes.csv\"");
        echo "codcliente;nombre;razonsocial;cifnif;telefono1;telefono2;codgrupo;codpago;fax;email;web;direccion;codpostal;ciudad;provincia;pais;iban;swift;serie\n";

        $offset = 0;
        $clientes = $cliente->all($offset);
        while ($clientes) {
            foreach ($clientes as $cli) {
                echo $cli->codcliente . ';';
                echo fs_fix_html($cli->nombre) . ';';
                echo fs_fix_html($cli->razonsocial) . ';';
                echo trim($cli->cifnif) . ';';
                echo trim($cli->telefono1) . ';';
                echo trim($cli->telefono2) . ';';
                echo trim($cli->codgrupo) . ';';
                echo trim($cli->codpago) . ';';
                echo trim($cli->fax) . ';';
                echo fs_fix_html($cli->email) . ';';
                echo trim($cli->web) . ';';

                $direccion = FALSE;
                foreach ($cli->get_direcciones() as $dir) {
                    echo fs_fix_html($dir->direccion) . ';';
                    echo trim($dir->codpostal) . ';';
                    echo fs_fix_html($dir->ciudad) . ';';
                    echo fs_fix_html($dir->provincia) . ';';
                    echo trim($dir->codpais) . ';';
                    $direccion = TRUE;
                    break;
                }

                if (!$direccion) {
                    echo ';;;;;';
                }

                $cuenta = FALSE;
                foreach ($cuenta_banco->all_from_cliente($cli->codcliente) as $cb) {
                    echo trim($cb->iban) . ';';
                    echo trim($cb->swift) . ';';
                    $cuenta = TRUE;
                    break;
                }

                if (!$cuenta) {
                    echo ';;';
                }

                echo trim($cli->codserie) . "\n";

                $offset++;
            }

            $clientes = $cliente->all($offset);
        }
    }

    /**
     * aqui se exportan los contactos como archivo csv
     */
    private function exportar_contactos()
    {
        $this->template = FALSE;
        $contacto = new crm_contacto();

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"contactos.csv\"");
        echo "First Name,Middle Name,Last Name,Suffix,Notes,E-mail Address,E-mail 2 Address,E-mail 3 Address,"
        . "Primary Phone,Home Phone,Home Phone 2,Mobile Phone,Home Street,Home City,Home State,Home Postal Code,"
        . "Home Country,Company Main Phone,Business Phone,Business Phone 2,Company,Job Title,Department,"
        . "Business City,Business State,Business Postal Code,Business Country\n";

        $offset = 0;
        $contactos = $contacto->all($offset);
        while ($contactos) {
            foreach ($contactos as $con) {
                echo '"' . fs_fix_html($con->nombre) . '",,,,';
                echo '"' . fs_fix_html(str_replace("\n", ' ', $con->observaciones)) . '",';
                echo '"' . trim($con->email) . '",,,';
                echo trim($con->telefono1) . ',';
                echo trim($con->telefono2) . ',,,';
                echo '"' . fs_fix_html($con->direccion) . '",';
                echo '"' . fs_fix_html($con->ciudad) . '",';
                echo '"' . fs_fix_html($con->provincia) . '",';
                echo trim($con->codpostal) . ',';
                echo $con->codpais . ',,,,';
                echo '"' . fs_fix_html($con->empresa) . '",';
                echo '"' . fs_fix_html($con->cargo) . '",,,,,,';
                echo "\n";

                $offset++;
            }

            $contactos = $contacto->all($offset);
        }
    }

    /**
     * aqui se exportan los proveedores como archivo csv
     */
    private function exportar_proveedores()
    {
        $this->template = FALSE;
        $proveedor = new proveedor();
        $cuenta_banco = new cuenta_banco_proveedor();

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"proveedores.csv\"");
        echo "codproveedor;nombre;razonsocial;cifnif;telefono1;telefono2;fax;email;web;direccion;codpostal;ciudad;provincia;pais;iban;swift;serie\n";

        $offset = 0;
        $proveedores = $proveedor->all($offset);
        while ($proveedores) {
            foreach ($proveedores as $pro) {
                echo $pro->codproveedor . ';';
                echo fs_fix_html($pro->nombre) . ';';
                echo fs_fix_html($pro->razonsocial) . ';';
                echo trim($pro->cifnif) . ';';
                echo trim($pro->telefono1) . ';';
                echo trim($pro->telefono2) . ';';
                echo trim($pro->fax) . ';';
                echo fs_fix_html($pro->email) . ';';
                echo trim($pro->web) . ';';

                $direccion = FALSE;
                foreach ($pro->get_direcciones() as $dir) {
                    echo fs_fix_html($dir->direccion) . ';';
                    echo trim($dir->codpostal) . ';';
                    echo fs_fix_html($dir->ciudad) . ';';
                    echo fs_fix_html($dir->provincia) . ';';
                    echo trim($dir->codpais) . ';';
                    $direccion = TRUE;
                    break;
                }

                if (!$direccion) {
                    echo ';;;;;';
                }

                $cuenta = FALSE;
                foreach ($cuenta_banco->all_from_proveedor($pro->codproveedor) as $cb) {
                    echo trim($cb->iban) . ';';
                    echo trim($cb->swift) . ';';
                    $cuenta = TRUE;
                    break;
                }

                if (!$cuenta) {
                    echo ';;';
                }

                echo trim($pro->codserie) . "\n";

                $offset++;
            }

            $proveedores = $proveedor->all($offset);
        }
    }

    /**
     * aqui se exportan las familias como archivo csv
     */
    private function exportar_familias()
    {
        $this->template = FALSE;
        $familia = new familia();

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"familias.csv\"");
        echo "codfamilia;descripcion;madre\n";
        foreach ($familia->all() as $fam) {
            echo $fam->codfamilia . ';';
            echo fs_fix_html($fam->descripcion) . ';';
            echo $fam->madre . "\n";
        }
    }

    /**
     * aqui se exportan los fabricantes como archivo csv
     */
    private function exportar_fabricantes()
    {
        $this->template = FALSE;
        $fabricante = new fabricante();

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"fabricantes.csv\"");
        echo "codfabricante;nombre\n";
        foreach ($fabricante->all() as $fab) {
            echo $fab->codfabricante . ';';
            echo fs_fix_html($fab->nombre) . "\n";
        }
    }

    /**
     * Aqui se exportan los articulos como archivo csv
     *
     * @param string $fam
     */
    private function exportar_articulos_familia($fam)
    {
        $this->template = FALSE;
        $articulo = new articulo();
        $tarifa = new tarifa();
        $tarifas = $tarifa->all();
        $offset = 0;

        header("content-type:application/csv;charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"articulos_" . $fam . ".csv\"");
        echo "referencia;codfamilia;codfabricante;descripcion;pvp;iva;codbarras;stock;coste";
        foreach ($tarifas as $i => $tar) {
            echo ';tarifa' . ($i + 1);
        }
        echo "\n";

        $sql = "SELECT * FROM articulos";
        if ($fam == '') {
            if (isset($_GET['sin'])) {
                $sql .= " WHERE codfamilia IS NULL";
            }
        } else {
            $sql .= " WHERE codfamilia = " . $articulo->var2str($fam);
        }

        /**
         * libreoffice y excel toman el punto y 3 decimales como millares,
         * así que si el usuario ha elegido 3 decimales, mejor usamos 4.
         */
        $nf0 = FS_NF0_ART;
        if ($nf0 == 3) {
            $nf0 = 4;
        }

        $data = $this->db->select_limit($sql, 100, $offset);
        while ($data) {
            foreach ($data as $d) {
                $art = new articulo($d);

                echo $art->referencia . ';';
                echo $art->codfamilia . ';';
                echo $art->codfabricante . ';';
                echo fs_fix_html(preg_replace('~[\r\n]+~', ' ', $art->descripcion)) . ';';
                echo number_format($art->pvp, $nf0, '.', '') . ';';
                echo $art->get_iva() . ';';
                echo trim($art->codbarras) . ';';
                echo $art->stockfis . ';';
                echo number_format($art->preciocoste(), $nf0, '.', '');
                foreach ($tarifas as $tar) {
                    $articulos = array($art);
                    $tar->set_precios($articulos);
                    echo ';' . number_format($articulos[0]->pvp * (100 - $articulos[0]->dtopor) / 100, $nf0, '.', '');
                }
                echo "\n";

                $offset++;
            }

            $data = $this->db->select_limit($sql, 100, $offset);
        }
    }

    public function iframe_xid()
    {
        $txt = "<div class='hidden'><iframe src='https://www.facturascripts.com/comm3/index.php?page=community_stats"
            . "&add=TRUE&version=" . $this->version() . "&xid=" . $this->empresa->xid . "&plugins=" . join(',', $GLOBALS['plugins']) . "'>"
            . "</iframe></div>";
        return $txt;
    }

    private function share_extensions()
    {
        $extensiones = array(
            array(
                'name' => 'exportar_clientes',
                'page_from' => __CLASS__,
                'page_to' => 'ventas_clientes',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=clientes'
            ),
            array(
                'name' => 'exportar_proveedores',
                'page_from' => __CLASS__,
                'page_to' => 'compras_proveedores',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=proveedores'
            ),
            array(
                'name' => 'exportar_familias',
                'page_from' => __CLASS__,
                'page_to' => 'ventas_familias',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=familias'
            ),
            array(
                'name' => 'exportar_fabricantes',
                'page_from' => __CLASS__,
                'page_to' => 'ventas_fabricantes',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=fabricantes'
            ),
            array(
                'name' => 'exportar_articulos',
                'page_from' => __CLASS__,
                'page_to' => 'ventas_articulos',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=articulos'
            ),
            array(
                'name' => 'importar_contactos',
                'page_from' => __CLASS__,
                'page_to' => 'crm_contactos',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-transfer" title="Importar/exportar"></span>',
                'params' => '&contiene=contactos'
            )
        );

        /// añadimos/actualizamos las extensiones
        foreach ($extensiones as $ext) {
            $fsext = new fs_extension($ext);
            if (!$fsext->save()) {
                $this->new_error_msg('Error al insertar la extensión ' . $ext['name']);
            }
        }

        /// eliminamos las que sobran
        $fsext = new fs_extension();
        foreach ($fsext->all_from(__CLASS__) as $ext) {
            $encontrada = FALSE;
            foreach ($extensiones as $ext2) {
                if ($ext->name == $ext2['name']) {
                    $encontrada = TRUE;
                    break;
                }
            }

            if (!$encontrada) {
                $ext->delete();
            }
        }
    }
}
