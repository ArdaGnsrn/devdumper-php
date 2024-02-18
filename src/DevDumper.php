<?php

namespace ArdaGnsrn\DevDumper;

use ArdaGnsrn\DevDumper\Payloads\ColorPayload;
use ArdaGnsrn\DevDumper\Payloads\ContentPayload;
use ArdaGnsrn\DevDumper\Payloads\TypePayload;

class DevDumper
{
    public function dump(...$variables): DevDumper
    {
        if (empty($variables)) return $this;

        foreach ($variables as $variable) {
            $payloadFactory = (new PayloadFactory())
                ->setPayload(new ContentPayload($variable))
                ->setPayload(new ColorPayload('green'));

            (new Client())->sendPayload($payloadFactory);
        }

        return $this;
    }
}