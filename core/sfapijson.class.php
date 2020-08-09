<?php

class SFApiJson extends SFApiBase
{

    public function execute()
    {
        header('Content-Type: application/json');
        $method = 'action' . ucfirst(SFCore::uri(0) ?: 'index');
        if ($method && method_exists($this, $method)) {
            $responseRaw = $this->$method($_GET);
            $response = SFApiResponse::fromMixed($responseRaw);
            echo json_encode($response->toArray());
            exit;
        }
        header("HTTP/1.0 404 Not Found");
        $response = (new SFApiResponse)->setError(802, 'Method not found');
        echo json_encode($response->toArray());
    }

}