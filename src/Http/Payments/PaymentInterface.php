<?php

namespace Piclou\Piclommerce\Http\Payments;

use Illuminate\Http\Request;

interface PaymentInterface
{
    public function process(float $total);

    public function auto(Request $request);

    public function accept();

    public function refuse();
}