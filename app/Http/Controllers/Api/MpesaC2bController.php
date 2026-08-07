<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MpesaC2bTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MpesaC2bController extends Controller
{
    /**
     * Handle incoming M-Pesa C2B Confirmation/Validation Callback.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        // Log raw incoming payload for debugging
        Log::info('M-Pesa C2B Callback Payload:', $request->all());

        try {
            // Parse & Extract all fields into separate string values
            $transaction = MpesaC2bTransaction::updateOrCreate(
                ['trans_id' => (string) $request->input('TransID')],
                [
                    'transaction_type'    => (string) $request->input('TransactionType'),
                    'trans_time'          => (string) $request->input('TransTime'),
                    'trans_amount'        => (string) $request->input('TransAmount'),
                    'business_short_code' => (string) $request->input('BusinessShortCode'),
                    'bill_ref_number'     => (string) $request->input('BillRefNumber'),
                    'invoice_number'      => (string) $request->input('InvoiceNumber'),
                    'org_account_balance' => (string) $request->input('OrgAccountBalance'),
                    'third_party_trans_id'=> (string) $request->input('ThirdPartyTransID'),
                    'msisdn'              => (string) $request->input('MSISDN'),
                    'first_name'          => (string) $request->input('FirstName'),
                    'middle_name'         => (string) $request->input('MiddleName'),
                    'last_name'           => (string) $request->input('LastName'),
                ]
            );

            // Return Safaricom-compliant structured acknowledgment response
            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
                'ThirdPartyTransID' => $transaction->trans_id,
            ], 200);

        } catch (Throwable $e) {
            Log::error('M-Pesa C2B Callback Error: ' . $e->getMessage());

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Rejected',
            ], 500);
        }
    }
}