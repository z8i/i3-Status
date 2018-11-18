<?php
session_start();

if ( isset( $_SESSION['user'] ) ) { //If logged in, show page...    
} else {
    // If not, redirect to login
    $svname = $_SERVER['SERVER_NAME'];
    header("Location: https://$svname/login.php");
}

?>
<!DOCTYPE HTML>

<html>
    <head>
		<!-- <meta http-equiv="Content-Security-Policy" content="default-src 'none'; img-src 'self'; script-src 'self'; style-src 'self'"> -->
		<!-- <meta http-equiv="Content-Security-Policy" content="style-src *;img-src *;script-src 'self'"> -->
		
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="i3 SoC">
		<link rel="manifest" href="./manifest.json">

        <link rel="preload" href="./fonts/advent-pro-100-reduced.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="apple-touch-icon" href="./img/apple-touch-icon.png" type="image/png">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="./batterieapp_style.css">
		
        <title>i3 Status</title>
		<script type="text/javascript">
		/* Writes the actual system date and time into the HTML time element */
		function updateSiteTime() { //renamed Function from updateTime to updateSiteTime to make clear difference between it.updateTime
			var date = new Date();
			var stunden = date.getHours();
			var minuten = date.getMinutes();
			var tag = date.getDate();
			var monatDesJahres = date.getMonth();
			var jahr = date.getFullYear();
			var tagInWoche = date.getDay();
			var wochentag = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
			var monat = new Array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

			var datum = wochentag[tagInWoche] + ", " + tag + ". " + monat[monatDesJahres] + " " + jahr + " " + date.toLocaleTimeString('en-GB');
			document.getElementById('time').innerHTML = datum;
		}		
		/* Loads the graphics page (if the SOC SVG is clicked) */
		function loadGraphics(){
			window.location.assign("./graphs/graph.php");
		}
		</script>
        <script id="header-tmpl" type="text/x-dot-template">
            <h2>
                i3 State of Charge
            </h2>
            <h4>
                {{=it.updateTime}}
            </h4>
        </script>

        <script id="main-tmpl" type="text/x-dot-template">
            <span class="percent" data-percent="{{=it.chargingLevel}}" onclick="loadGraphics()"></span>

            <div class="water" data-charging="{{=it.chargingActive}}" style="height:{{=it.chargingLevel}}%">
                <svg viewBox="0 0 560 20" class="wave wave-back">
                    <use xlink:href="#wave"></use>
                </svg>
                <svg viewBox="0 0 560 20" class="wave wave-front" >
                    <use xlink:href="#wave" ></use>
                </svg>
            </div>
        </script>

        <script id="footer-tmpl" type="text/x-dot-template">
            <section>
                <h5>
                    aktuelle Reichweite <br> aktuelle Ladung
                </h5>
                <h3>
                    {{=it.Range}}<units> km</units> <br> {{=it.stateOfCharge}}<units> kWh</units>
                </h3>
            </section>
			<section>
                <h5>
                    letzer Ø <br> letzte Strecke
                </h5>
                <h3>
                    {{=it.consumption}}<units> <sup>kWh</sup>&frasl;<sub>100km</sub></units> <br> {{=it.lastLegMileage}}<units>km</units>
                </h3>
            </section>

            <section>
                <h5>
                    Ladung maximal <br> HV %
                </h5>
                <h3>
                    {{=it.stateOfChargeMax}}<units> kWh</units><br>{{=it.soc_hv_percent}}<units>%</units>
                </h3>
            </section>

			<section>
                <h5>
                    Voll geladen
                </h5>
                <h3>
                    in {{=it.chargingTimeRemaining}}<units>h</units><br>um {{=it.chargingClock}}<units>h</units> <br> mit zuletzt {{=it.chargingPower}} <units>kW</units>
                </h3>
            </section>
			
			<section>
                <h4>
                    Türen
                </h4>
                <h2>
                    {{=it.doorLockState}}
                </h2>
            </section>

            <section>
                <h4>
                    Laufleistung
                </h4>
                <h2>
                    {{=it.mileage}} <units>km</units>
                </h2>
            </section>
			<footerdatetext><div id="time"></div></footerdatetext>
        </script>
    </head>

    <body class="loading">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" hidden>
            <symbol id="wave">
                <path d="M420,20c21.5-0.4,38.8-2.5,51.1-4.5c13.4-2.2,26.5-5.2,27.3-5.4C514,6.5,518,4.7,528.5,2.7c7.1-1.3,17.9-2.8,31.5-2.7c0,0,0,0,0,0v20H420z"></path>
                <path d="M420,20c-21.5-0.4-38.8-2.5-51.1-4.5c-13.4-2.2-26.5-5.2-27.3-5.4C326,6.5,322,4.7,311.5,2.7C304.3,1.4,293.6-0.1,280,0c0,0,0,0,0,0v20H420z"></path>
                <path d="M140,20c21.5-0.4,38.8-2.5,51.1-4.5c13.4-2.2,26.5-5.2,27.3-5.4C234,6.5,238,4.7,248.5,2.7c7.1-1.3,17.9-2.8,31.5-2.7c0,0,0,0,0,0v20H140z"></path>
                <path d="M140,20c-21.5-0.4-38.8-2.5-51.1-4.5c-13.4-2.2-26.5-5.2-27.3-5.4C46,6.5,42,4.7,31.5,2.7C24.3,1.4,13.6-0.1,0,0c0,0,0,0,0,0l0,20H140z"></path>
            </symbol>
        </svg>


        <header id="header"></header>
        <main id="main"></main>
        <footer id="footer"></footer>

        <script src="./scripts/doT.min.js"></script>
        <script>
            const req = new XMLHttpRequest();
			var no_charging_level_refresh  =3600000; // 60min			
			var high_charging_level_refresh=300000; // 5min
			var mid_charging_level_refresh =120000;	// 2min		
			var low_charging_level_refresh =60000;  // 60s
			const max_charging_power=50;
			
            req.addEventListener( 'load', function() {
                if ( this.status !== 200 ) {
                    return alert( 'Request failed: ' + this.status );
                }
				//console.log(this.responseText);
                const bmwCDjson = JSON.parse( this.responseText );
				//console.log(this.responseText);
                [ 'header', 'main', 'footer' ].forEach( id => {
                    document.getElementById( id ).innerHTML = doT.template( document.getElementById( `${id}-tmpl` ).text )( bmwCDjson );
                } );
                document.getElementsByTagName( 'body' )[0].classList.remove( 'loading' );
				updateSiteTime();
				
				// check if we should refresh the window after a while
				var chargingLevel=bmwCDjson["chargingLevel"];
				var chargingPower=bmwCDjson["chargingPower"];
				var chargingActive=bmwCDjson["chargingActive"];
				if(chargingActive>0 && typeof chargingActive != 'undefined' && typeof chargingLevel != 'undefined' && typeof chargingLevel != 'undefined')
				{
					console.log("chargingActive - Level:"+chargingLevel+"-"+chargingPower);
					var timeout=high_charging_level_refresh;
					if(chargingPower>0)
					{	
						console.log("chargingPower:"+chargingPower);
						if(chargingPower>20){
							console.log("chargingPower >20");
							timeout=low_charging_level_refresh;
						} else if(chargingPower>10) {
							console.log("chargingPower >10");
							timeout=mid_charging_level_refresh;					
						}
					} else if(chargingLevel<60){
						console.log("Charging Level <60");
						timeout=low_charging_level_refresh;
					} else if(chargingLevel<80){
						console.log("Charging Level <80");
						timeout=mid_charging_level_refresh;
					} 
					console.log("timeout:"+timeout);
					setTimeout("location.reload(true)", timeout);
				}
				else {
					console.log("Your BMW is not charging"+"-"+chargingActive+"-"+chargingLevel+"-"+chargingPower);
					setTimeout("location.reload(true)", no_charging_level_refresh);
				}
            } );

            req.open( 'GET', './api/' );
            req.send();
        </script>
        <script>
            window.addEventListener( 'load', function( e ) {
				// see https://www.html5rocks.com/en/tutorials/appcache/beginner/
				// and // https://stackoverflow.com/questions/10323392/in-javascript-jquery-what-does-e-mean 
                window.applicationCache.addEventListener( 'updateready', function( e ) { 
                    if ( window.applicationCache.status == window.applicationCache.UPDATEREADY ) {
                        window.applicationCache.swapCache();
                        window.location.reload();
                    }
                }, false );

            }, false );
		</script>
    </body>
</html>
