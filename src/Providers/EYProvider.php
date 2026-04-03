<?php

namespace LaravelEInvoiceSuite\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class EYProvider extends AbstractEInvoiceProvider
{
    protected $client;
    protected $apiKey;
    protected $accessToken = "";
    protected $refreshToken = "";
    protected $apiUrl;

    public function __construct()
    {
        $config = config('einvoice.providers.ey');
        $this->apiUrl = $config['api_url'] ?? '';
        $this->apiKey = $config['api_key'] ?? '';
        $this->client = new Client();
    }

    public function authenticate()
    {
        try {
            if ($this->accessToken == "") {
                $response = json_decode($this->client->post("{$this->apiUrl}/v2.0/authenticate", [
                    'headers' => [
                        'username'      => $this->userName,
                        'password'      => $this->password,
                        'apiaccesskey'  => $this->apiKey,
                        'Content-Type'  => 'application/x-www-form-urlencoded'
                    ]
                ])->getBody(), true);

                if ($response['status']) {
                    $this->accessToken = $response['accessToken'];
                    $this->refreshToken = $response['refreshToken'];
                }
            }
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        }
    }

    public function generateIrn()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/v2.0/generateIRN", [
                'headers' => [
                    'accessToken' => $this->accessToken,
                    'apiaccesskey' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'req' => [$this->irnBody()]
                ]
            ])->getBody(), true);
            if (isset($response["status"]) && $response["status"] == 0) {
                if (isset($response['errorDetails']) && $response['errorDetails'] != null) {
                    throw new Exception(end($response['errorDetails'])['errorDesc']);
                } else {
                    throw new Exception("IRN generation failed due to some technical issue. Please contact with EY support team.");
                }
            }
            $response['requestData'] = $this->irnBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }


    private function irnBody()
    {
        return [
            'pos' => $this->pos ?? null,
            'taxScheme' => $this->taxScheme,
            'docCat' => $this->docCat,
            'docType' => $this->docType,
            'docNo' => $this->docNo,
            'docDate' => $this->docDate,
            'reverseCharge' => $this->reverseCharge,
            'suppGstin' => $this->suppGstin,
            'supLegalName' => $this->supLegalName,
            'supBuildingNo' => $this->supBuildingNo,
            'supLocation' => $this->supLocation,
            'supPincode' => $this->supPincode,
            'supStateCode' => $this->supStateCode,
            'custGstin' => $this->custGstin ?? null,
            'custOrSupName' => $this->custOrSupName,
            'custOrSupAddr1' => $this->custOrSupAddr1,
            'custOrSupAddr4' => $this->custOrSupAddr4 ?? '',
            'custPincode' => $this->custPincode,
            'custEmail' => $this->custEmail ?? null,
            'custPhone' => $this->custPhone ?? null,
            'billToState' => $this->billToState ?? null,
            'dispatcherTradeName' => $this->dispatcherTradeName ?? null,
            'dispatcherBuildingNo' => $this->dispatcherBuildingNo ?? null,
            'dispatcherLocation' => $this->dispatcherLocation ?? null,
            'dispatcherPincode' => $this->dispatcherPincode ?? null,
            'dispatcherStateCode' => $this->dispatcherStateCode ?? null,
            'shipToLegalName' => $this->shipToLegalName,
            'shipToBuildingNo' => $this->shipToBuildingNo,
            'shipToLocation' => $this->shipToLocation,
            'shipToPincode' => $this->shipToPincode,
            'shipToState' => $this->shipToState ?? null,
            'invAssessableAmt' => $this->invAssessableAmt,
            'invIgstAmt' => $this->invIgstAmt,
            'invCgstAmt' => $this->invCgstAmt,
            'invSgstAmt' => $this->invSgstAmt,
            'invOtherCharges' => $this->invOtherCharges ?? 0,
            'invCessAdvaloremAmt' => $this->invCessAdvaloremAmt ?? 0,
            'invCessSpecificAmt' => $this->invCessSpecificAmt ?? 0,
            'invStateCessAmt' => $this->invStateCessAmt ?? 0,
            'portCode' => $this->portCode ?? null,
            'shippingBillNo' => $this->shippingBillNo ?? null,
            'shippingBillDate' => $this->shippingBillDate ?? null,
            'invPeriodStartDate' => $this->invPeriodStartDate ?? null,
            'invPeriodEndDate' => $this->invPeriodEndDate ?? null,
            'ecomGSTIN' => $this->ecomGSTIN ?? null,
            'tranType' => $this->tranType ?? null,
            'subsupplyType' => $this->subsupplyType,
            'otherSupplyTypeDesc' => $this->otherSupplyTypeDesc ?? null,
            'transporterID' => $this->transporterID ?? null,
            'transporterName' => $this->transporterName ?? null,
            'transportMode' => $this->transportMode ?? null,
            'transportDocNo' => $this->transportDocNo ?? null,
            'transportDocDate' => $this->transportDocDate ?? null,
            'distance' => $this->distance ?? 0,
            'vehicleNo' => $this->vehicleNo ?? null,
            'vehicleType' => $this->vehicleType ?? null,
            'returnPeriod' => $this->returnPeriod ?? null,
            'orgDocType' => $this->orgDocType ?? null,
            'orgCgstin' => $this->orgCgstin ?? null,
            'diffPercent' => $this->diffPercent ?? null,
            'sec7OfIgstFlag' => $this->sec7OfIgstFlag ?? null,
            'claimRefundFlag' => $this->claimRefundFlag ?? null,
            'autoPopToRefundFlag' => $this->autoPopToRefundFlag ?? null,
            'crDrPreGst' => $this->crDrPreGst ?? null,
            'stateApplyingCess' => $this->stateApplyingCess ?? null,
            'tcsFlag' => $this->tcsFlag ?? null,
            'lineItems' => $this->lineItems,
            'invStateCessSpecificAmt' => $this->invStateCessSpecificAmt ?? 0
        ];
    }

    public function generateEwayBill()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/v1.0/generateEWBfromIRN", [
                'headers' => [
                    'accessToken' => $this->accessToken,
                    'apiaccesskey' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $this->ewayBody()
            ])->getBody(), true);
            if (isset($response["status"]) && $response["status"] == 0) {
                if (isset($response['errorDetails']) && $response['errorDetails'] != null) {
                    throw new Exception(end($response['errorDetails'])['errorDesc']);
                } else {
                    throw new Exception("Eway bill generation failed due to some technical issue. Please contact with EY support team.");
                }
            }
            $response['requestData'] = $this->ewayBody();
            return $this->returnResponse(true, $response);
        } catch (RequestException $e) {
            return $this->returnResponse(false, $e->getMessage());
        } catch (Exception $ex) {
            return $this->returnResponse(false, $ex->getMessage());
        }
    }

    public function cancelEwayBill()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/v1.0/cancelEWB", [
                'headers' => [
                    'accessToken' => $this->accessToken,
                    'apiaccesskey' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $this->cancelEwayBody()
            ])->getBody(), true);
            if (isset($response["status"]) && $response["status"] == 0) {
                if (isset($response['errorDetails']) && $response['errorDetails'] != null) {
                    throw new Exception(end($response['errorDetails'])['errorDesc']);
                } else {
                    throw new Exception("Eway bill cancellation failed due to some technical issue. Please contact with EY support team.");
                }
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
            "ewbNo" => $this->ewbNo,
            "suppGstin" => $this->suppGstin,
            "docType" => $this->docType,
            "docNo" => $this->docNo,
            "docDate" => $this->docDate,
            "canRemarks" => $this->canRemarks,
            "canReason" => $this->canReason,
        ];
    }

    private function ewayBody()
    {
        return [
            'Irn'                    => $this->irn,
            'docNo'                  => $this->docNo,
            'docDate'                => $this->docDate,
            'doctype'                => $this->docType,
            'distance'               => $this->distance,
            'vehicleNo'              => $this->vehicleNo,
            'suppGstin'              => $this->suppGstin,
            'vehicleType'            => $this->vehicleType,
            'transportMode'          => $this->transportMode,
            'transportDocNo'         => $this->transportDocNo,
            'transporterName'        => $this->transporterName,
            'transportDocDate'       => $this->transportDocDate,
            'dispatcherTradeName'    => $this->dispatcherTradeName,
            'dispatcherBuildingNo'   => $this->dispatcherBuildingNo,
            'dispatcherBuildingName' => $this->dispatcherBuildingName,
            'dispatcherStateCode'    => $this->dispatcherStateCode,
            'shipToBuildingName'     => $this->shipToBuildingName,
            'dispatcherLocation'     => $this->dispatcherLocation,
            'dispatcherPincode'      => $this->dispatcherPincode,
            'shipToBuildingNo'       => $this->shipToBuildingNo,
            'shipToLocation'         => $this->shipToLocation,
            'shipToPincode'          => $this->shipToPincode,
            'shipToState'            => $this->shipToState
        ];
    }

    public function cancelIrn()
    {
        try {
            $response = json_decode($this->client->post("{$this->apiUrl}/v2.0/cancelIRN", [
                'headers' => [
                    'accessToken' => $this->accessToken,
                    'apiaccesskey' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => $this->cacelIrnBody()
            ])->getBody(), true);
            if (isset($response["status"]) && $response["status"] == 0) {
                if (isset($response['errorDetails']) && $response['errorDetails'] != null) {
                    throw new Exception(end($response['errorDetails'])['errorDesc']);
                } else {
                    throw new Exception("IRN cancellation failed due to some technical issue. Please contact with EY support team.");
                }
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
            "Irn"          => $this->irn,
            'suppGstin'   => $this->suppGstin,
            'docNo'       => $this->docNo,
            'docDate'     => $this->docDate,
            'docType'     => $this->docType,
            "canRemarks"  => $this->canRemarks,
            "canReason"   => $this->canReason
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
