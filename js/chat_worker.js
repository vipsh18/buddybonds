onmessage = function(e) {
	var message_id = e.data;
	var xhttp = new XMLHttpRequest();
	var param = "mid=" + message_id;
	xhttp.open("POST", "http://localhost/buddyBonds_backup/scripts/get_new_message.php", true);
	//Send the proper header information along with the request
	xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhttp.onreadystatechange = function() {
    	if (this.readyState == 4 && this.status == 200) postMessage(xhttp.responseText);
	};
	xhttp.send(param);
}