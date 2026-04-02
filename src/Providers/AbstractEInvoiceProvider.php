<?php

namespace LaravelEInvoiceSuite\Providers;

abstract class AbstractEInvoiceProvider implements EInvoiceProviderInterface
{
    // Shared parameters
    public $irn = null;
    public $suppGstin = null;
    public $docNo = null;
    public $docDate = null;
    public $docType = 'INV';
    public $transporterID = null;
    public $transporterName = null;
    public $transportMode = null;
    public $transportDocNo = null;
    public $transportDocDate = null;
    public $distance = 0;
    public $vehicleNo = null;
    public $vehicleType = null;
    public $dispatcherTradeName = null;
    public $dispatcherBuildingNo = null;
    public $dispatcherBuildingName = null;
    public $dispatcherLocation = null;
    public $dispatcherPincode = null;
    public $dispatcherStateCode = null;
    public $shipToLegalName = null;
    public $shipToGstin = null;
    public $shipToBuildingNo = null;
    public $shipToBuildingName = null;
    public $shipToLocation = null;
    public $shipToPincode = null;
    public $shipToState = null;
    public $taxScheme = 'GST';
    public $docCat = 'REG';
    public $reverseCharge = 'N';
    public $supLegalName = null;
    public $supBuildingNo = null;
    public $supBuildingName = null;
    public $supLocation = null;
    public $supPincode = null;
    public $supStateCode = null;
    public $custGstin = null;
    public $custOrSupName = null;
    public $custOrSupAddr1 = null;
    public $custOrSupAddr2 = null;
    public $custOrSupAddr4 = null;
    public $custPincode = null;
    public $custPhone = null;
    public $custEmail = null;
    public $billToState = null;
    public $pos = null;
    public $invAssessableAmt = null;
    public $invIgstAmt = null;
    public $invCgstAmt = 0;
    public $invSgstAmt = 0;
    public $totalDiscountAmount = 0;
    public $totalInvoiceAmount = 0;
    public $invOtherCharges = 0;
    public $invCessAdvaloremAmt = 0;
    public $invCessSpecificAmt = 0;
    public $invStateCessAmt = 0;
    public $invStateCessSpecificAmt = 0;
    public $invPeriodStartDate = null;
    public $invPeriodEndDate = null;
    public $ecomGSTIN = null;
    public $tranType = null;
    public $subsupplyType = 'TAX';
    public $otherSupplyTypeDesc = null;
    public $returnPeriod = null;
    public $orgDocType = null;
    public $orgCgstin = null;
    public $diffPercent = null;
    public $sec7OfIgstFlag = null;
    public $claimRefundFlag = null;
    public $autoPopToRefundFlag = null;
    public $crDrPreGst = null;
    public $stateApplyingCess = null;
    public $tcsFlag = null;
    public $lineItems = [];
    public $shippingBillNo = null;
    public $shippingBillDate = null;
    public $portCode = null;
    public $refundClaim = 'N';
    public $foreignCurreny = null;
    public $foreignCountryCode = null;
    public $exportDuty = null;
    public $canRemarks = 'Order canceled due to wrong entry.';
    public $canReason = '3';
    public $ewbNo = null;

    public function setParameters(array $params): void
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
