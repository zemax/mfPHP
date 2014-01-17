<?php
namespace MF\Exception;

use MF\Response;

class QuietHandler extends Handler {
	protected function handle404($e) {
		Response::setHTTPStatus(404);
		die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body style="font: 12pt Arial, helvetica, sans-serif">
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>');
	}
}
