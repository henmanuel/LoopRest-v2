<?php

/**
 * Class Response
 */
class Response
{
	private static $readyResponse = false;

	/**
	 * Response Manager
   *
	 * @param $response
	 */
	function __construct($response)
	{
	  $this->responseContentType();
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;

    $route = $routeMD->getRoute();
    $translateRoute = GlobalSystem::translateSystemRoute();

		if($routeMD->getResponseObject()){
      $callback = $routeMD->getCallback();
      $responseObj = json_encode($response);
      $response = ($callback) ? "{$callback}({$responseObj});" : $responseObj;
    }else{

		  if($routeMD->getRoute() != GlobalSystem::ExpRouteView){
        $parentComponent = ucfirst($translateRoute);
        $dataModel = [$translateRoute => $response[$route]];
        $view = CoreConfig::PRINCIPAL_VIEW . ':' . $parentComponent;

		    if($route == GlobalSystem::ExpRouteError){
          $dataModel = [
            GlobalSystem::ExpRouteError => $parentComponent,
            GlobalSystem::ExpErrorCode => $response[$route][GlobalSystem::ExpErrorCode],
            GlobalSystem::ExpErrorDesc => $response[$route][GlobalSystem::ExpErrorDesc]
          ];
        }

        $request = [GlobalSystem::ExpRouteView => [GlobalSystem::ExpView => $view]];

        $routeMD->setRequest($request);
        $routeMD->setRoute(GlobalSystem::ExpRouteView);

        $views = new View();
        $response = $views->routingView($dataModel);
      }
    }

    $this->headersResponse();

    echo($response);
    self::$readyResponse = true;
	}

  /**
   * Setting current header response code
   */
	private function headersResponse(){
    $model = Model::getInstance();
    $routeMD = $model->getRouteInstance;
    $clientServerMD = $model->getClientServerInstance;

    $route = $routeMD->getRoute();
    $code = $routeMD->getCodeResponse();
    $protocol = $clientServerMD->getProtocol();
    $description = ($routeMD->getDescriptionResponse()) ? $routeMD->getDescriptionResponse() : '';
    $contentType = ($routeMD->getResponseObject()) ? GlobalSystem::ExpContentTypeApplicationJSON : GlobalSystem::ExpContentTypeTextHTML;

    header_remove("X-Powered-By");
    header("{$protocol} {$code} {$description}");
    header(GlobalSystem::ExpHeaderContentType . ": {$contentType}");
    if($clientServerMD->getMethod() == GlobalSystem::ExpMethodPost && $route != GlobalSystem::ExpRouteError){
      $url = $clientServerMD->getRedirectURL();
      header("Location: {$url}");
    }
  }

  /**
   * Check type accept content type client
   */
	private function responseContentType()
  {
    $model = Model::getInstance();
    $routeMD = $model->getRouteInstance;
    $clientServerMD = $model->getClientServerInstance;

    $accept = $clientServerMD->getHeader(GlobalSystem::ExpHeaderAccept);
    $contentAccepting = explode(',', $accept);

    foreach($contentAccepting as $content => $type){
      if(in_array($type, GlobalSystem::ContentTypesAllows)){
        if($type == GlobalSystem::ExpContentTypeTextHTML || $type == GlobalSystem::ExpContentTypeAll){
          $routeMD->setResponseObject(false);
          break;
        }
      }
    }
  }

	/**
	 * Get if response its ready
	 *
	 * @return bool
	 */
	public static function getReadyResponse()
	{
		return self::$readyResponse;
	}

	/**
	 * Used to complete the steps of executing model tracking
	 *
	 * @see Model
	 * @see ModelsTracking
	 * @see StepsRoutes::$executionSteps
	 */
	public function __destruct()
	{
		Log::custom('ExecuteModels', json_encode(ModelsTracking::$executionSteps, JSON_PRETTY_PRINT));
	}
}