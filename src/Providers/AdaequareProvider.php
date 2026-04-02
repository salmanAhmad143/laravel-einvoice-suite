<?php

namespace LaravelEInvoiceSuite\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class AdaequareProvider extends AbstractEInvoiceProvider
{
    protected $client;
    protected $apiKey;
    protected $accessToken = '';
    protected $apiSecret = '';
    protected $apiUrl;
    protected $apiMode;

    public function __construct()
    {
        $config = config('einvoice.providers.adaequare');
        $this->apiUrl = $config['api_url'] ?? '';
        $this->apiKey = $config['client_id'] ?? '';
        $this->apiSecret = $config['client_secret'] ?? '';
        $this->userName = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->client = new Client();
        $this->apiMode = env('E_INVOICE_API_MODE', 'TEST');
        if ($this->apiMode == 'TEST') {
            $this->apiUrl = $this->apiUrl . '/test';
        }
    }

    public function authenticate()
    {
        try {
            if ($this->accessToken == "") {
                $response = json_decode($this->client->post("{$this->apiUrl}/gsp/authenticate", [
                ])->getBody(), true);
                if (isset($response['access_token']) && !empty($response['access_token'])) {
                    $this->accessToken = $response['access_token'];
                }
            }
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        }
    }


    public function generateIrn()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/enriched/ei/api/invoice", [
                'headers' => [
                    // Add any required headers here, e.g. authorization if needed
                    'requestid' => 'CT-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 5))
                ],
                'json' => $this->clearTaxIrnBody()
            ])->getBody(), true);
            if (isset($response["success"]) && $response["success"] == false) {
                throw new Exception($response["message"]);
            }
            $response['requestData'] = $this->clearTaxIrnBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }

    private function clearTaxIrnBody()
    {
        return [
            'Version' => '1.1',
            'TranDtls' => [
                'TaxSch' => $this->taxScheme,
                'RegRev' => $this->reverseCharge,
                'SupTyp' => $this->supplyType ?? 'B2B',
            ],
            'DocDtls' => [
                'Typ' => $this->docType,
                'No' => $this->docNo,
                'Dt' => $this->docDate
            ],
            'SellerDtls' => [
                'Gstin' => $this->suppGstin,
                'LglNm' => $this->supLegalName,
                'Addr1' => $this->supBuildingNo,
                'Loc' => $this->supLocation,
                'Pin' => $this->supPincode,
                'Stcd' => $this->supStateCode,
            ],
            'BuyerDtls' => [
                'Gstin' => $this->custGstin ?? null,
                'LglNm' => $this->custOrSupName,
                'Addr1' => $this->custOrSupAddr1,
                'Addr2' => $this->custOrSupAddr2 ?? null,
                'Loc' => $this->custOrSupAddr4 ?? null,
                'Pin' => $this->custPincode,
                'Em' => $this->custEmail ?? null,
                'Ph' => $this->custPhone ?? null,
                'Stcd' => $this->billToState,
                'Pos' => $this->billToState
            ],
            'ShipDtls' => [
                'Gstin' => $this->shipToGstin ?? null,
                'LglNm' => $this->shipToLegalName,
                'Addr1' => $this->shipToBuildingNo,
                'Loc' => $this->shipToLocation,
                'Pin' => $this->shipToPincode,
                'Stcd' => $this->shipToState ?? null,
            ],
            'ItemList' => $this->lineItems,
            'ValDtls' => [
                'AssVal' => $this->invAssessableAmt,
                'IgstVal' => $this->invIgstAmt,
                'CgstVal' => $this->invCgstAmt,
                'SgstVal' => $this->invSgstAmt,
                'OthChrg' => $this->invOtherCharges ?? 0,
                'CesVal' => $this->invCessSpecificAmt ?? 0,
                'StCesVal' => $this->invStateCessAmt ?? 0,
                'Discount' => $this->totalDiscountAmount,
                'TotInvVal' => $this->totalInvoiceAmount,
            ],
            'ExpDtls' => [
                'ShipBNo'   => $this->shippingBillNo,
                'ShipBDt'   => $this->shippingBillDate,
                'Port'      => $this->portCode,
                'RefClm'    => $this->refundClaim,
                'ForCur'    => $this->foreignCurreny,
                'CntCode'   => $this->foreignCountryCode,
                'ExpDuty'   => $this->exportDuty
            ]
        ];
    }

    public function generateEwayBill()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/enriched/ei/api/ewaybill", [
                'json' => $this->ewayBody()
            ])->getBody(), true);
            if (isset($response["success"]) && $response["success"] == false) {
                throw new Exception($response["message"]);
            }
            $response['requestData'] = $this->ewayBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }

    private function ewayBody()
    {
        $payload = [
            'Irn'           => $this->irn,
            'Distance'      => $this->distance,
            'TransId'       => $this->transporterID,
            'TransMode'     => $this->transportMode,
            'TransName'     => $this->transporterName,
            'TransDocNo'    => $this->transportDocNo,
            'TransDocDt'    => $this->transportDocDate,
            'VehNo'         => $this->vehicleNo,
            'VehType'       => $this->vehicleType
        ];
        if (($this->invoiceType ?? 'DOMESTIC') == 'EXPORT') {
            $payload['SupplyType']      = "EXP";
            $payload['SubSupplyType']   = "EXPORT";
            $payload['TransactionType'] = 3;
            $payload['ExpShipDtls']     = [
                // Add export shipping details if needed
            ];
        }
        return $payload;
    }

    public function cancelEwayBill()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/enriched/ewb/ewayapi?action=CANEWB", [
                'json' => $this->cancelEwayBody()
            ])->getBody(), true);
            if (isset($response["success"]) && $response["success"] == false) {
                throw new Exception($response["message"]);
            }
            $response['requestData'] = $this->cancelEwayBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }

    private function cancelEwayBody()
    {
        return [
            'ewbNo' => $this->ewbNo,
            'cancelRmrk' => $this->canRemarks,
            'cancelRsnCode' => $this->canReason
        ];
    }

    public function cancelIrn()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/enriched/ei/api/invoice/cancel", [
                'json' => $this->cacelIrnBody()
            ])->getBody(), true);
            if (isset($response["success"]) && $response["success"] == false) {
                throw new Exception($response["message"]);
            }
            $response['requestData'] = $this->cacelIrnBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }

    private function cacelIrnBody()
    {
        return [
            'irn' => $this->irn,
            'cnlrem' => $this->canRemarks,
            'cnlrsn' => $this->canReason
        ];
    }

    protected function returnResponse($status, $data)
    {
        return [
            'status' => $status,
            'data' => $data
        ];
    }
}
