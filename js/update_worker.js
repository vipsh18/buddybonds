onmessage = function(e) {
	fetch("http://localhost/buddyBonds_backup/scripts/check_for_updates.php", {
	    method: "GET",
	}).then(res => res.json())
	.then(result => {
	    postMessage(result);
	}).catch(error => console.log(Error(error)));
}