<?

namespace App\Controller;

use App\Http\HttpRequest;
use stdClass;

class CEP
{
  public $router, $return;
  public function __construct($router)
  {
    $this->router = $router;
    $this->return = new stdClass();
  }

  public function Search($post)
  {
    extract($post);

    try {
      $http = new HttpRequest('https://viacep.com.br/ws/');
      $response = $http->get("$cep/json/");

      $this->return->status = $response['status'];
      $this->return->error = $response['status'] !== 200 || isset($response['body']['erro']);
      $this->return->message = $this->return->error ? 'Erro ao consultar CEP' : 'success';
      $this->return->data = $this->return->error ? '' : $response['body'];

      print json_encode($this->return);
    } catch (\Exception $e) {
      echo "Erro: " . $e->getMessage();
    }
  }
}
