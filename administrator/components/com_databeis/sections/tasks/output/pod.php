<?php
define("ON_OUR_SITE", true);
include('configa.php');
//defined( '_JEXEC' ) or die( 'Restricted access' );
    mysql_connect($host, $db_user, $db_psw) or die("Error connecting to database: ".mysql_error());
    mysql_select_db($db_name) or die(mysql_error());
?>

<html>
<head>
<script>
    function goSelect(data,tensione,potenza){
        var idFromCallPage = getUrlVars()["id"];
		window.opener.document.getElementById("rigatipo").style.display = "";
        window.opener.callFromDialog(idFromCallPage,data,tensione,potenza);
        window.close();
    }


    function getUrlVars(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
	
</script>

<style type="text/css">
table.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
table.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
table.tftable tr {background-color:#d4e3e5;}
table.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
</style>

</head>
<body>
<table id="tfhover" class="tftable" border="1">
<tr><th>POD</th><th>EnelTel</th><th>Ubicazione</th><th>Tensione [V]</th><th>Potenza [kW]</th></tr>

<?php
		$commessa = $_GET['commessa'];
		$pod = $_GET['pod'];
		$tiporicerca = $_GET['tiporicerca'];
		
		$commessa = htmlspecialchars($commessa); 
		$pod = htmlspecialchars($pod); 
		$tiporicerca = htmlspecialchars($tiporicerca); 
		
		$commessa = mysql_real_escape_string($commessa);
		$pod = mysql_real_escape_string($pod);
		$tiporicerca = mysql_real_escape_string($tiporicerca);
         
        $raw_results = mysql_query("SELECT Pod, ValoreTensione, Potenza_disp, Indirizzo_fornitura, CodiceCliente  FROM anagrafica2 WHERE Commessa = '" . $commessa . "' AND " . $tiporicerca . " LIKE '%" . $pod . "%'") or die(mysql_error());
 
        if(mysql_num_rows($raw_results) > 0){ 
             
            while($results = mysql_fetch_array($raw_results)){
				echo "<tr><td><a href=\"#\" onclick=\"goSelect('".$results['Pod']."','".$results['ValoreTensione']."','".$results['Potenza_disp']."')\">".$results['Pod']."</a></td><td>".$results['CodiceCliente']."</td><td>".$results['Indirizzo_fornitura']."</td><td>".$results['ValoreTensione']."</td><td>".$results['Potenza_disp']."</td></tr>";
            }
             
        }
        else{
            echo "<tr><td colspan=6><b>Nessuna corrispondenza</b></td></tr>";
		}
?>
</table>
</body>
</html>