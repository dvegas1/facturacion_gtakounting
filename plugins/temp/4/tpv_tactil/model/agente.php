<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2013-2017  Carlos Garcia Gomez  neorazorx@gmail.com
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */
require_once 'model/core/agente.php';

/**
 * El agente/empleado es el que se asocia a un albarán, factura o caja.
 * Cada usuario puede estar asociado a un agente, y un agente puede
 * estar asociado a varios usuarios o a ninguno.
 *
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class agente extends \FacturaScripts\model\agente
{

    public $pin;
    public $rfid;

    public function __construct($data = FALSE)
    {
        parent::__construct($data);
        if ($data) {
            $this->pin = $data['pin'];
            $this->rfid = $data['rfid'];
        } else {
            $this->pin = NULL;
            $this->rfid = NULL;
        }
    }

    public function save()
    {
        if (parent::save()) {
            $sql = "UPDATE " . $this->table_name . " SET pin = " . $this->var2str($this->pin)
                . ", rfid = " . $this->var2str($this->rfid)
                . "  WHERE codagente = " . $this->var2str($this->codagente) . ";";

            return $this->db->exec($sql);
        }

        return FALSE;
    }

    public function get_by_rfid($rfid)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE rfid = " . $this->var2str($rfid) . ";";

        $data = $this->db->select($sql);
        if ($data) {
            return new \agente($data[0]);
        }

        return FALSE;
    }
}
