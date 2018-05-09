<?php

/**
 * Class Manager
 *
 * @author   Mario Henmanuel Vargas Ugalde <hemma.hvu@gmail.com>
 */
class Manager extends Auth
{
	/**
	 * Format response to response
	 */
	public function response()
	{
		$result = $this->route();
		new Response($result);
	}

	/**
	 * @return array|mixed|string
	 */
	protected function route()
	{
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;

		try {
			switch($routeMD->getTrigger()){
				case GlobalSystem::ExpAuthTrigger:
					return $this->auth();
				case GlobalSystem::ExpErrorTrigger:
					return $this->error();
				case GlobalSystem::ExpViewsTrigger:
					return $this->views();
				case GlobalSystem::ExpRequestTrigger:
					return $this->request();
				default: ErrorManager::throwException(ErrorCodes::ActionExc);
			}
		}catch(Exception $error){
			ErrorManager::onErrorRoute($error);
			return $this->error();
		}
	}
	
	/**
	 * @return array|bool|mixed
	 * @throws Exception
	 */
	private function request()
	{
		$availableAccess = self::checkClient();

		if($availableAccess === true){
			$model = Model::getInstance();
			$action = $this->requestAction();
			$routeMD = $model->getRouteInstance;
			$request = $routeMD->getRequest();
			$token = $routeMD->getRequest(GlobalSystem::ExpRequestToken);

			if($action){
				return $action;
			}

			return self::getData($token, $request);
		}

		$errorCode = ErrorCodes::AccessExc;
		$errorCode[GlobalSystem::ExpErrorDesc] = $availableAccess;

		ErrorManager::throwException($errorCode);
	}

	/**
	 * Active action in request and execute this
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	private function requestAction()
	{
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;

		switch($routeMD->getAction()){
			case GlobalSystem::ExpMethodPost:
				return $this->post();
			case GlobalSystem::ExpMethodPut:
				return $this->update();
			case GlobalSystem::ExpMethodDelete:
				return $this->delete();
			default: return false;
		}
	}

	private function post()
	{
	
	}

	private function update()
	{
	
	}

	private function delete()
	{
	
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private function auth()
	{
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;
		$authorization = $routeMD->getAuthorization();

		$availableAccess = self::checkClient();
		if($availableAccess){
			$routeMD->setResponseObject(false);
			$userEmail = $routeMD->getRequest(GlobalSystem::ExpAuthEmail);
			$userAccess = self::checkUserAccess($userEmail);

			$db = new AccessDB();
			$isUser = $db->getUser($userEmail);

			if(!$isUser){
				$db->newUser($routeMD->getRequest());
			}

			$tokenData = [
				'access' => $userAccess,
				'id' => $routeMD->getRequest(RequestRoute::ExpAuthId),
				'name' => $routeMD->getRequest(RequestRoute::ExpAuthName)
			];

			return self::signIn($tokenData);
		}

		$errorCode = ErrorCodes::AccessExc;
		$errorCode[GlobalSystem::ExpErrorDesc] = $availableAccess;

		ErrorManager::throwException($errorCode);
	}

	/**
	 * Error routing response to print custom message during execution
	 *
	 * @return mixed
	 */
	private function error()
	{
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;

		$routeMD->setResponseObject(true);
		$errorRequest = $routeMD->getRequest();
		$errorCode = $errorRequest[GlobalSystem::ExpErrorCode];
		$errorDesc = $errorRequest[GlobalSystem::ExpErrorDesc];

		if(key_exists($errorDesc, ExecutionStep::$errorCodesSteps)){
			if(ExecutionStep::$errorCodesSteps[$errorDesc] = $errorCode){
				$request = [
					GlobalSystem::ExpRouteViews => [
						GlobalSystem::ExpViews => 'Test'
					]
				];

				$routeMD->setRequest($request);
				$routeMD->setRoute(GlobalSystem::ExpRouteViews);

				return $this->views();
			}
		}

		return [$routeMD->getRoute() => $errorRequest];
	}

	/**
	 * Return any html view rendering component
	 *
	 * @return string
	 */
	private function views()
	{
		$model = Model::getInstance();
		$routeMD = $model->getRouteInstance;
		$routeMD->setResponseObject(false);
		$views = new Views($routeMD->getRequest(GlobalSystem::ExpViews));

		return $views->routingView();
	}
}