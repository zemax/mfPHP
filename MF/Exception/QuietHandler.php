<?php
class MF_Exception_QuietHandler extends MF_Exception_Handler {
	protected function handle404($e) {
		MF_Response::setHTTPStatus(404);
		die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body style="font: 12pt Arial, helvetica, sans-serif">
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>');
	}
}
