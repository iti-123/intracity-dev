$(document).ready(function () {
         
    /* ----------------------- Validations : Start -------------------- */
    /* 9 Cr Validation */
    $('body').on("keypress keyup", ".clsConsignValue, .clsRIASConsignValue", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            maxPreDecimalPlaces: 8,
            maxDecimalPlaces: 2,
            min: 1,
            max: 90000000
        });

        var str = $(this).val();
        if (str == 90000000 && str.lastIndexOf('.') === (str.length - 1)) {
            str = str.replace(/.\s*$/, "");
            $(this).val(str);
        }
    });

    /* 200000 core Validation */
    $('body').on("keypress keyup", ".distance_rate,.valid-number", function () {

        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 2,
            min: 1,
            max: 20000
        });

        var str = $(this).val();
        if (str == 200000 && str.lastIndexOf('.') === (str.length - 1)) {
            str = str.replace(/.\s*$/, "");
            $(this).val(str);
        }
    });

    /* 100 Validation */
    $('body').on("keypress keyup", ".clshunValue", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            maxPreDecimalPlaces: 3,
            maxDecimalPlaces: 2,
            min: 1,
            max: 100
        });

        var str = $(this).val();
        if (str == 100 && str.lastIndexOf('.') === (str.length - 1)) {
            str = str.replace(/.\s*$/, "");
            $(this).val(str);
        }
    });

    /* 9-2 Validation */
    $('body').on("keypress", ".clsEmdAmount", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            maxPreDecimalPlaces: 9,
            maxDecimalPlaces: 2
        });
    });

    /* 8-2 Validation */
    $('body').on("keypress", ".clsRIASFreightAmount,.clsConsignValue", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 8,
            maxDecimalPlaces: 2
        });
    });

    /* 7-2 Validation */
    $('body').on("keypress", ".clsRDSConsignValue", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 7,
            maxDecimalPlaces: 2
        });
    });

    /* 6-2 Validation */
    $('body').on("keypress", ".clsRIASPrice, .ClsRDSPrice, .clsCounterOffer, .clsFTLCounterOffer", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 6,
            maxDecimalPlaces: 2
        });
    });

    /* 5-2 Validation */
    $('body').on("keypress", ".clsRIASODChargesFlat, .clsRIASFreightChargesFlat, .clsRIOSFreightFlat, .clsRIOSODChargesFlat, .clsRPetOriginCharges, .clsRPetDestinationCharges, .clsRPetConsignValue, .clsRDVTransportCharges, .clsRDVCost, .clsGMSRateFlat, .clsFTLRate, .clsFTLSPrice, .clsFTLFreightAmount,.clsFTLTContractQty, .clsTHPrice, .clsTLCounterOffer, .clsTLDriverCost, .clsTHRate, .clsRDSTransportCharges", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 2
        });
    });

    /* 5-3 Validation */
    $('body').on("keypress", ".clsFTLTQuantity, .clsFTLTCurrIndentQty, .clsCOURMaxWeightGms, .clsCOURMaxWeightKgs, .clsAirInitUnitWeight,.clsFTLSQuantity,  .clsTHQuantity", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 3
        });
    });

    /* 4-3 Validation */
    $('body').on("keypress", ".clsAirInitTVolumeCCM, .clsRailKGpCFT, .clsRailVolumepCFT, .clsRailTContractVol, .clsIntraWeightGms, .clsIntraWeightKgs, .clsIntraWeightMts", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 4,
            maxDecimalPlaces: 3
        });
    });

    /* 4-4 Validation */
    $('body').on("keypress", ".clsAirDomKGperCCM, .clsAirIntKGperCCM", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.0001,
            maxPreDecimalPlaces: 4,
            maxDecimalPlaces: 3
        });
    });

    /* 4-2 Validation. */
    $('body').on("keypress", ".clsRIASWeightBracketpKG, .clsRIASFreightChargespKG, .clsRIASODChargespCFT, .clsRIASCancelCharges, .clsRIASOtherCharges, .clsRIASVolume, .clsRIATFreightChargespKG, .clsRIATODChargespCFT, .clsRIOTStorageCharges, .clsRPetODChargesFlat, .clsRPetFreightFlat, .clsRPetTransportCharges, .clsRPetCancelCharges, .clsRPetDocketCharges, .clsRPetFreightpKG, .clsROMODChargespCFT, .clsROMTransportChargespKm, .clsROMCancelCharges, .clsROMOtherCharges, .clsROMDoor2DoorCharges, .clsRDSVolumeCFT, .clsRDSCancellationCharges, .clsRDTAvgCFTpMove, .clsRDVStorageChargespDay, .clsAllOtherCharges, .clsGMSOtherCharges, .clsCOURMaxWeight, .clsGMSCancelCharges, .clsRIOSStorageCharges, .clsFTLCancelCharges, .clsFTLOtherCharges, .clsTHCancelCharges, .clsTHOtherCharges, .clsTLeasePrice, .clsTLOtherCharges, .clsTLOtherCharges, .clsRelocationAvgVolShip, .clsCOURLengthCM, .clsCOURBreadthCM, .clsCOURHeightCM, .clsCOURLengthFt, .clsCOURBreadthFt, .clsCOURHeightFt, .clsCOURLengthInchs, .clsCOURBreadthInchs, .clsCOURHeightInchs, .clsCOURLengthMeter, .clsCOURBreadthMeter, .clsCOURHeightMeter, .clsTLCancelCharges, .clsRIOSCancelCharges, .clsRIOSOtherCharges, .clsRDSOtherCharges, .clsRDSAdditionalCharges", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 2
        });
    });

    /* 4-3 Validation. */
    $('body').on("keypress", ".clsCOURMaxWeightMts", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 4,
            maxDecimalPlaces: 3
        });
    });

    /* 3-2 Validation */
    $('body').on("keypress", ".clsRIASStorageCharges, .clsRIATStorageCharges, .clsRIOTAvgCBMpMove, .clsRIOTCratingChargespCFT, .clsRPetWeight, .clsRDSODChargespCFT, .clsRDSCratingChargespCFT, .clsRDSStorageChargespCFTpDay", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 3,
            maxDecimalPlaces: 2
        });
    });

    /* 3-3 Validation */
    $('body').on("keypress", ".clsROMDistanceKM,.clsFTLQuantity", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 3,
            maxDecimalPlaces: 3
        });
    });

    /* 3-2 & 1-200 Max Validation */
    $('body').on("keypress", ".clsRIATAvgKgPerMove", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            max: 200,
            maxPreDecimalPlaces: 3,
            maxDecimalPlaces: 2
        });
    });

    /* 3-2 & 1-999 Max Validation */
    $('body').on("keypress", ".clsRIOSODChargespCBM, .clsRIOSCratingChargespCFT", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            max: 999,
            maxPreDecimalPlaces: 3,
            maxDecimalPlaces: 2
        });
    });

    /* 2-2 Validation */
    $('body').on("keypress", ".clsRIASTrasitTime, .clsRIASCartonTypenos, .clsRIATPlaceIndentNos, .clsRIOSVolumeCBM, .clsRPetCageWeight, .clsRDSVolumeCBM, .clsCOURConvFactor", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.01,
            maxPreDecimalPlaces: 2,
            maxDecimalPlaces: 2
        });
    });

    /* 1-2 Validation */
    /*$('body').on("keypress", "", function () {
     $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:1, maxDecimalPlaces:2 });
     });*/

    /* 1-10 Validation */
    $('body').on("keypress", ".clsGMSNoOfDays", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 10,
            allowDecSep: false
        });
    });

    /* 1-20 Validation */
    $('body').on("keypress", ".clsGMSNoOfPerson, .clsGMTNoOfPerson", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 20,
            allowDecSep: false
        });
    });

    /* 1-90 Validation */
    $('body').on("keypress", ".clsRIOSTransitDays", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 90,
            allowDecSep: false
        });
    });

    /* 1-99 Validation */
    $('body').on("keypress", ".clsRIASNoOfCartons, .clsRIASTrasitTime, .clsRIASCartonTypenos, .clsRIATPlaceIndentNos, .clsRPetTransitDays, .clsROMCreditPeriodWeeks, .clsRDSTransitDays, .clsRDVTransitDays, .clsGMSCreditPeriodWeeks, .clsFTLTransitDays, .clsFTLCreditPeriodWeeks, .clsTHTransitDays, .clsTHTransitWeeks, .clsCreditPeriodWeeks, .clsCOURTransitWeeks", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 99,
            allowDecSep: false,
            maxDigits: 3
        });
    });

    /* 1-200 Validation */
    $('body').on("keypress", ".clsRIATNoofMoves, .clsRDTNoOfMoves", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 200,
            allowDecSep: false
        });
    });

    /* 1-365 Validation */
    $('body').on("keypress", ".clsGMTNoOfDays, .clsGMSNoOfDaysTerm", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 365,
            allowDecSep: false
        });
    });
  
  $('body').on("keypress", ".NoOfDays", function (e) {
        
        if (this.value.length > 2 ){
        return false;
       }
        if (this.value.length == 0 && e.which == 48 ){
        return false;
       }
        if(this.value > 364){ return false;}



        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 365,
            maxDigits: 3,
            allowDecSep: false
        });
    });

    /* 0-999 Validation */
    $('body').on("keypress", ".clsROMMinKm", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0,
            max: 999,
            allowDecSep: false
        });
    });

    /* 1-1000 Validation */
    $('body').on("keypress", ".clsFTSLoads", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 1000,
            allowDecSep: false
        });
    });

    /* 1-999 & Max: 3 Chars Validation */
    $('body').on("keypress", ".clsTransitDays, .clsRInitMinLeasePeriod, .clsFTLCreditPeriod, .clsCreditPeriod, .clsTLMinLeasePeriod", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            allowDecSep: false,
            maxDigits: 3
        });
    });

    /* 1-999 Validation */
    $('body').on("keypress", ".clsRIASTrasitDays, .clsRIATTransitDays, .clsRIOSNoOfItems, .clsRIOTNoOfMoves, .clsRIOTNoOfItems, .clsROMMaxKm, .clsROMCreditPeriodDays, .clsROMNoOfItems, .clsRDSNoOfItems, .clsRDSNoOfCartons, .clsRDSInventory, .clsRDSHandymanChargespHour, .clsGMSCreditPeriod, .clsCOURTransitDays", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 999,
            allowDecSep: false
        });
    });

    /* 1-9999 Validation */
    $('body').on("keypress", ".clsRDSEscortChargespDay, .clsFTLSLoads", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 9999,
            allowDecSep: false
        });
    });

    /* 1-99999 Validation */
    $('body').on("keypress", ".clsRDSSettlingServicespDay, .clsRDSPropertySearchCharges, .clsRDSBrokerageCharges, .clsGMSRatepService, .clsGMSRatepPerson, .clsGMSRatepDay, .clsGMTRatepService, .clsGMTRatepPerson, .clsGMTRatepDay, .clsAirInitNoOfPackages, .clsPresentKMReading", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 99999,
            allowDecSep: false
        });
    });

    /* 1-999999 Validation */
    $('body').on("keypress", ".clsGMSSubmitQuote, .clsGMAvgRent, .clsGMSRatepRent", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 999999,
            allowDecSep: false
        });
    });

    /* 1-9 Cr Validation */
    $('body').on("keypress", ".clsRIOSConsignValue", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 1,
            max: 99999999,
            allowDecSep: false
        });
    });

    /**
     * 
     */

    /* -------------- Alphabets Validations -------------------- */

    /* 30 length - Aplhabets with space */
    $('body').on("keypress", ".clsReportTo", function () {
        $(this).alpha({allowSpace: true, maxLength: 30});
    });
    /* 20 length - Tittle with space  sriram 30-12-2016 */
    $('body').on("keypress", ".clsTitle", function () {
        //$(this).alpha({allowSpace:true, maxLength:20});
        $(this).attr("maxlength", 20);
        $(this).attr("allowSpace", true);
    });


    /* 50 length - Aplhabets with space */
    $('body').on("keypress", ".clsAlphaSpace, .clsConsignorName, .clsDrivername, .clsPrivateSellers, .clsRecipientName, .clsReportto", function () {
        $(this).alpha({allowSpace: true, maxLength: 50});
    });

    /* -------------- Alphanumeric Validations -------------------- */

    /* 10 length - AplhaNumeric without space */
    $('body').on("keypress", ".basedistance", function () {
        $(this).alphanum({allowSpace: false, maxLength: 5});
    });

    /* 11 length - AplhaNumeric without space */
    $('body').on("keypress", ".clsTRKHVehicleno, .clsVehicleno", function () {
        $(this).alphanum({allowSpace: false, maxLength: 11});
    });

    /* 20 length - AplhaNumeric without space */
    $('body').on("keypress", ".clsTinNumber, .clsServiceTaxno", function () {
        $(this).alphanum({allowSpace: false, maxLength: 20});
    });

    /* 20 length - AplhaNumeric */
    $('body').on("keypress", ".clsCustomerDoc", function () {
        $(this).alphanum({allowSpace: true, maxLength: 20});
    });

    /* 50 length - AplhaNumeric */
    $('body').on("keypress", ".clsVehicleInsuranceNo", function () {
        $(this).alphanum({allowSpace: false, maxLength: 50});
    });

    $('body').on("keypress", ".clsEngineNumber", function () {
        $(this).numeric({allowSpace: false, maxLength: 14, maxDigits: 14});
    });

    $('body').on("keypress", ".clsChassisNumber", function () {
        $(this).alphanum({allowSpace: false, maxLength: 17, maxDigits: 17});
    });

    /* 50 length - AplhaNumeric with space */
    $('body').on("keypress", ".clsBankName, .clsBranchName", function () {
        $(this).alphanum({allowSpace: true, maxLength: 50});
    });

    /* 50 length with / special char - AplhaNumeric */
    $('body').on("keypress", ".clsLRnumber, .clsTransporterBill", function () {
        $(this).alphanum({allowSpace: true, allow: '/', maxLength: 50});
    });

    /* 25 length - AplhaNumeric */
    $('body').on("keypress", ".clsOtherText", function () {
        $(this).alphanum({allowSpace: true, allow: '-', maxLength: 25});
    });

    /* 50 length - AplhaNumeric with space and allows . */
    $('body').on("keypress", ".clsCustDocs", function () {
        $(this).alphanum({allowSpace: true, allow: '.-_', maxLength: 50});
    });


    /* 50 length - AplhaNumeric */
    $('body').on("keypress", ".clsRIASOrdernumber, .clsRIASAwbnumber, .clsRIOSBLnumber, .clsRPetAwbnumber", function () {
        $(this).alphanum({allowSpace: true, allow: '-)(.*%$#@!&', maxLength: 50});
    });

    /* 75 length - AplhaNumeric */
    $('body').on("keypress", ".clsPinCodeAlphabets", function () {
        $(this).alphanum({allowSpace: true, allow: '-.,', maxLength: 75});
    });

    /* 100 length - AplhaNumeric */
    $('body').on("keypress onpaste paste", ".clsConsignAddInfo", function () {
        var maxAttr = $(this).attr('maxlength');
        if (typeof maxAttr !== typeof undefined && maxAttr !== false) {
        } else {
            $(this).attr("maxlength", 100);
        }
        $(this).alphanum({allowSpace: true, allow: '-)(.#@&/,:', maxLength: 100});
    });

    /* 500 length - AplhaNumeric */
    $('body').on("keypress onpaste paste", ".clsBusiDescription, .clsAddress, .clsRIASAdditionalInfo, .clsFTLComments, .clsAdditionalInfo, .clsReportingAddr", function () {
        var maxAttr = $(this).attr('maxlength');
        if (typeof maxAttr !== typeof undefined && maxAttr !== false) {
        } else {
            $(this).attr("maxlength", 500);
        }
        $(this).alphanum({allowSpace: true, allow: '-)(.#@&/,:', maxLength: 500});
    });

    $('body').on("keypress onpaste paste", ".clsTermsConditions", function () {
        var maxAttr = $(this).attr('maxlength');
        if (typeof maxAttr !== typeof undefined && maxAttr !== false) {
        } else {
            $(this).attr("maxlength", 500);
        }
        $(this).alphanum({allowSpace: true, allow: '($,*,{,},[,],!,%,^,|,~,`<,>).', maxLength: 500});
    });

    /* Password Restriction */
    $('body').on("keypress", ".clsPasswordVal", function () {
        $(this).alphanum({allowSpace: false, allow: '!@#$%^&*()_-+,.:;', maxLength: 14});
    });

    /* Email Validation */
    $('body').on("keypress", ".clsEmailAddr, .clsConsignEmail", function () {
        $(this).alphanum({allowSpace: false, allow: '-@._', maxLength: 75});
    });

    $('body').on("keyup",".alphaNumeric", function() {
        $(this).val($(this).val().replace(/[^a-zA-Z 0-9\n\r]+/g, ''));        
    });

    $('body').on("keyup",".validateDate", function() {
        $(this).val($(this).val().replace(/[^0-9\n\r]+/g, ''));        
    });

    /* Mobile number */
    $('body').on("keypress", ".clsMobileno, .clsMobile", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            allowDecSep: false,
            allowLeadingSpaces: false,
            maxDigits: 10
        });
    });

    /* Landline */
    $('body').on("keypress", ".clsLandline", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            allowDecSep: false,
            allowLeadingSpaces: false,
            maxDigits: 15
        });
    });

    /* PinCode */
    $('body').on("keypress", ".clsPinCode", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            allowDecSep: false,
            allowLeadingSpaces: false,
            maxDigits: 6
        });
    });

    /* Service Level Validations */
    $('body').on("keypress", ".clsFTLQuote,.clsFTLFinalQuote,.buyer_post_counter_offer_value,.clsFTLTQuote", function () {
        var serviceType = $(this).attr('data-servicetype');
        if (typeof serviceType !== typeof undefined && serviceType !== false) {
            $(this).numeric({
                allowPlus: false,
                allowMinus: false,
                allowThouSep: false,
                min: 0.01,
                maxPreDecimalPlaces: 8,
                maxDecimalPlaces: 2,
                max: 10000000
            });

            // if(serviceType == 1){
            //     /* FTL */
            //     $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:7, maxDecimalPlaces:2 });
            // }else if(serviceType == 4){
            //     /* Truck Haul */
            //     $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
            // }else if(serviceType == 5){
            //     /* Truck lease */
            //     var leaseType = $(this).attr('data-leasetype');
            //     var leaseVal = leaseType.toLowerCase();
            //     if(leaseVal == 'daily'){
            //         $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:4, maxDecimalPlaces:2 });
            //     }else if(leaseVal == 'weekly'){
            //         $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:5, maxDecimalPlaces:2 });
            //     }else if(leaseVal == 'monthly'){
            //         $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:6, maxDecimalPlaces:2 });
            //     }else{
            //         $(this).numeric({ allowPlus:false, allowMinus: false, allowThouSep: false, min:0.01, maxPreDecimalPlaces:7, maxDecimalPlaces:2 });
            //     }
            // }
        }
    });


    /* 4-3 LTL-4 Package Weight (Volumetric Weight). */
    $('body').on("keypress", ".clsLTL4LengthCM, .clsLTL4BreadthCM, .clsLTL4HeightCM, .clsLTL4LengthFt, .clsLTL4BreadthFt, .clsLTL4HeightFt, .clsLTL4LengthInchs, .clsLTL4BreadthInchs, .clsLTL4HeightInchs, .clsLTL4LengthMeter, .clsLTL4BreadthMeter, .clsLTL4HeightMeter", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 4,
            maxDecimalPlaces: 3
        });
    });

    /* 4-3 LTL-4 (Unit Weight). */
    $('body').on("keypress", ".clsLTL4MaxWeightGms, .clsLTL4MaxWeightKgs, .clsLTL4MaxWeightMts", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 3
        });
    });

    /* 4-3 LTL-4 (Unit Weight). */
    $('body').on("keypress", ".clsLTL4MaxWeightGms, .clsLTL4MaxWeightKgs, .clsLTL4MaxWeightMts", function () {
        $(this).numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            min: 0.001,
            maxPreDecimalPlaces: 5,
            maxDecimalPlaces: 3
        });
    });

    /* ----------------------- New Validations : End ---------------------- */

    /* 1 Digits 2 Decimals */
    $('body').on("keypress", ".onedigittwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 1, maxDecimalPlaces: 2});
    });

    /* 2 Digits 2 Decimals */
    $('body').on("keypress", ".twodigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 2, maxDecimalPlaces: 2});
    });

    /* 2 Digits 3 Decimals */
    $('body').on("keypress", ".twodigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 2, maxDecimalPlaces: 3});
    });

    /* 3 Digits 2 Decimals */
    $('body').on("keypress", ".threedigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 3, maxDecimalPlaces: 2});
    });

    /* 3 Digits 3 Decimals */
    $('body').on("keypress", ".threedigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 3, maxDecimalPlaces: 3});
    });

    /* 4 Digits 2 Decimals */
    $('body').on("keypress", ".fourdigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 4, maxDecimalPlaces: 2});
    });

    /* 4 Digits 3 Decimals */
    $('body').on("keypress", ".fourdigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 4, maxDecimalPlaces: 3});
    });

    /* 4 Digits 4 Decimals */
    $('body').on("keypress", ".fourdigitsfourdecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 4, maxDecimalPlaces: 4});
    });

    /* 5 Digits 3 Decimals */
    $('body').on("keypress", ".fivedigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 5, maxDecimalPlaces: 3});
    });

    /* 5 Digits 2 Decimals */
    $('body').on("keypress", ".fivedigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 5, maxDecimalPlaces: 2});
    });

    /* 6 Digits 2 Decimals */
    $('body').on("keypress", ".sixdigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 6, maxDecimalPlaces: 2});
    });

    /* 6 Digits 3 Decimals */
    $('body').on("keypress", ".sixdigitsthreedecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 6, maxDecimalPlaces: 3});
    });

    /* 7 Digits 2 Decimals */
    $('body').on("keypress", ".sevendigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 7, maxDecimalPlaces: 2});
    });

    /* Max limit 3 */
    $('body').on("keypress", ".maxlimitthree_lmtVal", function (e) {
        $(this).numeric({maxDigits: 3, allowDecSep: false});
    });

    /* Max limit 5 */
    $('body').on("keypress", ".maxlimitfive_lmtVal", function (e) {
        $(this).numeric({maxDigits: 5, allowDecSep: false});
    });

    /* Max limit 8 */
    $('body').on("keypress", ".maxlimiteight_lmtVal", function (e) {
        $(this).numeric({maxDigits: 8, allowDecSep: false, allowPlus: false, allowMinus: false});
    });

    /* Max limit 7 */
    $('body').on("keypress", ".maxlimitseven_lmtVal", function (e) {
        $(this).numeric({maxDigits: 7, allowDecSep: false, allowPlus: false, allowMinus: false});
    });

    /* Max limit 4 */
    $('body').on("keypress", ".maxlimitfour_lmtVal", function (e) {
        $(this).numeric({maxDigits: 4, allowDecSep: false, allowPlus: false, allowMinus: false});
    });

    /* Max limit 4 without commas allowed*/
    $('body').on("keypress", ".emdmaxlimitfour_lmtVal", function (e) {
        $(this).numeric({maxDigits: 4, allowDecSep: false, allowPlus: false, allowMinus: false, allowThouSep: false,});
    });

    /* Max limit 10 */
    $('body').on("keypress", ".maxlimitten_lmtVal", function (e) {
        $(this).numeric({maxDigits: 10, allowDecSep: false, allowPlus: false, allowMinus: false});
    });

    /* Max limit 6 */
    $('body').on("keypress", ".maxlimitsix_lmtVal", function (e) {
        $(this).numeric({maxDigits: 6, allowDecSep: false, allowPlus: false, allowMinus: false, allowThouSep: false});
    });

    /* Only numerberic */
    $('body').on("keypress", ".numericvalidation", function (e) {
        $(this).numeric({allowPlus: false, allowMinus: false, allowDecSep: false});
    });

    /* Alphabets Only */
    $('body').on("keypress", ".alphaonly_strVal", function (e) {
        $(this).alpha();
    });

    /* Alphabets with space */
    $('body').on("keypress", ".alphaspace_strVal", function (e) {
        $(this).alpha({allowSpace: true});
    });

    /* Alpha Numeric with space */
    $('body').on("keypress", ".alphanumeric_withSpace", function (e) {
        $(this).alphanum({allowSpace: true});
    });

    /* Alpha Numeric with out space */
    $('body').on("keypress", ".alphanumeric_strVal", function (e) {
        $(this).alphanum({allowSpace: false});
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".alphanumericonly_strVal", function (e) {
        $(this).alphanum();
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".alphanumericonlywithout_strVal", function (e) {
        $(this).alphanum();
    });

    /* Alpha Numeric only */
    $('body').on("keypress", ".numericvalidation_withoutsinglequote", function (e) {
        $(this).numeric({allowPlus: false, allowMinus: false, allowDecSep: false});
    });

    /* Alpha Numeric with space */
    $('body').on("keypress, keyup", ".alphanumericspace_strVal", function (e) {
        $(this).alphanum({allowSpace: true});
    });

    $('body').on("keypress", ".tendigitstwodecimals_deciVal", function (e) {
        $(this).numeric({maxPreDecimalPlaces: 10, maxDecimalPlaces: 2});
    });


    $('body').on("keypress", ".termMin2d", function (e) {
        $(this).numeric({min: 0.01, maxPreDecimalPlaces: 4, maxDecimalPlaces: 2});
    });

    $('body').on("keypress", ".termMin4d, .clsKGperCCM", function (e) {
        $(this).numeric({min: 0.0001, maxPreDecimalPlaces: 4, maxDecimalPlaces: 4});
    });

    $('body').on("keypress", ".alphaNum", function(e){
        var pattern = "[a-zA-Z0-9\s]+";
        console.log("Pattern:: ",$(this).val().match(pattern));
    });

    /* $('body').on("keypress", ".numericvalidation_autopop", function (e) {
     $(this).alphanum({allowSpace:true});
     });*/


    /******* Dont Remove below function ******/
    /* $('body').on("keypress", ".clsAutoDisable", function (e) {
     var keycode = e.keyCode || e.which;
     if(keycode == 8){
     $(this).removeClass("clsAutoDisable")
     return true;
     }else{
     e.preventDefault();
     }
     }); */

});


