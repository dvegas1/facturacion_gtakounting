<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2017-2018, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/ie_csv_home.php';

/**
 * Description of importador_asientos
 *
 * @author Carlos García Gómez
 */
class importador_asientos extends ie_csv_home
{

    private $cuenta;
    private $epigrafe;
    private $grupo_epi;
    private $importador_simple;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Importador de asientos', 'admin', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->contiene = isset($_REQUEST['contiene']) ? $_REQUEST['contiene'] : 'asiento';
        $this->cuenta = FALSE;
        $this->epigrafe = FALSE;
        $this->grupo_epi = FALSE;
        $this->separador = isset($_POST['separador']) ? $_POST['separador'] : ',';
        $this->importador_simple = new importador_simple($this->empresa, $this->separador, $this->db, $this->empresa->codalmacen);

        if (isset($_POST['contiene'])) {
            if (!is_uploaded_file($_FILES['fasiento']['tmp_name'])) {
                $this->new_error_msg('No has seleccionado el archivo del asiento.');
            } else if ($_POST['contiene'] == 'asiento') {
                $this->importar_asiento();
            } else {
                $this->new_error_msg('Opción de importación desconocida.');
            }
        }
    }

    private function importar_asiento()
    {
        $asi0 = new asiento();
        $asiento = FALSE;
        $ej0 = new ejercicio();
        $ejercicio = FALSE;
        $i = 0;
        $plinea = FALSE;
        $total = 0;

        $fcsv = fopen($_FILES['fasiento']['tmp_name'], 'r');
        if ($fcsv) {
            while (!feof($fcsv)) {
                $aux = trim(fgets($fcsv));
                if ($aux != '') {
                    if ($i < 5) {
                        /// nada
                    } else if ($plinea) {
                        $linea = [];
                        foreach ($this->importador_simple->custom_explode($this->separador, $aux) as $j => $value) {
                            if ($j < count($plinea)) {
                                $linea[$plinea[$j]] = $value;
                            }
                        }

                        $fecha = date('d-m-Y', strtotime($linea['Fecha asiento']));

                        /// ¿Existe el ejercicio?
                        if (!$ejercicio) {
                            $ejercicio = $ej0->get_by_fecha($fecha);
                        }

                        /// ¿Existe el asiento?
                        if ($asiento) {
                            /// ¿Existe la subcuenta?
                            $subcuenta = $this->get_subcuenta($linea['Código cuenta'], $ejercicio->codejercicio);
                            if ($subcuenta) {
                                $partida = new partida();
                                $partida->idasiento = $asiento->idasiento;
                                $partida->codsubcuenta = $subcuenta->codsubcuenta;
                                $partida->concepto = $linea['Comentario'];
                                $partida->debe = floatval_coma($linea['ImporteDebe']);
                                $partida->haber = floatval_coma($linea['Importe Haber']);
                                $partida->idsubcuenta = $subcuenta->idsubcuenta;
                                $partida->save();
                            } else {
                                $this->new_error_msg('Imposible obtener la subcuenta ' . $linea['Código cuenta']);
                                break;
                            }
                        } else {
                            $asiento = new asiento();
                            $asiento->codejercicio = $ejercicio->codejercicio;
                            $asiento->fecha = $fecha;
                            $asiento->concepto = $linea['Comentario'];

                            if (!$asiento->save()) {
                                $this->new_error_msg('Error al guardar el asiento.');
                                break;
                            }
                        }

                        $total++;
                    } else {
                        $plinea = $this->importador_simple->custom_explode($this->separador, $aux);

                        /// validamos las columnas
                        $columnas = "Ejercicio;Cód. empresa;Número de asiento;Cargo/Abono;Código cuenta;Contrapartida"
                            . ";Fecha asiento;Comentario;Importe asiento;Cuenta de cargo;Cuenta abono;ImporteDebe"
                            . ";Importe Haber";
                        if (!$this->importador_simple->validar_columnas($plinea, $this->importador_simple->custom_explode(';', $columnas))) {
                            break;
                        }
                    }
                }

                $i++;
            }

            if ($asiento) {
                $asiento->editable = FALSE;
                $asiento->fix();
            }

            $this->new_message($total . ' registros importados.');
            fclose($fcsv);
        }
    }

    private function get_subcuenta($codigo, $codejercicio)
    {
        $codgrupo = substr($codigo, 0, 1);
        $codepi = substr($codigo, 0, 2);
        $codcuenta = substr($codigo, 0, 3);

        /// ¿Existe el grupo?
        if ($this->grupo_epi && $this->grupo_epi->codgrupo != $codgrupo) {
            $this->grupo_epi = FALSE;
        }

        if (!$this->grupo_epi) {
            $ge0 = new grupo_epigrafes();
            $this->grupo_epi = $ge0->get_by_codigo($codgrupo, $codejercicio);
            if (!$this->grupo_epi) {
                $this->grupo_epi = new grupo_epigrafes();
                $this->grupo_epi->codgrupo = $codgrupo;
                $this->grupo_epi->codejercicio = $codejercicio;
                $this->grupo_epi->descripcion = 'Grupo ' . $codgrupo;
                $this->grupo_epi->save();
            }
        }

        /// ¿Existe el epigrafe?
        if ($this->epigrafe && $this->epigrafe->codepigrafe != $codepi) {
            $this->epigrafe = FALSE;
        }

        if (!$this->epigrafe) {
            $epi0 = new epigrafe();
            $this->epigrafe = $epi0->get_by_codigo($codepi, $codejercicio);
            if (!$this->epigrafe) {
                $this->epigrafe = new epigrafe();
                $this->epigrafe->codgrupo = $codgrupo;
                $this->epigrafe->codejercicio = $codejercicio;
                $this->epigrafe->codepigrafe = $codepi;
                $this->epigrafe->idgrupo = $this->grupo_epi->idgrupo;
                $this->epigrafe->descripcion = 'Epígrafe ' . $codepi;
                $this->epigrafe->save();
            }
        }

        /// ¿Existe la cuenta?
        if ($this->cuenta && $this->cuenta->codcuenta != $codcuenta) {
            $this->cuenta = FALSE;
        }

        if (!$this->cuenta) {
            $cu0 = new cuenta();
            $this->cuenta = $cu0->get_by_codigo($codcuenta, $codejercicio);
            if (!$this->cuenta) {
                $this->cuenta = new cuenta();
                $this->cuenta->codcuenta = $codcuenta;
                $this->cuenta->codejercicio = $codejercicio;
                $this->cuenta->codepigrafe = $codepi;
                $this->cuenta->descripcion = 'Cuenta ' . $codcuenta;
                $this->cuenta->idepigrafe = $this->epigrafe->idepigrafe;
                $this->cuenta->save();
            }
        }

        /// ¿Existe la subcuenta?
        $subc0 = new subcuenta();
        $subcuenta = $subc0->get_by_codigo($codigo, $codejercicio);
        if (!$subcuenta) {
            $subcuenta = new subcuenta();
            $subcuenta->codcuenta = $this->cuenta->codcuenta;
            $subcuenta->codejercicio = $codejercicio;
            $subcuenta->codsubcuenta = $codigo;
            $subcuenta->descripcion = 'Subcuenta ' . $codigo;
            $subcuenta->idcuenta = $this->cuenta->idcuenta;
            $subcuenta->save();
        }

        return $subcuenta;
    }
}
