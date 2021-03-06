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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Battery Graphics">
        <link rel="icon" href="../img/favicon.ico?2017-01-21-v3" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="../batterieapp_style.css">
	    <title>i3 Graphics</title>	
			
		<!-- scripts for graphics display Chart.cs
			The MIT License (MIT)
			Copyright (c) 2013-2017 Nick Downie
			Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
			The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
			THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
		-->
		<script src="./Chart/Chart.bundle.js"></script>
		<!-- scripts for OpenLayerMaps display 
			Licence: https://www.tldrlegal.com/l/freebsd 
		-->
		<link rel="stylesheet" href="./OpenLayer/ol.css" type="text/css">		
		<script src="./OpenLayer/ol.js"></script>	

		<script type="text/javascript">
			var globallat;
			var globallon;
			var vectorSource;
			var map;
			
			window.chartColors = {
				red: 'rgb(255, 99, 132)',
				orange: 'rgb(255, 159, 64)',
				yellow: 'rgb(255, 205, 86)',
				green: 'rgb(75, 192, 192)',
				blue: 'rgb(54, 162, 235)',
				purple: 'rgb(153, 102, 255)',
				grey: 'rgb(231,233,237)',
				lightgrey: 'rgb(235,240,245)',			
				white: 'rgb(255,255,255)'
			};

			window.randomScalingFactor = function() {
				return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
			}	
			
			/*
			converts a UNIX timestamp into a displaystring
			Format: (d)d.(m)m.(yy)y time like 13.10.17 13:35:29
			A Unix timestamp is defined as the number of seconds that have elapsed 
			since 00:00:00 Coordinated Universal Time (UTC), Thursday, 1 January 1970.
			JavaScript Date works with MILLIseconds! so in this case UNIX_timestamp is also in milliseconds
			*/
			function timeConverter(UNIX_timestamp) {		
				var timestamp=parseInt(UNIX_timestamp.toString());
				// hmm there still seems to be some summertime issue in the data
				// to be rechecked in wintertime period
				var date = new Date(timestamp-3600000);
				var stunden = date.getHours();
				var minuten = date.getMinutes();
				var tag = date.getDate(); // day of the month 1-31
				var monatDesJahres = date.getMonth(); // 0-11
				// Year in the 2000th
				var jahr = date.getFullYear()-2000;
				var tagInWoche = date.getDay();

				monatDesJahres++;
				var datum = tag + "." + monatDesJahres +"."+jahr+" " + date.toLocaleTimeString('en-GB');
				return datum;
			}
			
			//
			// returns the number of the week within the year
			//			
			function getWeekNumber(d) {
				// Copy date so don't modify original
				d = new Date(+d);
				d.setHours(0,0,0,0);
				// Set to nearest Thursday: current date + 4 - current day number
				// Make Sunday's day number 7
				d.setDate(d.getDate() + 4 - (d.getDay()||7));
				// Get first day of year
				var yearStart = new Date(d.getFullYear(),0,1);
				// Calculate full weeks to nearest Thursday
				var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
				// console.log("getWeekNumber: "+d.getFullYear()+","+weekNo);	
				// Return array of year and week number
				return weekNo;
			}
			
			//
			// returns the number of the month within the year
			// d: Date			
			//
			function getMonthNumber(d) {
				// Copy date so don't modify original
				d = new Date(+d);
				var monthNo = d.getMonth()+1;
				// Return array of year and month number
				// console.log("getMonthNumber: "+d.getFullYear()+","+monthNo);				
				return monthNo;
			}
			
			//
			// returns the number of the day within the year
			// d: Date			
			//
			function getDayNumber(d) {
				// Copy date so don't modify original
				d = new Date(+d);
				var start= new Date(d.getFullYear(), 0, 0);
				var diff=d-start;
				var oneDay = 1000 * 60 * 60 * 24;
				var day = Math.floor(diff / oneDay);
				// Return array of year and month number
				// console.log("getDayNumber - "+d.getFullYear()+","+day);
				return day;
			}
			
			//
			// Returns the number of days between two dates
			//
			function getDayDifference(a,b){
				var _MS_PER_DAY = 1000 * 60 * 60 * 24;
				// Discard the time and time-zone information.
				var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
				var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

				return Math.floor((utc2 - utc1) / _MS_PER_DAY);
			}
			
			//
			// Returns the number of weeks between two dates
			//
			function getWeekDifference(a,b){
				var _MS_PER_WEEK = 1000 * 60 * 60 * 24 * 7;
				// Discard the time and time-zone information.
				var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
				var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

				return Math.floor((utc2 - utc1) / _MS_PER_WEEK);
			}
			
			//
			// Returns the number of month between two dates
			//
			function getMonthDifference(a,b){
				var _MS_PER_MONTH = 1000 * 60 * 60 * 24 * 7 * 4;
				// Discard the time and time-zone information.
				var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
				var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

				return Math.floor((utc2 - utc1) / _MS_PER_MONTH);
			}
			
			//
			// returns the number of periods within the year
			// d: Date
			// periodtype: String
			//
			function getPeriodNumber(startdate,actdate,periodtype) {
				var noperiods=0;
				
				switch(periodtype){
				case "day": 
					noperiods=getDayDifference(startdate,actdate)+1;
					//console.log("getPeriodNumber - day:"+noperiods);
					return noperiods;
				case "week": 
					noperiods=getWeekDifference(startdate,actdate)+1;
					//console.log("getPeriodNumber - week:"+noperiods);
					return noperiods;										
				case "month": 
					noperiods=getMonthDifference(startdate,actdate)+1;
					//console.log("getPeriodNumber - month:"+noperiods);
					return noperiods;						
				}		
			}
						
			//
			// clears grpahic data
			//
			function clearData(config){
				//console.log("clearData");
				config.data.labels=[];
				config.data.datasets.forEach(function(dataset) {
					dataset.data=[];
				});
				window.myLine.update();
			}
			
			//
			// Returns number of periods to be displayed
			// New Period starts only if the actual SOC has changed!
			//
			function getNumberOfPeriods(json,periodType){
				var maxperiods=-1;
				var timestamp_obj=json.TIMESTAMP;
				var max_soc_obj=json.MAX_SOC;
				var oldsocfloat=0.0;
				var socstr;
				var socfloat=0.0;
				
				//console.log("getNumberOfPeriods - Start:"+" type:"+periodType);				
				if(timestamp_obj.length<0)
					return maxperiods;
					
				var timestamp=parseInt(timestamp_obj[0].toString());
				var startdate = new Date(timestamp);
				
				for(i=0;i<timestamp_obj.length;i++){
					// create the SOC as float value
					socstr=max_soc_obj[i].toString();
					socstr=socstr.replace(",",".");
					socfloat=parseFloat(socstr);
					
					// show only SOC changes
					if(socfloat!=oldsocfloat){
						timestamp=parseInt(timestamp_obj[i].toString());
						var date = new Date(timestamp);
						period=getPeriodNumber(startdate,date,periodType);
						if(maxperiods<0){
							maxperiods=period;
						}
						if(period>maxperiods){
							// new period started
							maxperiods=period;
						}
					}
				}
				//console.log("getNumberOfPeriods - Number of periods:"+maxperiods+" type:"+periodType);
				return maxperiods;
			}
			
			//
			// Returns the lable for horizontal axis
			// for the graphic based on period type
			// 
			function getChartLable(d,periodtype) {
				var lable="";
				switch(periodtype){
				case "day": lable=d.getDate()+"."+(d.getMonth()+1)+"."+d.getFullYear(); break;
				case "week": lable="w"+getWeekNumber(d)+" "+d.getFullYear();break;
				case "month": lable=(d.getMonth()+1)+"."+d.getFullYear();break;
				}		
				//console.log("ChartLable:"+lable);
				return lable;
			}
			
			function showHVPercPeriodData(json,config,periodType){
				var max_soc_obj=json.MAX_SOC;
				var hv_perc_obj=json.SOC_HV_PERCENT;
				var soc_perc_obj=json.SOC;
				console.log(json);
				
				var timestamp_obj=json.TIMESTAMP;
				var average=0.0;
				var oldperiod=-1;
                //console.log(periodType+"_Data");
				
				if (config.data.datasets.length > 0) {
					dataset=config.data.datasets[0];
					var socfloat=0.0;					
					var hvpercfloat=0.0;
					var diff=0;
					var hvpercstr="";
					
					var timestamp=parseInt(timestamp_obj[0].toString());
					var startdate = new Date(timestamp);
				
					// check how many periods we have
					// used to compress display data
					maxperiods=getNumberOfPeriods(json,periodType);
					//console.log("showPeriodData - maxperiods:"+maxperiods);
	
					for(i=0;i<timestamp_obj.length;i++){					
						// create the SOC as float value
						hvpercstr=hv_perc_obj[i].toString();
						// decimal point replacement as stored in german notation using ','
						hvpercstr=hvpercstr.replace(",",".");
						hvpercfloat=parseFloat(hvpercstr);
						
						// create the max_hv_perc as float value
						socstr=soc_perc_obj[i].toString();
						// decimal point replacement as stored in german notation using ','
						socstr=socstr.replace(",",".");
						socfloat=parseFloat(socstr);
						
						var timestamp=parseInt(timestamp_obj[i].toString());
						var date = new Date(timestamp);
						period=getPeriodNumber(startdate,date,periodType);
						if(oldperiod<0){
							// initialize oldperiod and averagemaxsoc soc value for calculation
							console.log("showPeriodData - init period");
							averagehvperc=hvpercfloat;
							oldperiod=period;
						}
						//console.log("showPeriodData - p,op,diff:"+period+","+oldperiod+", max/period:"+(maxperiods/period));							
						if(period>(oldperiod+maxperiods/period) && socfloat==100){
							// new display period started
							if(hvpercfloat>0)
								averagehvperc=hvpercfloat;							
							timestr=getChartLable(date,periodType);
							//console.log("showPeriodData - New period started:"+"p,op,diff:"+period+", "+oldperiod+", "+(maxperiods/period)+", timestr: >"+timestr+"<");
							oldperiod=period;
							console.log("showPeriodData - 100% soc hit hv_percent:"+hvpercfloat+"; av: "+averagehvperc);
							config.data.labels.push(timestr);

							dataset.data.push(averagehvperc);
						}
						else{
							if(socfloat==100){
								//console.log("showPeriodData - 100% soc hit hv_percent:"+hvpercfloat+"; av: "+averagehvperc);
								if(hvpercfloat!=0)
									averagehvperc=(averagehvperc+hvpercfloat)/2;
							}
						}
					}					
				}
				window.myLine.update();
			}	
			
			function showPeriodData(json,config,periodType){
				var max_soc_obj=json.MAX_SOC;
				//console.log(json);
				
				var timestamp_obj=json.TIMESTAMP;
				var average=0.0;
				var oldperiod=-1;
                //console.log(periodType+"_Data");
				
				if (config.data.datasets.length > 0) {
					dataset=config.data.datasets[0];
					var oldmaxsocfloat=0.0;
					var maxsocstr="";
					var maxsocfloat=0.0;				
					var diff=0;
					
					var timestamp=parseInt(timestamp_obj[0].toString());
					var startdate = new Date(timestamp);
				
					// check how many periods we have
					// used to compress display data
					maxperiods=getNumberOfPeriods(json,periodType);
					//console.log("showPeriodData - maxperiods:"+maxperiods);
	
					oldmaxsocfloat=0.0;
					for(i=0;i<timestamp_obj.length;i++){
						// create the max SOC as float value
						maxsocstr=max_soc_obj[i].toString();
						// decimal point replacement as stored in german notation using ','
						maxsocstr=maxsocstr.replace(",",".");
						maxsocfloat=parseFloat(maxsocstr);
						
						// show only SOC changes
						if(maxsocfloat!=oldmaxsocfloat){
							var timestamp=parseInt(timestamp_obj[i].toString());
							var date = new Date(timestamp);
							period=getPeriodNumber(startdate,date,periodType);
							if(oldperiod<0){
								// initialize oldperiod and averagemaxsoc soc value for calculation
								//console.log("showPeriodData - init period");
								averagemaxsoc=maxsocfloat;
								oldperiod=period;
							}
							//console.log("showPeriodData - p,op,diff:"+period+","+oldperiod+", max/period:"+(maxperiods/period)+", av soc:"+averagemaxsoc);
							//console.log("showPeriodData - p,op,diff:"+period+","+oldperiod+", max/period:"+(maxperiods/period));							
							if(period>(oldperiod+maxperiods/period)){
								// new display period started
								timestr=getChartLable(date,periodType);
								//console.log("showPeriodData - New period started:"+"p,op,diff:"+period+", "+oldperiod+", "+(maxperiods/period)+", timestr: >"+timestr+"<");
								config.data.labels.push(timestr);
								dataset.data.push(averagemaxsoc);
								oldmaxsocfloat=maxsocfloat;
								oldperiod=period;
								averagemaxsoc=maxsocfloat;
							}
							else{
								averagemaxsoc=(averagemaxsoc+maxsocfloat)/2;
							}
							
						}
					}					
				}
				window.myLine.update();
			}		
		
			//
			// Displays all SOC entries filtered by SOC change
			// means same values are not displayed if they are in a sequence
			// 
			function fullData(json,config){
				var max_soc_obj=json.MAX_SOC;
				var timestamp_obj=json.TIMESTAMP;
                console.log("fullData");
				/* for testing purposes */
				// Add some debug here
				/* end test */
				if (config.data.datasets.length > 0) {
					dataset=config.data.datasets[0];
					var oldsocfloat=0.0;
					var socstr;
					varscofloat=0.0;
					// iterating across all timestamps
					for(i=0;i<timestamp_obj.length;i++){
						socstr=max_soc_obj[i].toString();
						socstr=socstr.replace(",",".");
						socfloat=parseFloat(socstr);
						// show only SOC changes
						if(socfloat!=oldsocfloat){
							timestr=timeConverter(timestamp_obj[i]);
							//console.log("fulldata: "+socstr+","+timestr);
							config.data.labels.push(timestr);
							dataset.data.push(socfloat);
							oldsocfloat=socfloat;
						}
					}					
				}
				window.myLine.update();
			}
			
			function Lon2Merc(lon) {
				return 20037508.34 * lon / 180;
			}

			function Lat2Merc(lat) {
				var PI = 3.14159265358979323846;
				lat = Math.log(Math.tan( (90 + lat) * PI / 360)) / (PI / 180);
				return 20037508.34 * lat / 180;
			}
			
			// geoLocation works only on secure connections
			function getLocation() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(showPosition);
				}
			}
		
			function showPosition(position) {
				globallat= position.coords.latitude;
				globallon=position.coords.longitude;
				console.log( "Your are at Latitude: " + position.coords.latitude +" Longitude: " + position.coords.longitude);
				var iconFeature = new ol.Feature({
					  geometry: new ol.geom.Point(ol.proj.transform([globallon, globallat], 'EPSG:4326',  'EPSG:3857')),
					  name: 'your postion'
					});
				var iconStyle = new ol.style.Style({
				  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
					anchor: [0.5, 46],
					anchorXUnits: 'fraction',
					anchorYUnits: 'pixels',
					//opacity: 0.75,
					src: './male.png'
				  }))
				});		
				iconFeature.setStyle(iconStyle);
				vectorSource.addFeature(iconFeature);
				var view = map.getView();
				// fit all vectors into view and zoom out
				view.fit(vectorSource.getExtent(), map.getSize());
				var zoom = view.getZoom();
				var viewCenter=view.getCenter();
				// zoom out by one
				map.setView(new ol.View({ center: viewCenter, zoom: (zoom-1) }));
			}

			function getLastLonOfCar(json){
				var lastlon=json.GPS_LON;
				var lon=0.0;

				var lonstr=lastlon[lastlon.length-1].toString();
				lonstr=lonstr.replace(",",".");
				lon=parseFloat(lonstr);
				
				//console.log(lon);
				return lon;
			}
			
			function getLastLatOfCar(json){
				var lastlon=json.GPS_LAT;
				var lon=0.0;

				var lonstr=lastlon[lastlon.length-1].toString();
				lonstr=lonstr.replace(",",".");
				lon=parseFloat(lonstr);
				
				//console.log(lon);
				return lon;
			}
		</script>


		
    </head>

    <body>
		<BR>
		<div><canvas id="i3SOCChart" width="100%"></canvas></div>
		<br>
		<center>
			<button id="dailyData">daily data</button>
			<button id="weeklyData">weekly data</button>
			<button id="monthlyData">monthly data</button>
			<button id="fullData">full data</button>
			<button id="hvPercData">HV Perc</button>			
		</center>
		<br>
		<center>
			<div id="osm_map" style="width:90%;height:300px;line-height:normal;margin:auto"></div>
			<button id="zoom-out">Zoom out</button>
			<button id="zoom-in">Zoom in</button>
			<button id="centralpos">Center</button>			
		</center>
		<script>
        var config = {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "SOC Max",
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.grey,
                    data: [],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:true,
					fontColor: window.chartColors.lightgrey,
                    text:'i3 data'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Timeline',
							fontColor: window.chartColors.lightgrey,
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'kWh',
							fontColor: window.chartColors.lightgrey,
                        }
                    }]
                }
            }
        };
        var hvpercconfig = {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: "HV Percentage",
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.grey,
                    data: [],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:true,
					fontColor: window.chartColors.lightgrey,
                    text:'i3 data'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Timeline',
							fontColor: window.chartColors.lightgrey,
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '%',
							fontColor: window.chartColors.lightgrey,
                        }
                    }]
                }
            }
        };		
		var json;
	
        window.onload = function() {
            var ctx = document.getElementById("i3SOCChart").getContext("2d");
            window.myLine = new Chart(ctx, config);
			// Position und Zoomstufe der Karte, default Stuttgart
			var carlon = 9.1833;
			var carlat = 48.7667;
			var zoom = 10;
			globallat= carlat;
			globallon= carlon;

			var html_element = document.getElementById('osm_map');
			var osm_default = new ol.layer.Tile({ source: new ol.source.OSM() });			
			map = new ol.Map(html_element);

			// build actual graph on car data
			const req = new XMLHttpRequest();		
            req.addEventListener( 'load', function() {
                if ( this.status !== 200 ) {
                    return alert( 'Request failed: ' + this.status );
                }
				// get location of "browser"
				getLocation();	
				
				//console.log(this.responseText);
                json = JSON.parse( this.responseText );
				showPeriodData(json,config,"day");
				// get the last location of the car (if submitted)
				carlon=getLastLonOfCar(json);
				carlat=getLastLatOfCar(json);

				// iconfeatures to store icons/markers on the map
				vectorSource = new ol.source.Vector();	
				var iconFeature = new ol.Feature({
				  geometry: new ol.geom.Point(ol.proj.transform([carlon, carlat], 'EPSG:4326',  'EPSG:3857')),
				  name: 'car postion'
				});
				var iconStyle = new ol.style.Style({
				  image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
					anchor: [0.5, 46],
					anchorXUnits: 'fraction',
					anchorYUnits: 'pixels',
					opacity: 0.75,
					src: './car.png'
				  }))
				});		
				iconFeature.setStyle(iconStyle);
				vectorSource.addFeature(iconFeature);
								
				var vectorLayer = new ol.layer.Vector({
				  source: vectorSource,
				  //style: iconStyle
				});
				map.addLayer( new ol.layer.Tile({ source: new ol.source.OSM() }));
				map.addLayer(vectorLayer);
				map.setView(new ol.View({ center: ol.proj.transform([carlon, carlat], 'EPSG:4326', 'EPSG:3857'), zoom: zoom }));
				map.setTarget("osm_map");
            } );

			document.getElementById('fullData').addEventListener('click', function() {
				window.myLine.destroy();
				window.myLine = new Chart(ctx, config);
				clearData(config);
				fullData(json,config);
			});
			document.getElementById('weeklyData').addEventListener('click', function() {
				window.myLine.destroy();
				window.myLine = new Chart(ctx, config);
				clearData(config);
				showPeriodData(json,config,"week");
			});	
			document.getElementById('monthlyData').addEventListener('click', function() {
				window.myLine.destroy();
				window.myLine = new Chart(ctx, config);
				clearData(config);
				showPeriodData(json,config,"month");
			});
			document.getElementById('dailyData').addEventListener('click', function() {
				window.myLine.destroy();
				window.myLine = new Chart(ctx, config);
				clearData(config);
				showPeriodData(json,config,"day");
			});	
			document.getElementById('hvPercData').addEventListener('click', function() {
				window.myLine.destroy();
				window.myLine = new Chart(ctx, hvpercconfig);			
				clearData(hvpercconfig);
				showHVPercPeriodData(json,hvpercconfig,"day");
			});	
			
			document.getElementById('zoom-out').onclick = function() {
				var view = map.getView();
				var zoom = view.getZoom();
				view.setZoom(zoom - 1);
			};
			document.getElementById('zoom-in').onclick = function() {
				var view = map.getView();
				var zoom = view.getZoom();
				view.setZoom(zoom + 1);
			};
			document.getElementById('centralpos').onclick = function() {
				var view = map.getView();
				// fit all vectors into view and zoom out
				view.fit(vectorSource.getExtent(), map.getSize());
				var zoom = view.getZoom();
				var viewCenter=view.getCenter();
				// zoom out by one
				map.setView(new ol.View({ center: viewCenter, zoom: (zoom-1) }));
			};			
            req.open( 'GET', './index.php' );
            req.send();
        };
		</script>
    </body>
</html>
