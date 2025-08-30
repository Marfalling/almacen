<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<style> #map { width: 100%; height: 300px; border: 1px solid #d0d0d0; } </style>
	
	<script>
		function localize() 
		{
			if (navigator.geolocation) 
			{
				navigator.geolocation.getCurrentPosition(mapa,error);
			} 
			else 
			{
				alert('Tu navegador no soporta geolocalizacion.');
			}
		}


		function mapa(pos) 
		{ 
			/************************ Aqui están las variables que te interesan***********************************/
			var latitud = pos.coords.latitude;
			var longitud = pos.coords.longitude;
			var precision = pos.coords.accuracy;
			var contenedor = document.getElementById("map")
			document.getElementById("lti").value = latitud;
			document.getElementById("lgi").value = longitud;
			//document.getElementById("lti").innerHTML=latitud;
			//document.getElementById("lgi").innerHTML=longitud;
			//document.getElementById("psc").innerHTML=precision;
			var centro = new google.maps.LatLng(latitud,longitud);
			var propiedades = { zoom: 18, center: centro, mapTypeId: google.maps.MapTypeId.ROADMAP };
			var map = new google.maps.Map(contenedor, propiedades);
			var marcador = new google.maps.Marker({ position: centro, map: map, title: "Tu posicion actual" });
		}
		function error(errorCode) 
		{
			/*
			if(errorCode.code == 1) alert("No has permitido buscar tu localizacion")
			else if (errorCode.code==2) alert("Posicion no disponible")
			else alert("Ha ocurrido un error")
			*/
		}
	</script>
</head>

<body onLoad="localize()">
	<!-- <h1>Google Maps Geoposicionamiento</h1> -->
	<!--<p>Latitud: <span id="lti"></span></p> -->
	<!-- <p>Longitud: <span id="lgi"></span></p> -->
	<!-- <p>Presici&oacute;n: <span id="psc"></span></p> -->
	<!-- <div id="map" ></div> -->
	<input type="text" id="lti" name="lti" size="2" style="visibility: hidden;" > 
	<input type="text" id="lgi" name="lgi" size="2" style="visibility: hidden;" > 
</body>
</html>