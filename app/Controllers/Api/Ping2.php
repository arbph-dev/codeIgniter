<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\CodeIgniter;

class Ping2 extends BaseController
{
    use ResponseTrait;

    public function getIndex(): ResponseInterface
    {
        return $this->respond([
            'status'  => 'ok',
            'time'    => date('c'),
            'version' => CodeIgniter::CI_VERSION
        ]);        
    }
}