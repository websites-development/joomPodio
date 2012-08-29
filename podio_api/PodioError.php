<?php

class PodioError extends Exception {
  public $body;
  public $status;
  public $url;
  public function __construct($body, $status, $url) {
    $this->body = json_decode($body, TRUE);
    $this->status = $status;
    $this->url = $url;
  }
}
class PodioInvalidGrantError extends PodioError {}
class PodioBadRequestError extends PodioError {}
class PodioAuthorizationError extends PodioError {}
class PodioNotFoundError extends PodioError {}
class PodioConflictError extends PodioError {}
class PodioGoneError extends PodioError {}
class PodioRateLimitError extends PodioError {}
class PodioServerError extends PodioError {}
class PodioUnavailableError extends PodioError {}

