<?php

class Routes
{
  /**
   * Get routes or a specific route
   *
   * @param string|null $routeId
   * @return bool|mixed
   */
	public function route(string $routeId = null)
	{
		$route = Cache::getDocument(CoreConfig::CACHE_TRANSLATE_ROUTES);

		if($routeId){
			if(key_exists($routeId, $route)){
				return $route[$routeId];
			}

			return false;
		}

		return $route;
	}

  /**
   * Generate new route
   *
   * @param string $name
   * @param string|null $method
   * @param string|null $action
   * @return bool
   * @throws Exception
   */
	public function postRoute(string $name, string $method = null, string $action = null)
	{
    Input::validate($name, GlobalSystem::ExpFormatChar);

    $routes = Cache::getDocument(CoreConfig::CACHE_TRANSLATE_ROUTES);
    if(!key_exists($name, $routes)){
      $route = [
        $name => [
          GlobalSystem::ExpTranslatePublicRoute => false,
          GlobalSystem::ExpTranslateParamsMethodWithRoutes => true,
          GlobalSystem::ExpTranslateRouteType => GlobalSystem::ExpRouteRequest
        ]
      ];

      if($routes){
        $routes = array_merge($routes, $route);
      }
    }

    if($method){
      Input::validate($method, GlobalSystem::ExpFormatChar);
      Input::validate($action, GlobalSystem::ExpFormatChar);

      if(!key_exists($action, $routes[$name][GlobalSystem::ExpTranslateMethodsRoute][$method])){
        $model = Model::getInstance();
        $routeMD = $model->getRouteInstance;

        $body = $routeMD->getBody();
        if(strtoupper($action) == GlobalSystem::ExpMethodGet && $body){
          $errorMessage = "Method GET not need body input structure, please remove this";
          ErrorManager::errorMessage($errorMessage, ErrorCodes::HttpParamsExc);
        }

        $route = ($routes) ? $routes : $route;
        $route[$name][GlobalSystem::ExpTranslateMethodsRoute][$method][$action] = json_decode($body, true);
      }
    }

    return Cache::loadDocument(CoreConfig::CACHE_TRANSLATE_ROUTES, $route, false);
	}

  /**
   * Update current registry
   *
   * @param string $name
   * @param string|null $method
   * @return bool
   * @throws Exception
   */
	public function putRoute(string $name, string $method = null)
  {
    Input::validate($name, GlobalSystem::ExpFormatChar);

    $model = Model::getInstance();
    $routeMD = $model->getRouteInstance;
    $routes = Cache::getDocument(CoreConfig::CACHE_TRANSLATE_ROUTES);

    if(key_exists($name, $routes)){
      $body = $routeMD->getBody();
      $routeConfig = GlobalSystem::routeConfig($name);

      if($method){
        Input::validate($method, GlobalSystem::ExpFormatChar);
        if(key_exists($method, $routes[$name][GlobalSystem::ExpTranslateMethodsRoute])){
          $bodyFormat = $routeConfig[GlobalSystem::ExpTranslateMethodsRoute][$method][strtolower(GlobalSystem::ExpMethodPost)];

          GlobalSystem::validateFormatFieldsBodyActionMethod($body, $bodyFormat);
          $newBody = array_merge($routes[$name][GlobalSystem::ExpTranslateMethodsRoute], json_decode($body, true));
          $routes[$name][GlobalSystem::ExpTranslateMethodsRoute][$method][strtolower(GlobalSystem::ExpMethodPost)] = $newBody;
        }
      }

      return Cache::loadDocument(CoreConfig::CACHE_TRANSLATE_ROUTES, $routes, false);
    }

    return false;
  }
}