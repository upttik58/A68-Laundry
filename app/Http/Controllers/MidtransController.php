<?php

namespace App\Http\Controllers;

use App\Models\Orderan;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashedKey = hash('sha512', $request->id . $serverKey);

        if ($hashedKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->id;
        $order = Orderan::where('id', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        switch ($transactionStatus) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $order->update(['status' => 'pending']);
                    } else {
                        $order->update(['status' => 'success']);
                    }
                }
                break;
            case 'settlement':
                $order->update(['status' => 'success']);
                break;
            case 'pending':
                $order->update(['status' => 'pending']);
                break;
            case 'deny':
                $order->update(['status' => 'failed']);
                break;
            case 'expire':
                $order->update(['status' => 'expired']);
                break;
            case 'cancel':
                $order->update(['status' => 'canceled']);
                break;
            default:
                $order->update(['status' => 'unknown']);
                break;
        }

        return response()->json(['message' => 'Callback received successfully']);
    }
}