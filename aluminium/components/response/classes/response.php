<?php
/**
 * This file contains the Response class.
 *
 * It is based on the Http package from "The Aura Project for PHP", licensed under the Simplified BSD license.
 *
 * @copyright	2010-2012 The Aura Project for PHP <http://auraphp.github.com/>
 * @license		https://github.com/auraphp/Aura.Http/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\Response;

/**
 * Represents an HTTP response that can be sent from the server to the client.
 */
class Response {

	/**
	 * The cookies for the response.
	 *
	 * @var mixed
	 */
	protected $cookies;

	/**
	 * The headers for the response.
	 *
	 * @var mixed
	 */
	protected $headers;

	/**
	 * The content of the response.
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * The HTTP status code of the response.
	 *
	 * @var int
	 */
	protected $status_code;

	/**
	 * The HTTP status message of the response.
	 *
	 * @var string
	 */
	protected $status_message;

	/**
	 * HTTP status codes and messages.
	 *
	 * @var array
	 */
    protected $statuses = array(
		 100 => 'Continue',
		 101 => 'Switching Protocols',
		 200 => 'OK',
		 201 => 'Created',
		 202 => 'Accepted',
		 203 => 'Non-Authoritative Information',
		 204 => 'No Content',
		 205 => 'Reset Content',
		 206 => 'Partial Content',
		 300 => 'Multiple Choices',
		 301 => 'Moved Permanently',
		 302 => 'Found',
		 303 => 'See Other',
		 304 => 'Not Modified',
		 305 => 'Use Proxy',
		 307 => 'Temporary Redirect',
		 400 => 'Bad Request',
		 401 => 'Unauthorized',
		 402 => 'Payment Required',
		 403 => 'Forbidden',
		 404 => 'Not Found',
		 405 => 'Method Not Allowed',
		 406 => 'Not Acceptable',
		 407 => 'Proxy Authentication Required',
		 408 => 'Request Timeout',
		 409 => 'Conflict',
		 410 => 'Gone',
		 411 => 'Length Required',
		 412 => 'Precondition Failed',
		 413 => 'Request Entity Too Large',
		 414 => 'Request-URI Too Long',
		 415 => 'Unsupported Media Type',
		 416 => 'Requested Range Not Satisfiable',
		 417 => 'Expectation Failed',
		 500 => 'Internal Server Error',
		 501 => 'Not Implemented',
		 502 => 'Bad Gateway',
		 503 => 'Service Unavailable',
		 504 => 'Gateway Timeout',
		 505 => 'HTTP Version Not Supported',
    );

	/**
	 * Response constructor.
	 *
	 * @param	Headers	$headers	A Headers instance.
	 * @param	Cookies	$cookies	A Cookies instance.
	 */
	public function __construct(Headers $headers, Cookies $cookies) {
		$this->headers = $headers;
		$this->cookies = $cookies;
		$this->set_status_code(200);
	}

	/**
	 * Returns the headers (not including cookies).
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Sets the headers (not including cookies).
	 *
	 * @param	Headers	$headers	A Headers instance.
	 */
	public function set_headers(Headers $headers) {
		$this->headers = $headers;
	}

	/**
	 * Returns the cookies.
	 */
	public function get_cookies() {
		return $this->cookies;
	}

	/**
	 * Sets the cookies.
	 *
	 * @param	Cookies	$cookies	A Cookies instance.
	 */
	public function set_cookies(Cookies $cookies) {
		$this->cookies = $cookies;
	}

	/**
	 * Returns the content.
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Sets the content.
	 *
	 * @param	mixed	$content	The content of the response. Note that this could be a resource,
	 * in that case it will be streamed out when sending.
	 */
	public function set_content($content) {
		$this->content = $content;
	}

	/**
	 * Returns the HTTP status code.
	 */
	public function get_status_code() {
		return $this->status_code;
	}

	/**
	 * Sets the HTTP status code. Automatically resets the status text to the default for that code.
	 *
	 * @param	int	$code	An HTTP status code.
	 */
	public function set_status_code($code) {
		$code = (int) $code;

		// If the status code does not appear in the statuses array, stop the process
		if(!array_key_exists($code, $this->statuses)) {
			trigger_error('Status code '.$code.' not recognized.', E_USER_ERROR);
		}

		$this->status_code = $code;

		$this->set_status_message($this->statuses[$code]);
	}

	/**
	 * Returns the HTTP status message.
	 */
	public function get_status_message() {
		return $this->status_message;
	}

	/**
	 * Sets the HTTP status message.
	 *
	 * @param	string	$message	The status message.
	 */
	public function set_status_message($message) {
		$message = trim(str_replace(array("\r", "\n"), '', $message));
		$this->status_message = $message;
	}

	/**
	 * Sends the full HTTP response.
	 */
	public function send() {
		$this->send_headers();
		$this->send_content();
	}

	/**
	 * Sends the HTTP status code, status test, headers and cookies.
	 */
	public function send_headers() {
		$status = 'HTTP/1.1 '.$this->status_code.' '.$this->status_message;
		header($status, true, $this->status_code);

		$this->headers->send();
		$this->cookies->send();
	}

	/**
	 * Sends the HTTP content.
	 *
	 * If the content is a resource, it streams out 8192 bytes at a time.
	 */
	public function send_content() {
		$content = $this->get_content();

		if(is_resource($content)) {
			while(!feof($content)) {
				echo fread($content, 8192);
			}
			fclose($content);
		}
		else {
			echo $content;
		}
	}

}
?>
