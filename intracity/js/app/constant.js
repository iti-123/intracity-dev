var PUSHER_APP_KEY = '276a19811bb8506bfcf0';
var PUSHER_OPTIONS = {
    cluster: 'ap2',
    encrypted: true
};

var _INTRACITY_ = 3;
var _HYPERLOCAL_ = 47;
var _BLUECOLLAR_ = 46;


var STORAGE_PATH = '';
var TERMFILE_PATH = '';
var IMAGE_PATH = '';



if(location.host == 'localhost') { 
    APPLICATION_PATH='http://localhost/intracity-dev/intracity/index.html#';
    STORAGE_PATH = 'http://localhost/intracity-dev/shippingserver/storage/app/';
} else {
    STORAGE_PATH = 'http://115.124.98.243/~logistiks/shippingserver/storage/app/';
    APPLICATION_PATH='http://115.124.98.243/~logistiks/intracity/index.html#';
}

if(location.host == 'localhost') { 
    TERMFILE_PATH = 'http://localhost/intracity-dev/shippingserver/storage/app';
} else {
    TERMFILE_PATH = 'http://115.124.98.243/~logistiks/shippingserver/storage/app';
}

if(location.host == 'localhost') { 
    IMAGE_PATH = 'http://localhost/intracity-dev/shippingserver/public/';
} else {
    IMAGE_PATH = 'http://115.124.98.243/~logistiks/shippingserver/public/';
}

var SERVICE_API_NAME = {
    "service_3":{
        "serviceId":3,
        "serviceName":"INTRACITY",
        "fullName":"INTRACITY",
        "imagePath":"images/intracity.png",
        "parentservice":"International Logistics",
        "servicePrefix":"intracity",
        "data":{}
    },
    "service_47":{
        "serviceId":47,
        "serviceName":"HYPERLOCAL",
        "fullName":"HYPERLOCAL",
        "imagePath":"images/log-icons/Break_Bulk.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"hyperlocal",
        "data":{}
    },
    "service_46":{
        "serviceId":46,
        "serviceName":"BLUECOLLAR",
        "fullName":"BLUECOLLAR",
        "imagePath":"images/log-icons/Break_Bulk.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"bluecollar",
        "data":{}
    },
    "service_22":{
        "serviceId":22,
        "serviceName":"FCL",
        "fullName":"FCL",
        "imagePath":"images/log-icons/FCL.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"fcl",
        "data":{}
    },
    "service_23":{
        "serviceId":23,
        "serviceName":"LCL",
        "fullName":"LCL",
        "imagePath":"images/log-icons/LCL.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"lcl",
        "data":{}
    },
    "service_24":{
        "serviceId":24,
        "serviceName":"Air Freight",
        "fullName":"Air Freight",
        "parentservice":"International Logistics",
        "imagePath":"images/log-icons/Air_Chartering.png",
        "servicePrefix":"airfreight",
        "data":{}
    },
    "service_25":{
        "serviceId":25,
        "serviceName":"Tyre Mounted",
        "fullName":"Tyre Mounted",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"rorotm",
        "data":{}
    },
    "service_26":{
        "serviceId":26,
        "serviceName":"Chain Mounted",
        "fullName":"Chain Mounted",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"rorocm",
        "data":{}
    },
    "service_27":{
        "serviceId":27,
        "serviceName":"MAFI",
        "fullName":"MAFI",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"mafi",
        "data":{}
    },
    "service_28":{
        "serviceId":28,
        "serviceName":"Dry Bulk",
        "fullName":"Dry Bulk",
        "imagePath":"images/log-icons/Dry_Bulk.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"drybulk",
        "data":{}
    },
    "service_29":{
        "serviceId":29,
        "serviceName":"Liquid Bulk",
        "fullName":"Liquid Bulk",
        "imagePath":"images/log-icons/Liquid_Bulk.svg",
        "parentservice":"International Logistics",
        "servicePrefix":"liquidbulk",
        "data":{}
    },
    "service_30":{
        "serviceId":30,
        "serviceName":"Break Bulk",
        "fullName":"Break Bulk",
        "imagePath":"images/log-icons/BreakBulk_International.png",
        "parentservice":"International Logistics",
        "servicePrefix":"breakbulk",
        "data":{}
    },
    "service_32":{
        "serviceId":32,
        "serviceName":"Vessel Chartering",
        "fullName":"Vessel Chartering",
        "imagePath":"images/log-icons/Vessel_Chartering.png",
        "parentservice":"Asset Lease",
        "servicePrefix":"vesselcharter",
        "data":{}
    },
    "service_33":{
        "serviceId":33,
        "serviceName":"Air Chartering",
        "fullName":"Air Chartering",
        "imagePath":"images/log-icons/Air_Chartering.png",
        "parentservice":"Asset Lease",
        "servicePrefix":"aircharter",
        "data":{}
    },
    "service_34":{
        "serviceId":34,
        "serviceName":"Port Services",
        "fullName":"Port Services",
        "imagePath":"images/log-icons/Port_Services.png",
        "parentservice":"Port Marine",
        "servicePrefix":"port",
        "data":{}
    },
    "service_35":{
        "serviceId":35,
        "serviceName":"Marine Services",
        "fullName":"Marine Services",
        "imagePath":"images/log-icons/Marine_Services.png",
        "parentservice":"Port Marine",
        "servicePrefix":"marine",
        "data":{}
    },
    "service_36":{
        "serviceId":36,
        "serviceName":"Coastal FCL",
        "fullName":"Coastal FCL",
        "imagePath":"images/log-icons/FCL.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"cfcl",
        "data":{}
    },
    "service_37":{
        "serviceId":37,
        "serviceName":"Coastal LCL",
        "fullName":"Coastal LCL",
        "imagePath":"images/log-icons/LCL.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"clcl",
        "data":{}
    },
    "service_38":{
        "serviceId":38,
        "serviceName":"Coastal Tyre Mounted",
        "fullName":"Coastal Tyre Mounted",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"crorotm",
        "data":{}
    },
    "service_39":{
        "serviceId":39,
        "serviceName":"Coastal Chain Mounted",
        "fullName":"Coastal Chain Mounted",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"crorocm",
        "data":{}
    },
    "service_40":{
        "serviceId":40,
        "serviceName":"Coastal MAFI",
        "fullName":"Coastal MAFI",
        "imagePath":"images/log-icons/roro.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"cmafi",
        "data":{}
    },
    "service_41":{
        "serviceId":41,
        "serviceName":"Coastal Dry Bulk",
        "fullName":"Coastal Dry Bulk",
        "imagePath":"images/log-icons/Dry_Bulk.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"cdrybulk",
        "data":{}
    },
    "service_42":{
        "serviceId":42,
        "serviceName":"Coastal Liquid Bulk",
        "fullName":"Coastal Liquid Bulk",
        "imagePath":"images/log-icons/Liquid_Bulk.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"cliquidbulk",
        "data":{}
    },
    "service_43":{
        "serviceId":43,
        "serviceName":"Coastal Break Bulk",
        "fullName":"Coastal Break Bulk",
        "imagePath":"images/log-icons/Break_Bulk.svg",
        "parentservice":"Domestic Logistics",
        "servicePrefix":"cbreakbulk",
        "data":{}
    }
};


