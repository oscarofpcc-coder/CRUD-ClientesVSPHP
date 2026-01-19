<?php
class Cliente {
  public $id;
  public $cedula;
  public $nombres;
  public $email;
  public $telefono;
  public $direccion;
  public $estado;

  public function __construct($data = []) {
    $this->id = $data["id"] ?? null;
    $this->cedula = $data["cedula"] ?? "";
    $this->nombres = $data["nombres"] ?? "";
    $this->email = $data["email"] ?? "";
    $this->telefono = $data["telefono"] ?? "";
    $this->direccion = $data["direccion"] ?? "";
    $this->estado = isset($data["estado"]) ? (bool)$data["estado"] : true;
  }

  public function toArray() {
    return [
      "cedula" => $this->cedula,
      "nombres" => $this->nombres,
      "email" => $this->email,
      "telefono" => $this->telefono,
      "direccion" => $this->direccion,
      "estado" => $this->estado
    ];
  }
}
?>
