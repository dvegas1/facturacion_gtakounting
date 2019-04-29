<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016-2018, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/ie_csv_home.php';
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Description of importador_facturas
 *
 * @author Carlos García Gómez
 */
class importador_facturas extends ie_csv_home
{

    public $cliente;
    public $ejercicio;
    public $factura_cliente;
    public $factura_proveedor;
    public $importador_simple;
    public $proveedor;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Importador de facturas', 'admin', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->almacen = new almacen();
        $this->cliente = new cliente();
        $this->contiene = isset($_REQUEST['contiene']) ? $_REQUEST['contiene'] : 'compras';
        $this->ejercicio = new ejercicio();
        $this->factura_cliente = new factura_cliente();
        $this->factura_proveedor = new factura_proveedor();
        $this->impuesto = new impuesto();
        $this->proveedor = new proveedor();
        $this->separador = isset($_REQUEST['separador']) ? $_REQUEST['separador'] : ';';
        $this->serie = new serie();

        $this->importador_simple = new importador_simple($this->empresa, $this->separador, $this->db, $this->empresa->codalmacen);

        if (isset($_POST['contiene'])) {
            if (!is_uploaded_file($_FILES['ffacturas']['tmp_name'])) {
                $this->new_error_msg('No has seleccionado el archivo de facturas.');
            } else if (!is_uploaded_file($_FILES['flineas']['tmp_name'])) {
                $this->new_error_msg('No has seleccionado el archivo de lineas.');
            } else if ($_POST['contiene'] == 'compras') {
                $this->importar_compras();
            } else if ($_POST['contiene'] == 'ventas') {
                $this->importar_ventas();
            } else {
                $this->new_error_msg('Opción de importación desconocida.');
            }
        }
    }

    private function importar_compras()
    {
        $csv_facturas = new ParseCsv\Csv();
        $csv_facturas->delimiter = $this->separador;
        $csv_facturas->parse($_FILES['ffacturas']['tmp_name']);

        $csv_lineas = new ParseCsv\Csv();
        $csv_lineas->delimiter = $this->separador;
        $csv_lineas->parse($_FILES['flineas']['tmp_name']);

        if (empty($csv_facturas) || empty($csv_lineas)) {
            return false;
        }

        /// validamos el archivo de facturas
        foreach ($csv_facturas->data as $linea) {
            if (empty($linea) || count($linea) === 1) {
                continue;
            }

            $columnas = "idfactura,idproveedor,nif,nombreproveedor,numproveedor,fecha,vencimiento,neto,totaliva,total,pagada";
            if (!$this->importador_simple->validar_columnas(array_keys($linea), $this->importador_simple->custom_explode(',', $columnas))) {
                $this->new_error_msg('El archivo ' . $_FILES['ffacturas']['name'] . ' no contiene las columnas necesarias.');
                return false;
            }

            break;
        }

        /// validamos el archivo de lineas
        foreach ($csv_lineas->data as $linea) {
            if (empty($linea) || count($linea) === 1) {
                continue;
            }

            $columnas = "idfactura,referencia,descripcion,cantidad,pvp,dto,iva";
            if (!$this->importador_simple->validar_columnas(array_keys($linea), $this->importador_simple->custom_explode(',', $columnas))) {
                $this->new_error_msg('El archivo ' . $_FILES['flineas']['name'] . ' no contiene las columnas necesarias.');
                return false;
            }

            break;
        }

        $total = 0;

        /// leemos las facturas
        foreach ($csv_facturas->data as $linea_f) {
            if (empty($linea_f) || count($linea_f) === 1) {
                continue;
            }

            /// ¿Existe el proveedor?
            if (!$this->existe_proveedor($linea_f)) {
                $this->new_error_msg('Error al procesar el proveedor.');
                continue;
            }

            /// leemos las líneas de la factura
            $lineas = [];
            foreach ($csv_lineas->data as $linea_l) {
                if (empty($linea_l) || count($linea_l) === 1) {
                    continue;
                }

                if ($linea_l['idfactura'] == $linea_f['idfactura']) {
                    $lineas[] = $linea_l;
                } else if (count($lineas) > 0) {
                    break;
                }
            }

            if ($this->existe_factura_compra($linea_f, $lineas)) {
                $total++;
            }
        }

        $this->new_message($total . ' facturas importadas.');
        $this->cache->clean();
    }

    private function importar_ventas()
    {
        $csv_facturas = new ParseCsv\Csv();
        $csv_facturas->delimiter = $this->separador;
        $csv_facturas->parse($_FILES['ffacturas']['tmp_name']);

        $csv_lineas = new ParseCsv\Csv();
        $csv_lineas->delimiter = $this->separador;
        $csv_lineas->parse($_FILES['flineas']['tmp_name']);

        if (empty($csv_facturas) || empty($csv_lineas)) {
            return false;
        }

        /// validamos el archivo de facturas
        foreach ($csv_facturas->data as $linea) {
            if (empty($linea) || count($linea) === 1) {
                continue;
            }

            $columnas = "idfactura,idcliente,nif,nombrecliente,numero2,direccion,codpostal"
                . ",ciudad,provincia,codpais,fecha,vencimiento,neto,totaliva,total,pagada";
            if (!$this->importador_simple->validar_columnas(array_keys($linea), $this->importador_simple->custom_explode(',', $columnas))) {
                $this->new_error_msg('El archivo ' . $_FILES['ffacturas']['name'] . ' no contiene las columnas necesarias.');
                return false;
            }

            break;
        }

        /// validamos el archivo de lineas
        foreach ($csv_lineas->data as $linea) {
            if (empty($linea) || count($linea) === 1) {
                continue;
            }

            $columnas = "idfactura,referencia,descripcion,cantidad,pvp,dto,iva";
            if (!$this->importador_simple->validar_columnas(array_keys($linea), $this->importador_simple->custom_explode(',', $columnas))) {
                $this->new_error_msg('El archivo ' . $_FILES['flineas']['name'] . ' no contiene las columnas necesarias.');
                return false;
            }

            break;
        }

        $total = 0;

        /// leemos las facturas
        foreach ($csv_facturas->data as $linea_f) {
            if (empty($linea_f) || count($linea_f) === 1) {
                continue;
            }

            /// ¿Existe el cliente?
            if (!$this->existe_cliente($linea_f)) {
                $this->new_error_msg('Error al procesar el cliente.');
                continue;
            }

            /// leemos las líneas de la factura
            $lineas = [];
            foreach ($csv_lineas->data as $linea_l) {
                if (empty($linea_l) || count($linea_l) === 1) {
                    continue;
                }

                if ($linea_l['idfactura'] == $linea_f['idfactura']) {
                    $lineas[] = $linea_l;
                } else if (count($lineas) > 0) {
                    break;
                }
            }

            if ($this->existe_factura_venta($linea_f, $lineas)) {
                $total++;
            }
        }

        $this->new_message($total . ' facturas importadas.');
        $this->cache->clean();
    }

    private function existe_proveedor(&$lineaf)
    {
        if ($this->proveedor->get($lineaf['idproveedor'])) {
            return true;
        }

        $proveedor = new proveedor();
        $proveedor->codproveedor = $lineaf['idproveedor'];
        $proveedor->nombre = $proveedor->razonsocial = $lineaf['nombreproveedor'];
        $proveedor->cifnif = $lineaf['nif'];
        return $proveedor->save();
    }

    private function existe_cliente(&$lineaf)
    {
        if ($this->cliente->get($lineaf['idcliente'])) {
            return TRUE;
        }

        $cliente = new cliente();
        $cliente->codcliente = $lineaf['idcliente'];
        $cliente->nombre = $cliente->razonsocial = $lineaf['nombrecliente'];
        $cliente->cifnif = $lineaf['nif'];

        if ($cliente->save()) {
            $dir = new direccion_cliente();
            $dir->codcliente = $cliente->codcliente;
            $dir->direccion = $lineaf['direccion'];
            $dir->codpostal = $lineaf['codpostal'];
            $dir->ciudad = $lineaf['ciudad'];
            $dir->provincia = $lineaf['provincia'];
            $dir->codpais = $lineaf['codpais'];
            $dir->descripcion = 'Principal';
            return $dir->save();
        }

        return false;
    }

    private function existe_factura_compra(&$lineaf, &$lineas)
    {
        $ok = FALSE;

        $factura = $this->factura_proveedor->get_by_codigo($lineaf['idfactura']);
        if ($factura) {
            $factura->fecha = date('d-m-Y', strtotime($lineaf['fecha']));
            $factura->observaciones = 'Vencimeinto: ' . date('d-m-Y', strtotime($lineaf['vencimiento']));
            $factura->codproveedor = $lineaf['idproveedor'];
            $factura->cifnif = $lineaf['nif'];
            $factura->nombre = $lineaf['nombreproveedor'];
            $factura->numproveedor = $lineaf['numproveedor'];

            if (isset($lineaf['codpago'])) {
                $factura->codpago = $lineaf['codpago'];
            }

            if (isset($lineaf['codserie'])) {
                $factura->codserie = $lineaf['codserie'];
            }

            if (intval($lineaf['pagada'])) {
                $factura->pagada = TRUE;
            }

            if ($factura->save()) {
                $ok = TRUE;
            }
        } else {
            $ejercicio = $this->ejercicio->get_by_fecha(date('d-m-Y', strtotime($lineaf['fecha'])));
            if ($ejercicio) {
                $factura = new factura_proveedor();
                $factura->codejercicio = $ejercicio->codejercicio;
                $factura->codserie = $_POST['codserie'];
                $factura->codalmacen = $_POST['codalmacen'];
                $factura->fecha = date('d-m-Y', strtotime($lineaf['fecha']));
                $factura->codigo = $lineaf['idfactura'];
                $factura->observaciones = 'Vencimeinto: ' . date('d-m-Y', strtotime($lineaf['vencimiento']));
                $factura->codproveedor = $lineaf['idproveedor'];
                $factura->cifnif = $lineaf['nif'];
                $factura->nombre = $lineaf['nombreproveedor'];
                $factura->codpago = $this->empresa->codpago;
                $factura->coddivisa = $this->empresa->coddivisa;
                $factura->numproveedor = $lineaf['numproveedor'];

                if (isset($lineaf['codpago'])) {
                    $factura->codpago = $lineaf['codpago'];
                }

                if (isset($lineaf['codserie'])) {
                    $factura->codserie = $lineaf['codserie'];
                }

                if (intval($lineaf['pagada'])) {
                    $factura->pagada = TRUE;
                }

                if ($factura->save()) {
                    $ok = TRUE;

                    foreach ($lineas as $l) {
                        $lf = new linea_factura_proveedor();
                        $lf->idfactura = $factura->idfactura;
                        $lf->referencia = $l['referencia'];
                        $lf->descripcion = $l['descripcion'];
                        $lf->cantidad = floatval_coma($l['cantidad']);
                        $lf->pvpunitario = floatval_coma($l['pvp']);
                        $lf->dtopor = floatval_coma($l['dto']);

                        $lf->iva = floatval_coma($l['iva']);
                        foreach ($this->impuesto->all() as $imp) {
                            if ($imp->iva == $lf->iva) {
                                $lf->codimpuesto = $imp->codimpuesto;
                                break;
                            }
                        }

                        $lf->pvpsindto = ($lf->pvpunitario * $lf->cantidad);
                        $lf->pvptotal = ($lf->pvpunitario * $lf->cantidad * (100 - $lf->dtopor) / 100);

                        if ($lf->save()) {
                            $factura->neto += $lf->pvptotal;
                            $factura->totaliva += $lf->pvptotal * $lf->iva / 100;
                        }
                    }

                    /// redondeamos
                    $factura->neto = round($factura->neto, FS_NF0);
                    $factura->totaliva = round($factura->totaliva, FS_NF0);
                    $factura->total = $factura->neto + $factura->totaliva;

                    $factura->codigo = $lineaf['idfactura'];
                    $factura->save();
                }
            }
        }

        return $ok;
    }

    private function existe_factura_venta(&$lineaf, &$lineas)
    {
        $ok = FALSE;

        $factura = $this->factura_cliente->get_by_codigo($lineaf['idfactura']);
        if ($factura) {
            $factura->fecha = date('d-m-Y', strtotime($lineaf['fecha']));
            $factura->vencimiento = date('d-m-Y', strtotime($lineaf['vencimiento']));
            $factura->codcliente = $lineaf['idcliente'];
            $factura->cifnif = $lineaf['nif'];
            $factura->nombrecliente = $lineaf['nombrecliente'];
            $factura->codpago = $this->empresa->codpago;
            $factura->coddivisa = $this->empresa->coddivisa;
            $factura->numero2 = $lineaf['numero2'];
            $factura->direccion = $lineaf['direccion'];
            $factura->codpostal = $lineaf['codpostal'];
            $factura->ciudad = $lineaf['ciudad'];
            $factura->provincia = $lineaf['provincia'];
            $factura->codpais = $lineaf['codpais'];

            if (intval($lineaf['pagada'])) {
                $factura->pagada = TRUE;
            }

            if ($factura->save()) {
                $ok = TRUE;
            }
        } else {
            $ejercicio = $this->ejercicio->get_by_fecha(date('d-m-Y', strtotime($lineaf['fecha'])));
            if ($ejercicio) {
                $factura = new factura_cliente();
                $factura->codejercicio = $ejercicio->codejercicio;
                $factura->codserie = $_POST['codserie'];
                $factura->codalmacen = $_POST['codalmacen'];
                $factura->fecha = date('d-m-Y', strtotime($lineaf['fecha']));
                $factura->codigo = $lineaf['idfactura'];
                $factura->vencimiento = date('d-m-Y', strtotime($lineaf['vencimiento']));
                $factura->codcliente = $lineaf['idcliente'];
                $factura->cifnif = $lineaf['nif'];
                $factura->nombrecliente = $lineaf['nombrecliente'];
                $factura->codpago = $this->empresa->codpago;
                $factura->coddivisa = $this->empresa->coddivisa;
                $factura->numero2 = $lineaf['numero2'];
                $factura->direccion = $lineaf['direccion'];
                $factura->codpostal = $lineaf['codpostal'];
                $factura->ciudad = $lineaf['ciudad'];
                $factura->provincia = $lineaf['provincia'];
                $factura->codpais = $lineaf['codpais'];

                if (isset($lineaf['codpago'])) {
                    $factura->codpago = $lineaf['codpago'];
                }

                if (isset($lineaf['codserie'])) {
                    $factura->codserie = $lineaf['codserie'];
                }

                if (intval($lineaf['pagada'])) {
                    $factura->pagada = TRUE;
                }

                if ($factura->save()) {
                    $ok = TRUE;

                    foreach ($lineas as $l) {
                        $lf = new linea_factura_cliente();
                        $lf->idfactura = $factura->idfactura;
                        $lf->referencia = $l['referencia'];
                        $lf->descripcion = $l['descripcion'];
                        $lf->cantidad = floatval_coma($l['cantidad']);
                        $lf->pvpunitario = floatval_coma($l['pvp']);
                        $lf->dtopor = floatval_coma($l['dto']);

                        $lf->iva = floatval_coma($l['iva']);
                        foreach ($this->impuesto->all() as $imp) {
                            if ($imp->iva == $lf->iva) {
                                $lf->codimpuesto = $imp->codimpuesto;
                                break;
                            }
                        }

                        $lf->pvpsindto = ($lf->pvpunitario * $lf->cantidad);
                        $lf->pvptotal = ($lf->pvpunitario * $lf->cantidad * (100 - $lf->dtopor) / 100);

                        if ($lf->save()) {
                            $factura->neto += $lf->pvptotal;
                            $factura->totaliva += $lf->pvptotal * $lf->iva / 100;
                        }
                    }

                    /// redondeamos
                    $factura->neto = round($factura->neto, FS_NF0);
                    $factura->totaliva = round($factura->totaliva, FS_NF0);
                    $factura->total = $factura->neto + $factura->totaliva;

                    $factura->codigo = $lineaf['idfactura'];
                    $factura->save();
                }
            }
        }

        return $ok;
    }
}
