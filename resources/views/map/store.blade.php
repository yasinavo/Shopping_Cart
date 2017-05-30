
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <base href="https://www.sanwebe.com/assets/" target="_self">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="public/style.css" rel="stylesheet" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyCw-Viepxab4m9pRRQyjm_yRq1uhOj9iPc&sensor=false"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var mapCenter = new google.maps.LatLng(47.6145, -122.3418); //Google map Coordinates
            var map;
            map_initialize(); // initialize google map
            //############### Google Map Initialize ##############
            function map_initialize()
            {
                var googleMapOptions =
                    {
                        center: mapCenter, // map center
                        zoom: 17, //zoom level, 0 = earth view to higher value
                        maxZoom: 18,
                        minZoom: 16,
                        zoomControlOptions: {
                            style: google.maps.ZoomControlStyle.SMALL //zoom control size
                        },
                        scaleControl: true, // enable scale control
                        mapTypeId: google.maps.MapTypeId.ROADMAP // google map type
                    };

                map = new google.maps.Map(document.getElementById("google_map"), googleMapOptions);

                //Load Markers from the XML File, Check (map_process.php)
                $.get("google-map-save-markers-db?get_map=1", function (data) {
                    $(data).find("marker").each(function () {
                        var name 		= $(this).attr('name');
                        var address 	= '<p>'+ $(this).attr('address') +'</p>';
                        var type 		= $(this).attr('type');
                        var point 	= new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
                        create_marker(point, name, address, false, false, false, "public/images/google_map_icons/pin_blue.png");
                    });
                });

                //Right Click to Drop a New Marker
                google.maps.event.addListener(map, 'rightclick', function(event) {
                    //Edit form to be displayed with new marker
                    var EditForm = '<p><div class="marker-edit">'+
                        '<form action="ajax-save.php" method="POST" name="SaveMarker" id="SaveMarker">'+
                        '<label for="pName"><span>Place Name :</span><input type="text" name="pName" class="save-name" placeholder="Enter Title" maxlength="30" /></label>'+
                        '<label for="pDesc"><span>Description :</span><textarea name="pDesc" class="save-desc" placeholder="Enter Address" maxlength="90"></textarea></label>'+
                        '<label for="pType"><span>Type :</span> <select name="pType" class="save-type"><option value="restaurant">Rastaurant</option><option value="bar">Bar</option>'+
                        '<option value="house">House</option></select></label>'+
                        '</form>'+
                        '</div></p><button name="save-marker" class="save-marker">Save Marker Details</button>';

                    //Drop a new Marker with our Edit Form
                    create_marker(event.latLng, 'New Marker', EditForm, true, true, true, "public/images/google_map_icons/pin_green.png");
                });

            }

            //############### Create Marker Function ##############
            function create_marker(MapPos, MapTitle, MapDesc,  InfoOpenDefault, DragAble, Removable, iconPath)
            {

                //new marker
                var marker = new google.maps.Marker({
                    position: MapPos,
                    map: map,
                    draggable:DragAble,
                    animation: google.maps.Animation.DROP,
                    title:"Hello World!",
                    icon: iconPath
                });

                //Content structure of info Window for the Markers
                var contentString = $('<div class="marker-info-win">'+
                    '<div class="marker-inner-win"><span class="info-content">'+
                    '<h1 class="marker-heading">'+MapTitle+'</h1>'+
                    MapDesc+
                    '</span><button name="remove-marker" class="remove-marker" title="Remove Marker">Remove Marker</button>'+
                    '</div></div>');

                //Create an infoWindow
                var infowindow = new google.maps.InfoWindow();
                //set the content of infoWindow
                infowindow.setContent(contentString[0]);

                //Find remove button in infoWindow
                var removeBtn 	= contentString.find('button.remove-marker')[0];
                var saveBtn 	= contentString.find('button.save-marker')[0];

                //add click listner to remove marker button
                google.maps.event.addDomListener(removeBtn, "click", function(event) {
                    remove_marker(marker);
                });

                if(typeof saveBtn !== 'undefined') //continue only when save button is present
                {
                    //add click listner to save marker button
                    google.maps.event.addDomListener(saveBtn, "click", function(event) {
                        var mReplace = contentString.find('span.info-content'); //html to be replaced after success
                        var mName = contentString.find('input.save-name')[0].value; //name input field value
                        var mDesc  = contentString.find('textarea.save-desc')[0].value; //description input field value
                        var mType = contentString.find('select.save-type')[0].value; //type of marker

                        if(mName =='' || mDesc =='')
                        {
                            alert("Please enter Name and Description!");
                        }else{
                            save_marker(marker, mName, mDesc, mType, mReplace); //call save marker function
                        }
                    });
                }

                //add click listner to save marker button
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker); // click on marker opens info window
                });

                if(InfoOpenDefault) //whether info window should be open by default
                {
                    infowindow.open(map,marker);
                }
            }

            //############### Remove Marker Function ##############
            function remove_marker(Marker)
            {
                /* determine whether marker is draggable
                 new markers are draggable and saved markers are fixed */
                if(Marker.getDraggable())
                {
                    Marker.setMap(null); //just remove new marker
                }
                else
                {
                    //Remove saved marker from DB and map using jQuery Ajax
                    var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
                    var myData = {del : 'true', latlang : mLatLang}; //post variables
                    $.ajax({
                        type: "POST",
                        url: "google-map-save-markers-db",
                        data: myData,
                        success:function(data){
                            Marker.setMap(null);
                            alert(data);
                        },
                        error:function (xhr, ajaxOptions, thrownError){
                            alert(thrownError); //throw any errors
                        }
                    });
                }

            }
            //############### Save Marker Function ##############
            function save_marker(Marker, mName, mAddress, mType, replaceWin)
            {
                //Save new marker using jQuery Ajax
                var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
                var myData = {name : mName, address : mAddress, latlang : mLatLang, type : mType }; //post variables
                console.log(replaceWin);
                $.ajax({
                    type: "POST",
                    url: "google-map-save-markers-db",
                    data: myData,
                    success:function(data){
                        replaceWin.html(data); //replace info window with new html
                        Marker.setDraggable(false); //set marker to fixed
                        Marker.setIcon('public/images/google_map_icons/pin_blue.png'); //replace icon
                    },
                    error:function (xhr, ajaxOptions, thrownError){
                        alert(thrownError); //throw any errors
                    }
                });
            }

        });
    </script>
    <style type="text/css">
        h1.heading{padding:0px;margin: 0px 0px 10px 0px;text-align:center;font: 18px Georgia, "Times New Roman", Times, serif;}
        /* width and height of google map */
        #google_map {width: 90%; height: 500px;margin-top:0px;margin-left:auto;margin-right:auto;border: 3px solid #DADADA;}
        /* Marker Edit form */
        .marker-edit label{display:block;margin-bottom: 5px;}
        .marker-edit label span {width: 100px;float: left;}
        .marker-edit label input, .marker-edit label select{height: 24px;}
        .marker-edit label textarea{height: 60px;}
        .marker-edit label input, .marker-edit label select, .marker-edit label textarea {width: 60%;margin:0px;padding-left: 5px;border: 1px solid #DDD;border-radius: 3px;}
        /* Marker Info Window */
        h1.marker-heading{color: #585858;margin: 0px;padding: 0px;font: 25px "Trebuchet MS", Arial!important;border-bottom: 1px dotted #D8D8D8;}
        div.marker-info-win {max-width: 300px;}
        div.marker-info-win p{padding: 0px;margin: 10px 0px 10px 0;}
        div.marker-inner-win{padding: 5px;}
        button.save-marker, button.remove-marker{border: none;background: rgba(0, 0, 0, 0);color: #00F;padding: 0px;text-decoration: underline;margin-right: 10px;cursor: pointer;
        }
    </style><script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-50194497-1', 'sanwebe.com');
        ga('send', 'pageview');
    </script>
    <!-- BuySellAds Ad Code -->
    <script type="text/javascript">
        (function(){
            var bsa = document.createElement('script');
            bsa.type = 'text/javascript';
            bsa.async = true;
            bsa.src = '//s3.buysellads.com/ac/bsa.js';
            (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
        })();
    </script>
</head>
<body>




<div class="this-top-google-ad">
    <div class="grid">
        <div class="col-12-12" align="center" style="overflow:hidden;">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- sanwebe-asset-top-responsive -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-0052126645916042"
                 data-ad-slot="1112792045"
                 data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
</div>
<div class="grid">
    <div class="col-12-12" content-wrapper"><div align="center">Right click on the map to add a new marker, and click on marker to edit and save.</div>
    <div id="google_map"></div></div>
</div>
<div class="grid">
    <div class="col-12-12">
        <div class="ad-bottom">
            <!-- BuySellAds Zone Code -->
            <div id="bsap_1289305" class="bsarocks bsap_3246a2e15c6824a11558262db4409e23"></div>
            <!-- End BuySellAds Zone Code -->
        </div>
    </div>
</div>

<footer>
    <div class="grid">

    </div>
</footer>
</body>
</html>