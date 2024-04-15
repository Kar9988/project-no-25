<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $test = $this->paymentService->index($user_id = null, $request->get('page', 1),$request->get('take', 10) );
        return response()->json($test);
    }
    public function getByUser(Request $request, $id)
    {
        $payments =  $this->paymentService->index($id, $request->get('page', 1),$request->get('take', 10) );
        return response()->json($payments);
    }

}
