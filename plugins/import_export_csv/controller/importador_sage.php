<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2017, Carlos García Gómez. All Rights Reserved. 
 */
require_once __DIR__ . '/ie_csv_home.php';

/**
 * Description of importador_asientos
 *
 * @author Carlos García Gómez
 */
class importador_sage extends ie_csv_home
{

    public $url_recarga;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Importador SAGE', 'admin', FALSE, FALSE);
    }

    protected function private_core()
    {
        $this->url_recarga = FALSE;

        if (isset($_GET['opcion']) && $_GET['opcion'] == 'clientes') {
            $this->importar_clientes();
        }
    }

    private function importar_clientes()
    {
        $cli0 = new cliente();
        $existentes = 0;
        $nuevos = 0;
        $pro0 = new proveedor();
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

        $sql = "select p.idNo,p.idType,p.fiscalName,p.commercialName,p.creationDate,p.lastUpdate,c.__discriminator
         FROM BusinessPartner p, BPCommon c WHERE p.pk = c.bpartner ORDER BY idNo ASC";

        $data = $this->db->select_limit($sql, 100, $offset);
        if ($data) {
            foreach ($data as $d) {
                $cifnif = str_replace(' ', '', $d['idNo']);
                $cifnif = str_replace('-', '', $cifnif);
                $cifnif = str_replace('.', '', $cifnif);

                $razon = $d['fiscalName'];

                if ($d['__discriminator'] == 'com.ignos.man.commercial.sales.Customer') {
                    $cliente = $cli0->get_by_cifnif($cifnif, $razon);
                    if ($cliente) {
                        /// el cliente existe
                        $existentes++;
                    } else {
                        $cliente = new cliente();
                        $cliente->cifnif = $cifnif;
                        $cliente->tipoidfiscal = $d['idType'];
                        $cliente->nombre = $d['commercialName'];
                        $cliente->razonsocial = $razon;

                        if ($d['creationDate']) {
                            $cliente->fechaalta = date('d-m-Y', strtotime($d['creationDate']));
                        } else if ($d['lastUpdate']) {
                            $cliente->fechaalta = date('d-m-Y', strtotime($d['lastUpdate']));
                        }

                        if ($cliente->save()) {
                            $nuevos++;
                        }
                    }
                } else if ($d['__discriminator'] == 'com.ignos.man.commercial.purchasing.Vendor') {
                    $proveedor = $pro0->get_by_cifnif($cifnif, $razon);
                    if ($proveedor) {
                        /// el cliente existe
                        $existentes++;
                    } else {
                        $proveedor = new proveedor();
                        $proveedor->cifnif = $cifnif;
                        $proveedor->tipoidfiscal = $d['idType'];
                        $proveedor->nombre = $d['commercialName'];
                        $proveedor->razonsocial = $razon;

                        if ($proveedor->save()) {
                            $nuevos++;
                        }
                    }
                }

                $offset++;
            }

            $this->new_message($nuevos . ' nuevos registros. ' . $existentes . ' ya existían.');
            $this->url_recarga = $this->url() . '&opcion=clientes&offset=' . $offset;
        }
    }
}
