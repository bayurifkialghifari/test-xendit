<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VaController extends Controller
{
    public function index() {
        dd(base64_encode(config('xendit.secret_key') . ':'));
    }

    public function createVirtualAccount() {
        // API KEY
        $apiKey = base64_encode(config('xendit.secret_key') . ':');

        // REQUEST DATA
        $data = [
            'external_id' => 'va-' . Str::random(10),
            'bank_code' => 'BCA', // BCA, BNI, BRI, BJB, BSI, BNC, CIMB, MANDIRI, PERMATA // OR CALL API https://api.xendit.co/available_virtual_account_banks
            'name' => 'John Doe',
            'is_single_use' => true,
            'is_closed' => true, // FIXED AMOUNT
            'expected_amount' => 1000000,
            'expiration_date' => Carbon::now()->addDay(1)->toIso8601String(), // ISO8601 UTC +0
        ];

        try {
            $create = Http::withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
            ])->post('https://api.xendit.co/callback_virtual_accounts', $data);

            if($create->successful()) {
                dd($create->json());
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getVirtualAccount($id) {
        // API KEY
        $apiKey = base64_encode(config('xendit.secret_key') . ':');

        try {
            $create = Http::withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
            ])->get('https://api.xendit.co/callback_virtual_accounts/' . $id);

            if($create->successful()) {
                dd($create->json());
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function virtualAccountPaidWebhookUrl() {
        $xenditXCallbackToken = config('xendit.webhook_verification_key');
        $reqHeaders = getallheaders();
        $xIncomingCallbackTokenHeader = isset($reqHeaders['x-callback-token']) ? $reqHeaders['x-callback-token'] : '';

        if($xIncomingCallbackTokenHeader === $xenditXCallbackToken) {
            // Get request body
            $rawRequestInput = file_get_contents("php://input");
            $arrRequestInput = json_decode($rawRequestInput, true);
            print_r($arrRequestInput);

            $_id = $arrRequestInput['id'];
            $_externalId = $arrRequestInput['external_id'];
            $_userId = $arrRequestInput['user_id'];
            $_status = $arrRequestInput['status'];
            $_paidAmount = $arrRequestInput['paid_amount'];
            $_paidAt = $arrRequestInput['paid_at'];
            $_paymentChannel = $arrRequestInput['payment_channel'];
            $_paymentDestination = $arrRequestInput['payment_destination'];
        } else {
            dd('Invalid Callback Token');
        }
    }
}