var TIME_SLOT = {
    '1-2': '1',
    '2-3': '2',
    '3-4': '3',
    '4-5': '4',
    '5-6': '5',
    '6-7': '6',
    '7-8': '7',
    '8-9': '8',
    '9-10': '9',
    '10-11': '10',
    '11-12': '11'
};


var MATERIAL_TYPE = {1: 'CC'};
var ESTIMATED_UNIT = [{"id": 1, "value": "KG"}, {"id": 2, "value": "MT"}];
var PRICE_TYPES = [{"id": 1, "value": "Competitive"}, {"id": 2, "value": "Firm Price"}];



var HYPER_PRICE_TYPES = [{"id":1,"value": "Negotiable"}, {"id":2,"value":"Fixed"}];

var TRANSIT_HOUR = { '1-2': '1-2', '2-3': '2-3', '3-4': '3-4', '4-5': '4-5', '5-6': '5-6', '6-7': '6-7', '7-8': '7-8', '8-9': '8-9', '9-10': '9-10', '10-11': '10-11', '11-12': '11-12' };
var HP_TRANSIT_HOUR = { '0-1': '0-1','1-2': '1-2', '2-3': '2-3', '3-4': '3-4', '4-5': '4-5', '5-6': '5-6', '6-7': '6-7', '7-8': '7-8', '8-9': '8-9', '9-10': '9-10', '10-11': '10-11', '11-12': '11-12' };
var POST_STATUS = [{ "id":0, "value": "Draft" }, { "id": 1, "value": "Open" }, { "id": 2, "value": "Deleted" }, { "id": 3, "value": "Booked" }];
var SERVICE_TYPE = [{"id":1,"value": "Express"}, {"id":2,"value":"Fast"},{"id":3,"value":"Same Day"}];
var HYPERLOCAL_MATERIAL_TYPE = [{ "id":1,"value":"GM(gram)"}, {"id":2,"value": "KG(kilogram)" }];
var POST_TYPE = [{id:1,post_type:'Hour'},{id:2,post_type:'Distance'}];

var STATUS = [{"id":1,"value":'Enable'},{"id":2,"value":'Disable'}];

var ARTICLE = [{id:1,value:'Paid'},{id:2,value:'Free'}];

var lowestRoutes = [{id:0,value:'L1'},{id:1,value:'L2'},{id:2,value:'L3'},{id:3,value:'L4'},{id:4,value:'L5'},{id:6,value:'Manual Selection'}];

var HYPERLOCAL_WEIGHT = [
    {"id": 1, "value": "0.50 kg"},
    {"id": 2, "value": "1.00 kg"},
    {"id": 3, "value": "1.50 kg"},
    {"id": 4, "value": "2.00 kg"},
    {"id": 5, "value": "2.50 kg"},
    {"id": 6, "value": "3.00 kg"},
    {"id": 7, "value": "3.50 kg"},
    {"id": 8, "value": "4.00 kg"},
    {"id": 9, "value": "4.50 kg"},
    {"id": 10, "value": "5.00 kg"},
    {"id": 11, "value": "5.50 kg"},
    {"id": 12, "value": "6.00 kg"},
    {"id": 13, "value": "6.50 kg"},
    {"id": 14, "value": "7.00 kg"},
    {"id": 15, "value": "7.50 kg"},
    {"id": 16, "value": "8.00 kg"},
    {"id": 17, "value": "8.50 kg"},
    {"id": 18, "value": "9.00 kg"},
    {"id": 19, "value": "9.50 kg"},
    {"id": 20, "value": "10.00 kg"}
]


function DataTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("myTable");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
    /*Make a loop that will continue until
    no switching has been done:*/
    while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
            one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /*If a switch has been marked, make the switch
            and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            //Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /*If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

/**
* Handle authentication error 
*/
function handleAuthenticationError(error) {
    var status = error.status;
    localStorage.setItem('redirect_url', window.location.href);
    switch(status) {
        case 401:
            location.replace('login.html');
            break;
        case 400:
            location.replace('login.html');
            break;
        default:
            // console.log("Error Handeler::",error);
    }
}

if(!navigator.onLine) {
    alert("Internet Connectivity Failed");
}