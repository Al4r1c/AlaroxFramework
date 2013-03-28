<?php
return array(
    100 => array('Continue', ''),
    101 => array('Switching Protocols', ''),
    200 => array('OK', ''),
    201 => array('Created', ''),
    202 => array('Accepted', ''),
    203 => array('Non-Authoritative Information', ''),
    204 => array('No Content', ''),
    205 => array('Reset Content', ''),
    206 => array('Partial Content', ''),
    300 => array('Multiple Choices', ''),
    301 => array('Moved Permanently', ''),
    302 => array('Found', ''),
    303 => array('See Other', ''),
    304 => array('Not Modified', ''),
    305 => array('Use Proxy', ''),
    306 => array('(Unused)', ''),
    307 => array('Temporary Redirect', ''),
    400 => array('Bad Request',
        'The request could not be understood by the server due to malformed syntax.'),
    401 => array('Unauthorized', 'You must be authorized to view this page.'),
    402 => array('Payment Required', ''),
    403 => array('Forbidden',
        'The request was a valid request, but the server is refusing to respond to it.'),
    404 => array('Not Found', 'The requested URL was not found.'),
    405 => array('Method Not Allowed',
        'The method specified in the Request-Line is not allowed for this resource.'),
    406 => array('Not Acceptable',
        'The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.'),
    407 => array('Proxy Authentication Required', 'You must first authenticate with the proxy.'),
    408 => array('Request Timeout', 'The server timed out waiting for the request.'),
    409 => array('Conflict',
        'The request could not be completed due to a conflict with the current state of the resource.'),
    410 => array('Gone',
        'he requested resource is no longer available at the server and no forwarding address is known.'),
    411 => array('Length Required',
        'The request did not specify the length of its content, which is required by the requested resource.'),
    412 => array('Precondition Failed',
        'The server does not meet one of the preconditions that the requester put on the request.'),
    413 => array('Request Entity Too Large',
        'The request is larger than the server is willing or able to process.'),
    414 => array('Request-URI Too Long', 'The URI provided was too long for the server to process.'),
    415 => array('Unsupported Media Type',
        'The request entity has a media type which the server or resource does not support.'),
    416 => array('Requested Range Not Satisfiable',
        'You asked for a portion of the file, but the server cannot supply that portion.'),
    417 => array('Expectation Failed',
        'The server cannot meet the requirements of the Expect request-header field.'),
    500 => array('Internal Server Error', 'The server encountered an error processing your request.'),
    501 => array('Not Implemented', 'The requested method is not implemented.'),
    502 => array('Bad Gateway',
        'The server received an invalid response from the upstream server it accessed in attempting to fulfill the request.'),
    503 => array('Service Unavailable',
        'The server is currently unable to handle the request due to a temporary overloading or maintenance of the server.'),
    504 => array('Gateway Timeout',
        'The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.'),
    505 => array('HTTP Version Not Supported',
        'The server does not support the HTTP protocol version used in the request.'));