<?php

namespace XTAIN\DeutschePostPortokasse;

class HttpJsonResponse extends HttpResponse
{
    public function getData()
    {
        return \json_decode($this->getContent(), true);
    }
}