$(document).on("keypress", ".numericvalidation_autopop", function (event) {
    var keycode = event.keyCode || event.which;
    if (!(event.shiftKey == false && (keycode == 9 || keycode == 8 || keycode == 37 || keycode == 39 || keycode == 46 || (keycode >= 48 && keycode <= 57)))) {
        event.preventDefault();
    }

});


$(document).on("keypress", ".maxlimitsix_lmtVal", function (event) {
    if ($(this).val().length < 6 || event.keyCode == 46 || event.keyCode == 8) {
        return true;
    } else {
        return false;
    }
});

function commaSeparateNo(x, boolType) {

    x = x.toString();
    var afterPoint = '';
    if (x.indexOf('.') > 0)
        afterPoint = x.substring(x.indexOf('.'), x.length);
    x = Math.floor(x);
    x = x.toString();
    var lastThree = x.substring(x.length - 3);
    var otherNumbers = x.substring(0, x.length - 3);
    if (otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;

    if (!boolType)
        return res;
    else
        return res + '/-';
}

/* Load Lease term based on from & to dates */
function loadLeaseterms(from_date, to_date) {
    var data = {'from_date': from_date, 'to_date': to_date};
    $("#lease_terms, #lease_type, #lkp_trucklease_lease_term_id").empty().append('<option value="">Lease Term*</option>').selectpicker('refresh');
    if (from_date != '' && to_date != '') {
        $.ajax({
            type: "POST",
            url: '/trucklease/ajxleaseterms',
            data: data,
            dataType: 'json',
            success: function (resData) {
                if (resData.success == true) {
                    var htmlText = '';
                    $.each(resData.optHtml, function (k, v) {
                        htmlText += '<option value="' + k + '">' + v + '</option>';
                    });
                    $("#lease_terms, #lease_type, #lkp_trucklease_lease_term_id").append(htmlText);
                    $('#lease_terms, #lease_type, #lkp_trucklease_lease_term_id').selectpicker('refresh');
                    /* Checking element exists or not */
                    if ($('#daysDiffCnt').length) {
                        $("#daysDiffCnt").val(resData.daysDiff);
                    } else {
                        $('#lease_terms, #lease_type').next().append('<input type="hidden" name="daysDiffCnt" id="daysDiffCnt" value="' + resData.daysDiff + '" />');
                    }
                }
            },
            error: function (request, status, error) {
            },
        });
    }
}


// Intracity BUyer post Validation

function validate(data) {
    console.log(data);
     $(":input").css('border-color', '#e0e0e0');
     $('.btn-group.bootstrap-select').css('border-bottom', '1px solid #e0e0e0');
    var column = [];
    // Validate Spot Hour Basis
    if (data.type_basis == 'hours' && data.type == 'spot') {
        if (data.city == '' || typeof(data.city) === 'string') {
            column.push('city');
        }
        if (data.departure == '' || typeof(data.departure) === 'undefined') {
            column.push('departure');
        }
        if (data.hd_slab == '' || typeof(data.hd_slab) === 'undefined') {
            column.push('hd_slab');
        }
        if (data.no_of_vehicles == '' || typeof(data.no_of_vehicles) === 'undefined') {
            column.push('no_of_vehicles');
        }
        if (data.vehicle_reporting_location == '' || typeof(data.vehicle_reporting_location) === 'string') {
            column.push('vehicle_reporting_location');
        }
        if (data.vehicle_reporting_time == '' || typeof(data.vehicle_reporting_time) === 'undefined') {
            column.push('vehicle_reporting_time');
        }

       

    }

    // Validate Spot Distance Basis

    if (data.type_basis == 'distance_basis' && data.type == 'spot') {
        if (data.city == '' || typeof(data.city) === 'string') {
            column.push('d_city');
        }
        if (data.d_vehicle_reporting_time == '' || typeof(data.d_vehicle_reporting_time) === 'undefined') {
            column.push('d_vehicle_reporting_time');
        }
        if (data.d_from_location == '' || typeof(data.d_from_location) === 'string') {
            column.push('d_from_location');
        }
        if (data.d_to_location == '' || typeof(data.d_to_location) === 'string') {
            column.push('d_to_location');
        }
        if (data.d_valid_from == '' || typeof(data.d_valid_from) === 'undefined') {
            column.push('d_valid_from');
        }
        // if (data.d_valid_to == '' || typeof(data.d_valid_to) === 'undefined') {
        //     column.push('d_valid_to');
        // }
        if (data.d_no_of_vehicle == '' || typeof(data.d_no_of_vehicle) === 'undefined') {
            column.push('d_no_of_vehicle');
        }


        // if (data.d_weight == '' || typeof(data.d_weight) === 'undefined') {
        //     column.push('d_weight');
        // }
        if (data.d_material_type == '' || typeof(data.d_material_type) === 'undefined') {
            column.push('d_material_type');
        }
        if (data.d_vehicle_type_any == '' || typeof(data.d_vehicle_type_any) === 'undefined') {
            column.push('d_vehicle_type_any');
        }
        // if (data.price_type == '' || typeof(data.price_type) === 'undefined') {
        //     column.push('price_type');
        // }

         if (data.price_type == '' || typeof(data.price_type) === 'undefined') {
            column.push('price_type');
        } else {
            if(data.price_type.id == 2) {
                if (data.firm_price == '' || typeof(data.firm_price) === 'undefined') {
                    column.push('firm_price');
                }                
            }
        }
    }
    // Validate Term Hours 

    if (data.type_basis == 'term_hours' || typeof(data.type_basis) === 'undefined') {
        if (data.term_city_id == '' || typeof(data.term_city_id) === 'undefined') {
            column.push('term_city_id');
        }
        if (data.hd_slab_term == '' || typeof(data.hd_slab_term) === 'undefined') {
            column.push('hd_slab_term');
        }
        if (data.d_vehicle_type_term == '' || typeof(data.d_vehicle_type_term) === 'undefined') {
            column.push('d_vehicle_type_term');
        }
        if (data.valid_from_term == '' || typeof(data.valid_from_term) === 'undefined') {
            column.push('valid_from_term');
        }
        if (data.valid_to_term == '' || typeof(data.valid_to_term) === 'undefined') {
            column.push('valid_to_term');
        }
        if (data.no_of_vehicles_term == '' || typeof(data.no_of_vehicles_term) === 'undefined') {
            column.push('no_of_vehicles_term');
        }
        if (data.vehicle_reporting_time_term == '' || typeof(data.vehicle_reporting_time_term) === 'undefined') {
            column.push('vehicle_reporting_time_term');
        }
        if (data.vehicle_reporting_location_term == '' || typeof(data.vehicle_reporting_location_term) === 'undefined') {
            column.push('vehicle_reporting_location_term');
        }
    }
    // 
    if (data.type_basis == 'term_distance' || typeof(data.type_basis) === 'undefined') {
        if (data.term_distance_city_id == '' || typeof(data.term_distance_city_id) === 'undefined') {
            column.push('term_distance_city_id');
        }
        if (data.vehicle_reporting_time_dTerm == '' || typeof(data.vehicle_reporting_time_dTerm) === 'undefined') {
            column.push('vehicle_reporting_time_dTerm');
        }
        if (data.from_location == '' || typeof(data.from_location) === 'undefined') {
            column.push('from_location');
        }
        if (data.to_location == '' || typeof(data.to_location) === 'undefined') {
            column.push('to_location');
        }
        if (data.valid_from_dTerm == '' || typeof(data.valid_from_dTerm) === 'undefined') {
            column.push('valid_from_dTerm');
        }
        if (data.valid_to_dTerm == '' || typeof(data.valid_to_dTerm) === 'undefined') {
            column.push('valid_to_dTerm');
        }
        if (data.no_of_vehicle_dTerm == '' || typeof(data.no_of_vehicle_dTerm) === 'undefined') {
            column.push('no_of_vehicle_dTerm');
        }
        // if (data.weight_dTerm == '' || typeof(data.weight_dTerm) === 'undefined') {
        //     column.push('weight_dTerm');
        // }
        if (data.material_type_dTerm == '' || typeof(data.material_type_dTerm) === 'undefined') {
            column.push('material_type_dTerm');
        }
        if (data.vehicle_type_any_dTerm == '' || typeof(data.vehicle_type_any_dTerm) === 'undefined') {
            column.push('vehicle_type_any_dTerm');
        }
    }


    // Indicate validation Error
    column.forEach(function (value) {
         //$('.' + value).css('border-color', 'red');
         //$('.' + value + ' .btn-group.bootstrap-select').css('border-bottom', '1px solid red');
    });
    console.log(column);
    // Lets Check Validation
    if (column.length == 0) {
        return true;
    } else {
        return false;
    }
}

function validateQuote(data) {
    console.log("INFO Data::", data);
    // debugger;
    $(":input").css('border-color', '#e0e0e0');
    var column = [];
    // for spot validation
    if (data.type_basis == 'hours' || data.type_basis == 'distance_basis') {
        if (data.last_date == '' || typeof(data.last_date) === 'undefined') {
            column.push('last_date');
        }
        if (data.last_time == '' || typeof(data.last_time) === 'undefined') {
            column.push('last_time');
        }
        if (data.term_condition == '' || typeof(data.term_condition) === 'undefined') {
            column.push('term_condition');
        }
    }
    // for term validation

    if (data.type_basis == 'term_hours' || data.type_basis == 'term_distance') {
        if (data.term_last_date == '' || typeof(data.term_last_date) === 'undefined') {
            column.push('term_last_date');
        }
        if (data.term_last_time == '' || typeof(data.term_last_time) === 'undefined') {
            column.push('term_last_time');
        }
        if (data.term_condition == '' || typeof(data.term_condition) === 'undefined') {
            column.push('term_condition_');
        }

        if (data.emd_amount == '' || typeof(data.emd_amount) === 'undefined') {
            column.push('emd_amount');
        }

        if (data.emd_mode == '' || typeof(data.emd_mode) === 'undefined') {
            column.push('emd_mode');
        }

        if (data.award_criteria == '' || typeof(data.award_criteria) === 'undefined') {
            column.push('award_criteria');
        }

        if (data.term_condition == '' || typeof(data.term_condition) === 'undefined') {
            column.push('term_condition_');
        }

        if (data.contract_allotment == '' || typeof(data.contract_allotment) === 'undefined') {
            column.push('contract_allotment');
        }

        if (data.payment_term == '' || typeof(data.payment_term) === 'undefined') {
            column.push('payment_term');
        }

        if (data.payment_term != '' || typeof(data.payment_term) !== 'undefined') {
            if(data.payment_term == 2) {
                if (data.no_of_days == '' || typeof(data.no_of_days) === 'undefined') {
                    column.push('no_of_days');
                }                
            }           
        }

        

        if (data.payment_method == '' || typeof(data.payment_method) === 'undefined') {
            column.push('payment_method');
        }

        if (data.no_of_own_truck == '' || typeof(data.no_of_own_truck) === 'undefined') {
            column.push('no_of_own_truck');
        }

        if (data.average_turn_over == '' || typeof(data.average_turn_over) === 'undefined') {
            column.push('average_turn_over');
        }
       
    }

    // Indicate validation Error
    column.forEach(function (value) {
        if (value == 'term_condition' && (data.type_basis == 'hours' || data.type_basis == 'distance_basis')) {
            $('.' + value).css('color', 'red');
            alert("Accept term and condition");

        } else if (value == 'term_condition_' && (data.type_basis == 'term_hours' || data.type_basis == 'term_distance')) {
            $('.' + value).css('color', 'red');
            alert("Accept term and condition");

        } else {
            $('.' + value).css('border-color', 'red');
        }
    });

    console.log("Required Field", column);
    // Lets Check Validation
    if (column.length == 0) {
        $('.term_condition').css('color', '#666');

        return true;
    } else {
        $('.term_condition_').css('color', '#666');
        return false;
    }
}
