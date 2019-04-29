<?php
/**
 * @author Carlos García Gómez <neorazorx@gmail.com>
 * @copyright 2015-2019, Carlos García Gómez. All Rights Reserved. 
 */

/**
 * Description of importador_simple
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class importador_simple
{

    private $articulo_model;
    private $cache;
    private $cliente_model;
    private $codalmacen;
    private $contacto_model;
    private $corelog;
    private $db;
    private $empresa;
    private $fabricante_model;
    private $fabricantes;
    private $familia_model;
    private $familias;
    private $grupo_model;
    private $impuestos;
    private $proveedor_model;
    private $separador;
    private $series;

    public function __construct($empresa, $separador, $db, $codalmacen)
    {
        $this->articulo_model = new articulo();
        $this->cache = new fs_cache();
        $this->cliente_model = new cliente();
        $this->codalmacen = $codalmacen;
        if (class_exists('crm_contacto')) {
            $this->contacto_model = new crm_contacto();
        }
        $this->corelog = new fs_core_log();
        $this->db = $db;
        $this->empresa = $empresa;
        $this->fabricante_model = new fabricante();
        $this->fabricantes = $this->fabricante_model->all();
        $this->familia_model = new familia();
        $this->familias = $this->familia_model->all();
        $this->grupo_model = new grupo_clientes();

        $impuesto_model = new impuesto();
        $this->impuestos = $impuesto_model->all();

        $this->proveedor_model = new proveedor();
        $this->separador = $separador;

        $serie_model = new serie();
        $this->series = $serie_model->all();
    }

    /**
     * en esta funcion se lee las lineas del archivo a importar y dependiendo del valor de la variable $tipo,
     * se importa un tipo de archivo u otro. 
     * 
     * @param string $tipo
     */
    public function abrir_archivo($tipo)
    {
        $plinea = FALSE;
        $total = 0;

        $fcsv = fopen($_FILES['fcsv']['tmp_name'], 'r');
        if ($fcsv) {
            while (!feof($fcsv)) {
                $aux = trim(fgets($fcsv));
                if ($aux == '') {
                    continue;
                }

                if ($tipo === "contactos") {
                    $aux = mb_convert_encoding($aux, 'UTF-8', mb_detect_encoding($aux, 'UTF-8, ISO-8859-1', true));
                }

                if ($plinea) {
                    $linea = $this->linea2array($aux, $plinea);
                    $this->process_linea($linea, $plinea, $total, $tipo);
                } else {
                    $plinea = $this->custom_explode($this->separador, $aux);
                    if (!$this->process_plinea($plinea, $tipo)) {
                        break;
                    }
                }
            }

            $this->corelog->new_message($total . ' registros importados.');
            if ($tipo == "fabricantes") {
                $this->cache->clean();
            }

            fclose($fcsv);
        }
    }

    /**
     * Convierte una línea del archivo en un array
     * @param array $data
     * @param array $plinea
     * @return array
     */
    private function linea2array(&$data, &$plinea)
    {
        $linea = [];
        foreach ($this->custom_explode($this->separador, $data) as $i => $value) {
            if (isset($plinea[$i]) && !isset($linea[$plinea[$i]])) {
                $linea[$plinea[$i]] = $value;
            } else {
                $linea[$i] = $value;
            }
        }

        return $linea;
    }

    private function process_linea(&$linea, &$plinea, &$total, $tipo)
    {
        switch ($tipo) {
            case "clientes":
                $this->importar_cliente($linea, $total, $plinea);
                break;

            case "contactos":
                $this->importar_contacto($linea, $total, $plinea);
                break;

            case "proveedores":
                $this->importar_proveedor($linea, $total, $plinea);
                break;

            case "familias":
                $this->importar_familia($linea, $total, $plinea);
                break;

            case "fabricantes":
                $this->importar_fabricante($linea, $total, $plinea);
                break;

            case "articulos":
                $this->importar_articulo($linea, $total, $plinea);
                break;
        }
    }

    private function process_plinea(&$plinea, $tipo)
    {
        switch ($tipo) {
            case "clientes":
                /// validamos las columnas
                $columnas = "nombre;razonsocial;cifnif;telefono1;telefono2;fax;email;web;direccion;codpostal;ciudad;provincia;pais;iban;swift;serie";
                break;

            case "contactos":
                /// validamos las columnas
                $columnas = "First Name;Middle Name;Last Name;Suffix;Notes;E-mail Address;E-mail 2 Address;E-mail 3 Address;"
                    . "Primary Phone;Home Phone;Home Phone 2;Mobile Phone;Home City;Home State;Home Postal Code;"
                    . "Company Main Phone;Business Phone;Business Phone 2;Company;Job Title;Department;"
                    . "Business City;Business State;Business Postal Code";

                /// hacemos este pequeño fix para algunas listas de contactos
                foreach ($plinea as $key => $value) {
                    if ($key == 0 && strlen($value) == 13) {
                        $plinea[0] = 'First Name';
                    }
                }
                break;

            case "proveedores":
                /// validamos las columnas
                $columnas = "nombre;razonsocial;cifnif;telefono1;telefono2;fax;email;web;direccion;codpostal;ciudad;provincia;pais;iban;swift;serie";
                break;

            case "familias":
                /// validamos las columnas
                $columnas = "codfamilia;descripcion;madre";
                break;

            case "fabricantes":
                /// validamos las columnas
                $columnas = "codfabricante;nombre";
                break;

            case "articulos":
                /// validamos las columnas
                $columnas = "referencia;codfamilia;codfabricante;descripcion;pvp;iva;codbarras;stock;coste";
                break;

            default:
                $columnas = "";
        }

        if (!$this->validar_columnas($plinea, $this->custom_explode(';', $columnas))) {
            $this->corelog->new_error('El archivo no contiene las columnas necesarias.');
            return false;
        }

        return true;
    }

    /**
     * esta funcion comprueba si existen clientes en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados.
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_cliente(&$linea, &$total, &$plinea)
    {
        /// ¿Existe el cliente?
        $sql = "SELECT * FROM clientes";
        if (isset($linea['codcliente']) && $linea['codcliente'] != '') {
            $sql .= " WHERE codcliente = " . $this->cliente_model->var2str($linea['codcliente']) . ";";
        } else if ($linea['cifnif'] != '') {
            $sql .= " WHERE cifnif = " . $this->cliente_model->var2str($linea['cifnif']) . ";";
        } else if ($linea['email'] != '') {
            $sql .= " WHERE email = " . $this->cliente_model->var2str($linea['email']) . ";";
        } else {
            $sql .= " WHERE nombre = " . $this->cliente_model->var2str($linea['nombre']) . ";";
        }

        $data = $this->db->select($sql);
        if (count($linea) == count($plinea) && (!$data || isset($_POST['sobreescribir']))) {
            if ($data && isset($_POST['sobreescribir'])) {
                $cliente = new cliente($data[0]);
            } else {
                $cliente = new cliente();

                if (isset($linea['codcliente']) && $linea['codcliente'] != '') {
                    $cliente->codcliente = $linea['codcliente'];
                } else {
                    $cliente->codcliente = $cliente->get_new_codigo();
                }
            }

            $cliente->nombre = trim($linea['nombre']);
            if (strlen($cliente->nombre) > 100) {
                $cliente->nombre = substr($cliente->nombre, 0, 100);
            }

            $cliente->razonsocial = trim($linea['razonsocial']);
            if (strlen($cliente->razonsocial) > 100) {
                $cliente->razonsocial = substr($cliente->razonsocial, 0, 100);
            } else if (strlen($cliente->razonsocial) < 1) {
                $cliente->razonsocial = $cliente->nombre;
            }
            $cliente->nombrecomercial = $cliente->razonsocial;

            $cliente->cifnif = $linea['cifnif'];
            $cliente->telefono1 = $linea['telefono1'];
            $cliente->telefono2 = $linea['telefono2'];
            $cliente->fax = $linea['fax'];
            $cliente->email = $linea['email'];
            $cliente->web = $linea['web'];

            if (isset($linea['codgrupo'])) {
                $grupo = $this->grupo_model->get($linea['codgrupo']);
                if ($grupo) {
                    $cliente->codgrupo = $linea['codgrupo'];
                } else {
                    $grupo = new grupo_clientes();
                    $grupo->codgrupo = $linea['codgrupo'];
                    $grupo->nombre = 'GRUPO ' . $linea['codgrupo'];
                    if ($grupo->save()) {
                        $cliente->codgrupo = $linea['codgrupo'];
                    }
                }
            }

            if (isset($linea['codpago'])) {
                $cliente->codpago = $linea['codpago'];
            }

            $cliente->codserie = $this->empresa->codserie;
            if ($linea['serie'] != '') {
                /// comprobamos que exista la serie
                foreach ($this->series as $se) {
                    if ($se->codserie == $linea['serie']) {
                        $cliente->codserie = $linea['serie'];
                        break;
                    }
                }
            }

            if ($cliente->save()) {
                $total++;

                if ($linea['direccion'] != '') {
                    $direccion = new direccion_cliente();
                    $direccion->codcliente = $cliente->codcliente;
                    $direccion->descripcion = 'General';

                    foreach ($cliente->get_direcciones() as $dir) {
                        /// si el cliente ya tiene direcciones, usamos la 1º
                        $direccion = $dir;
                        break;
                    }

                    $direccion->direccion = $linea['direccion'];
                    $direccion->codpostal = $linea['codpostal'];
                    $direccion->ciudad = $linea['ciudad'];
                    $direccion->provincia = $linea['provincia'];
                    $direccion->codpais = $linea['pais'];
                    $direccion->save();
                }

                if ($linea['iban'] != '' || $linea['swift'] != '') {
                    $cuentab = new cuenta_banco_cliente();
                    $cuentab->codcliente = $cliente->codcliente;

                    foreach ($cuentab->all_from_cliente($cliente->codcliente) as $cb) {
                        /// si el cliente ya tiene cuentas bancarias, usamos la 1º
                        $cuentab = $cb;
                        break;
                    }

                    $cuentab->iban = $linea['iban'];
                    $cuentab->swift = $linea['swift'];
                    $cuentab->save();
                }
            } else {
                $this->corelog->new_error('Error al guardar los datos del cliente.');
            }
            return $total;
        }
    }

    /**
     * esta funcion comprueba si existen contactos en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados.
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_contacto(&$linea, &$total, &$plinea)
    {
        $nombre = $linea['First Name'];
        if ($linea['Middle Name']) {
            $nombre .= ' ' . $linea['Middle Name'];
        }
        if ($linea['Last Name']) {
            $nombre .= ' ' . $linea['Last Name'];
        }

        /// necesitamos algunos datos para determinar si el contacto ya existe
        $extra_sql = '';
        if (empty($nombre) && empty($linea['E-mail Address'])) {
            /// no hay nada que hacer
            return;
        } elseif (empty($linea['E-mail Address'])) {
            $extra_sql .= 'nombre = ' . $this->contacto_model->var2str($nombre);
        } else {
            $extra_sql .= 'email = ' . $this->contacto_model->var2str($linea['E-mail Address']);
        }

        /// ¿Existe el contacto?
        $data = $this->db->select("SELECT * FROM crm_contactos WHERE " . $extra_sql . ";");
        if (!$data || isset($_POST['sobreescribir'])) {
            if ($data && isset($_POST['sobreescribir'])) {
                $contac = $this->contacto_model->get($data[0]['codcontacto']);
            } else {
                $contac = new crm_contacto();
                $contac->email = $linea['E-mail Address'];
            }

            if ($nombre) {
                $contac->nombre = $nombre;
            } else {
                $contac->nombre = $contac->email;
            }

            if ($linea['Company']) {
                $contac->empresa = $linea['Company'];
            }

            if ($linea['Job Title']) {
                $contac->cargo = $linea['Job Title'];
            } else if ($linea['Department']) {
                $contac->cargo = $linea['Department'];
            } else if ($linea['Suffix']) {
                $contac->cargo = $linea['Suffix'];
            }

            $observaciones = '';
            $telefonos = [];
            if ($linea['Primary Phone']) {
                $telefonos[] = $linea['Primary Phone'];
            }
            if ($linea['Mobile Phone']) {
                $telefonos[] = $linea['Mobile Phone'];
            }
            if ($linea['Business Phone']) {
                $telefonos[] = $linea['Business Phone'];
            }
            if ($linea['Business Phone 2']) {
                $telefonos[] = $linea['Business Phone 2'];
            }
            if ($linea['Company Main Phone']) {
                $telefonos[] = $linea['Company Main Phone'];
            }
            if ($linea['Home Phone']) {
                $telefonos[] = $linea['Home Phone'];
            }
            if ($linea['Home Phone 2']) {
                $telefonos[] = $linea['Home Phone 2'];
            }

            if ($telefonos) {
                $contac->telefono1 = $telefonos[0];
                if (isset($telefonos[1])) {
                    $contac->telefono2 = $telefonos[1];
                }
                if (isset($telefonos[2])) {
                    $observaciones .= 'Teléfono alternativo: ' . $telefonos[2] . "\n";
                }
                if (isset($telefonos[3])) {
                    $observaciones .= 'Teléfono alternativo: ' . $telefonos[3] . "\n";
                }
            }

            if ($linea['Business Street']) {
                $contac->direccion = $linea['Business Street'];
            } else if ($linea['Home Street']) {
                $contac->direccion = $linea['Home Street'];
            }

            if ($linea['Business City']) {
                $contac->ciudad = $linea['Business City'];
            } else if ($linea['Home City']) {
                $contac->ciudad = $linea['Home City'];
            }

            if ($linea['Business Postal Code']) {
                $contac->codpostal = $linea['Business Postal Code'];
            } else if ($linea['Home Postal Code']) {
                $contac->codpostal = $linea['Home Postal Code'];
            }

            if ($linea['Business State']) {
                $contac->provincia = $linea['Business State'];
            } else if ($linea['Home State']) {
                $contac->provincia = $linea['Home State'];
            }

            if ($linea['E-mail 2 Address']) {
                $observaciones .= 'Email alternativo: ' . $linea['E-mail 2 Address'] . "\n";
            }
            if ($linea['E-mail 3 Address']) {
                $observaciones .= 'Email alternativo: ' . $linea['E-mail 3 Address'] . "\n";
            }
            if ($linea['Notes']) {
                $observaciones .= "\n" . $linea['Notes'];
            }

            if ($contac->observaciones == '') {
                $contac->observaciones = $observaciones;
            }

            if ($contac->save()) {
                $total++;
            } else {
                $this->corelog->new_error('Error al guardar los datos del contacto.');
            }
        }
        return $total;
    }

    /**
     * esta funcion comprueba si existen proveedores en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados.
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_proveedor(&$linea, &$total, &$plinea)
    {
        /// ¿Existe el proveedor?
        $sql = "SELECT * FROM proveedores";
        if (isset($linea['codproveedor']) && $linea['codproveedor'] != '') {
            $sql .= " WHERE codproveedor = " . $this->proveedor_model->var2str($linea['codproveedor']) . ";";
        } else if ($linea['cifnif'] != '') {
            $sql .= " WHERE cifnif = " . $this->proveedor_model->var2str($linea['cifnif']) . ";";
        } else if ($linea['email'] != '') {
            $sql .= " WHERE email = " . $this->proveedor_model->var2str($linea['email']) . ";";
        } else {
            $sql .= " WHERE nombre = " . $this->proveedor_model->var2str($linea['nombre']) . ";";
        }

        $data = $this->db->select($sql);
        if (count($linea) == count($plinea) && (!$data || isset($_POST['sobreescribir']))) {
            if ($data && isset($_POST['sobreescribir'])) {
                $proveedor = new proveedor($data[0]);
            } else {
                $proveedor = new proveedor();

                if (isset($linea['codproveedor']) && $linea['codproveedor'] != '') {
                    $proveedor->codproveedor = $linea['codproveedor'];
                } else {
                    $proveedor->codproveedor = $proveedor->get_new_codigo();
                }
            }

            $proveedor->nombre = $linea['nombre'];
            $proveedor->nombrecomercial = $proveedor->razonsocial = $linea['razonsocial'];
            $proveedor->cifnif = $linea['cifnif'];
            $proveedor->telefono1 = $linea['telefono1'];
            $proveedor->telefono2 = $linea['telefono2'];
            $proveedor->fax = $linea['fax'];
            $proveedor->email = $linea['email'];
            $proveedor->web = $linea['web'];

            $proveedor->codserie = $this->empresa->codserie;
            if ($linea['serie'] != '') {
                /// comprobamos que exista la serie
                foreach ($this->series as $se) {
                    if ($se->codserie == $linea['serie']) {
                        $proveedor->codserie = $linea['serie'];
                        break;
                    }
                }
            }

            if ($proveedor->save()) {
                $total++;

                if ($linea['direccion'] != '') {
                    $direccion = new direccion_proveedor();
                    $direccion->codproveedor = $proveedor->codproveedor;
                    $direccion->descripcion = 'General';

                    foreach ($proveedor->get_direcciones() as $dir) {
                        /// si el proveedor ya tiene direcciones, usamos la 1º
                        $direccion = $dir;
                        break;
                    }

                    $direccion->direccion = $linea['direccion'];
                    $direccion->codpostal = $linea['codpostal'];
                    $direccion->ciudad = $linea['ciudad'];
                    $direccion->provincia = $linea['provincia'];
                    $direccion->codpais = $linea['pais'];
                    $direccion->save();
                }

                if ($linea['iban'] != '' || $linea['swift'] != '') {
                    $cuentab = new cuenta_banco_proveedor();
                    $cuentab->codproveedor = $proveedor->codproveedor;

                    foreach ($cuentab->all_from_proveedor($proveedor->codproveedor) as $cb) {
                        /// si el proveedor ya tiene cuentas bancarias, usamos la 1º
                        $cuentab = $cb;
                        break;
                    }

                    $cuentab->iban = $linea['iban'];
                    $cuentab->swift = $linea['swift'];
                    $cuentab->save();
                }
            } else {
                $this->corelog->new_error('Error al guardar los datos del proveedor.');
            }
        }

        return $total;
    }

    /**
     * esta funcion comprueba si existen familias en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados. 
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_familia(&$linea, &$total, &$plinea)
    {
        /// ¿Acortamos?
        if (strlen($linea['codfamilia']) > 8) {
            $linea['codfamilia'] = substr($linea['codfamilia'], 0, 8);
        }

        /// ¿Existe la familia?
        $sql = "SELECT * FROM familias  WHERE codfamilia = " . $this->familia_model->var2str($linea['codfamilia']) . ";";
        $data = $this->db->select($sql);
        if (count($linea) == count($plinea) && (!$data || isset($_POST['sobreescribir']))) {
            if ($data && isset($_POST['sobreescribir'])) {
                $familia = new familia($data[0]);
            } else {
                $familia = new familia();
                $familia->codfamilia = $linea['codfamilia'];
            }

            $familia->descripcion = $linea['descripcion'];

            if ($linea['madre'] != '') {
                $familia->madre = $linea['madre'];
            }

            if ($familia->save()) {
                $total++;
            } else {
                $this->corelog->new_error('Error al guardar los datos de la familia.');
            }
        }
        return $total;
    }

    /**
     * esta funcion comprueba si existen fabricantes en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados. 
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_fabricante(&$linea, &$total, &$plinea)
    {
        /// ¿Acortamos?
        if (strlen($linea['codfabricante']) > 8) {
            $linea['codfabricante'] = substr($linea['codfabricante'], 0, 8);
        }

        /// ¿Existe el fabricante?
        $sql = "SELECT * FROM fabricantes WHERE codfabricante = " . $this->fabricante_model->var2str($linea['codfabricante']) . ";";
        $data = $this->db->select($sql);
        if (count($linea) == count($plinea) && (!$data || isset($_POST['sobreescribir']))) {
            if ($data && isset($_POST['sobreescribir'])) {
                $fabricante = new fabricante($data[0]);
            } else {
                $fabricante = new fabricante();
                $fabricante->codfabricante = $linea['codfabricante'];
            }

            $fabricante->nombre = $linea['nombre'];

            if ($fabricante->save()) {
                $total++;
            } else {
                $this->corelog->new_error('Error al guardar los datos del fabricante.');
            }
        }
        return $total;
    }

    /**
     * esta funcion comprueba si existen articulos en la BBDD, y comprueba si selecciona el usuario la opcion de sobreescribir,
     * luego almacena los valores encontrados en el archivo a importar en variables, y se van creando los objetos nuevos con los valores encontrados. 
     * @param array $linea
     * @param int $total
     * @param array $plinea
     * @return int
     */
    private function importar_articulo(&$linea, &$total, &$plinea)
    {
        /// ¿Existe el artículo?
        $sql = "SELECT * FROM articulos";
        if ($linea['referencia'] == '' && $linea['codbarras'] == '') {
            $this->new_error_msg('Se necesita una referencia o un código de barras para identificar al artículo.');
            /// break borrado.
        } else if ($linea['codbarras'] == '') {
            $sql .= " WHERE referencia = " . $this->articulo_model->var2str($linea['referencia']) . ";";
        } else if ($linea['referencia'] == '') {
            $sql .= " WHERE codbarras = " . $this->articulo_model->var2str($linea['codbarras']) . ";";
        } else {
            $sql .= " WHERE referencia = " . $this->articulo_model->var2str($linea['referencia']) . " || codbarras = " . $this->articulo_model->var2str($linea['codbarras']) . ";";
        }

        $data = $this->db->select($sql);
        if (count($linea) == count($plinea) && (!$data || isset($_POST['sobreescribir']))) {
            if ($data && isset($_POST['sobreescribir'])) {
                $articulo = new articulo($data[0]);
            } else {
                $articulo = new articulo();
                $articulo->referencia = $linea['referencia'];
            }

            $articulo->codbarras = $linea['codbarras'];
            $articulo->descripcion = $linea['descripcion'];
            $articulo->set_pvp($linea['pvp']);
            $articulo->preciocoste = $articulo->costemedio = floatval($linea['coste']);

            foreach ($this->impuestos as $imp) {
                if ($imp->iva == floatval($linea['iva'])) {
                    $articulo->codimpuesto = $imp->codimpuesto;
                    break;
                }
            }

            $articulo->codfamilia = NULL;
            if ($linea['codfamilia'] != '') {
                $encontrada = FALSE;
                foreach ($this->familias as $fam) {
                    if ($fam->codfamilia == $linea['codfamilia']) {
                        $encontrada = TRUE;
                        $articulo->codfamilia = $linea['codfamilia'];
                        break;
                    }
                }

                if (!$encontrada) {
                    $familia = new familia();
                    $familia->codfamilia = $familia->descripcion = $linea['codfamilia'];
                    if ($familia->save()) {
                        $this->corelog->new_message('Creada la familia ' . $linea['codfamilia']);
                        $articulo->codfamilia = $linea['codfamilia'];
                        $this->familias[] = $familia;
                    }
                }
            }

            $articulo->codfabricante = NULL;
            if ($linea['codfabricante'] != '') {
                $encontrado = FALSE;
                foreach ($this->fabricantes as $fab) {
                    if ($fab->codfabricante == $linea['codfabricante']) {
                        $encontrado = TRUE;
                        $articulo->codfabricante = $linea['codfabricante'];
                        break;
                    }
                }

                if (!$encontrado) {
                    $fabricante = new fabricante();
                    $fabricante->codfabricante = $fabricante->nombre = $linea['codfabricante'];
                    if ($fabricante->save()) {
                        $this->corelog->new_message('Creado el fabricante ' . $linea['codfabricante']);
                        $articulo->codfabricante = $linea['codfabricante'];
                        $this->fabricantes[] = $fabricante;
                    }
                }
            }

            if ($articulo->save()) {
                $articulo->set_stock($this->codalmacen, $linea['stock']);
                $total++;
            } else {
                $this->corelog->new_error('Error al guardar los datos del artículo.');
            }
        }
        return $total;
    }

    /**
     * Devuelve un array con los resultados después de partir una cadena usando
     * el separador $separador.
     * Tiene en cuenta los casos en que la subcadena empieza por comillas y
     * contiene el separador dentro, como cuando exportas de excel o libreoffice:
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

        if (mb_detect_encoding($texto, 'UTF-8', TRUE) === FALSE) {
            /// si no es utf8, convertimos
            $texto = utf8_encode($texto);
        }

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

        return $seplist;
    }

    /**
     * 
     * @param array $cols
     * @param array $valids
     * @return boolean
     */
    public function validar_columnas($cols, $valids)
    {
        $result = TRUE;

        if (is_array($cols) && is_array($valids)) {
            $faltan = [];
            foreach ($valids as $val) {
                if (!in_array($val, $cols)) {
                    $faltan[] = $val;
                    $result = FALSE;
                }
            }

            if (count($faltan) == 1) {
                $corelog = new fs_core_log();
                $corelog->new_error('Falta la columna ' . $faltan[0]);
            } else if (count($faltan) > 1) {
                $corelog = new fs_core_log();
                $corelog->new_error('Faltan las columnas: ' . join(', ', $faltan) . '.');

                if (count($faltan) == count($valids)) {
                    $corelog = new fs_core_log();
                    $corelog->new_error('¿Separador incorrecto?');
                }
            }
        } else {
            $result = FALSE;
        }

        return $result;
    }
}
