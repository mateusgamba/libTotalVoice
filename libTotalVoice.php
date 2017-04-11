<?php

/**
 * libTotalVoice - Comunicação com Total Voice
 *
 * @author  Mateus Luiz Gamba <mateusgamba@gmail.com>
 */
class libTotalVoice {

    /**
     * Token de acesso
     *
     * @var string
     */
    private $accessToken;

    /**
     * Endereço da api
     *
     * @var string
     */
    private $uri = 'https://api.totalvoice.com.br/';

    /**
     * Define o token de acesso
     *
     * @param String $accessToken
     * @return void
     */
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $account = $this->request('conta', 'GET');
        if ($account['status']!=200) {
            print_r(json_encode($account));
        }
    }

    /**
     * Retorna descrição do erro
     *
     * @param $status
     * @return string
     */
    public function getErrors($status)
    {
        $codes = array(
            '40' => 'N&uacute;mero do celular inv&aacute;lido',
            '41' => 'Saldo insuficiente',
            '400' => 'Requisi&ccedil;&atilde;o inv&aacute;lida',
            '401' => 'N&atilde;o autorizado',
            '402' => 'Pagamento necess&aacute;rio',
            '403' => 'Proibido',
            '404' => 'N&atilde;o encontrado',
            '405' => 'Método n&atilde;o permitido',
            '406' => 'N&atilde;o Aceit&aacute;vel',
            '407' => 'Autentica&ccedil;&atilde;o de proxy necess&aacute;ria',
            '408' => 'Tempo de requisi&ccedil;&atilde;o esgotou (Timeout)'
        );
        return $codes[$status];
    }

    /**
     * Retorna somente números
     *
     * @param string $fone
     * @return string
     */
    public function getOnlyNumber($fone)
    {
        return preg_replace("/[^0-9]/", "", $fone);
    }

    /**
     * Retorna se o número do celular é valido
     *
     * @param String $fone
     * @return bool
     */
    public function isCellNumber($fone)
    {
        $fone = $this->getOnlyNumber($fone);
        $regexCell = "/^[0-9]{11}$/";
        if (preg_match($regexCell, $fone)) {
            return true;
        }
        return false;
    }

    /**
     * Faz o request com o servidor
     *
     * @param string $service
     * @param string $method
     * @param array $postdata
     * @return array
     */
    public function request($service, $method, $postdata = null)
    {
        $context = array(
            'http' => array(
                'method' => $method,
                'header' => 'Accept: application/json' . "\r\n" .
                    'Content-Type: application/json' . "\r\n" .
                    'Access-Token: ' . $this->accessToken
            )
        );
        if ($postdata) {
            $context['http']['content'] = json_encode($postdata);
        }
        $contents = @file_get_contents($this->uri.$service, false, stream_context_create($context));
        $responde_http = $this->parseHeaders($http_response_header);
        if ( (int) $responde_http['reponse_code'] != 200 ) {
            $contents = $this->getErrors($responde_http['reponse_code']);
        }
        return array(
            'status' => $responde_http['reponse_code'],
            'response' => $contents
        );
    }

    /**
     * Formata cabeçalho de retorno da requisição
     *
     * @param array $headers
     * @return array
     */
    private function parseHeaders($headers)
    {
        $head = array();
        foreach($headers as $k=>$v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim( $t[1] );
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['reponse_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }

    /**
     * Envia SMS
     *
     * @param String $fone
     * @param String $message
     * @return array
     */
    public function sendSMS($fone, $message = '')
    {
        $nFone =  $this->isCellNumber($fone);
        if (!$nFone) {
            return array(
                'status' => 40,
                'response' => $this->getErrors(40)
            );
        }
        $account = $this->request('conta', 'GET');
        $account = json_decode($account['response']);
        if ($account->dados->saldo<0.09) {
            return array(
                'status' => 41,
                'response' => $this->getErrors(41)
            );
        }
        $data = array(
            'numero_destino' => $this->getOnlyNumber($fone),
            'mensagem' => $message,
            'resposta_usuario' => false
        );
        return $this->request('sms', 'POST', $data);
    }

    /**
     * Retorna Saldo
     *
     * @return float
     */
    public function getSaldo()
    {
        $account = $this->request('conta', 'GET');
        $account = json_decode($account['response']);
        return $account->dados->saldo;
    }
}
