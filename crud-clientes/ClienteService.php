<?php
require_once "config.php";

class ClienteService {

  private function request($method, $url, $payload = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = ["Content-Type: application/json"];

    if ($payload !== null) {
      $json = json_encode($payload);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      $headers[] = "Content-Length: " . strlen($json);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ["code" => $code, "body" => $res];
  }

  public function getAll() {
    $r = $this->request("GET", API_BASE);
    return json_decode($r["body"], true) ?? [];
  }

  public function create($data) {
    return $this->request("POST", API_BASE, $data);
  }

  public function update($id, $data) {
    return $this->request("PUT", API_BASE . "/" . intval($id), $data);
  }

  public function delete($id) {
    return $this->request("DELETE", API_BASE . "/" . intval($id));
  }
}
?>
