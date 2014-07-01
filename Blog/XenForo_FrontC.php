<?php

/**
* Class to manage most of the flow of a request to a XenForo page.
*
* This class: resolves a URL to a route, loads a specified controller, executes an action
* in that controller, loads the view, renders the view, and outputs the response.
*
* Most dependent objects can be injected
*
* @package XenForo_Mvc
*/
class XenForo_FrontC
{
	/**
	* An object that is able to load the dependencies needed to use this front controller.
	*
	* @var XenForo_Dependencies_Abstract
	*/
	protected $_dependencies;

	/**
	* Request object.
	*
	* @see setRequest()
	* @var Zend_Controller_Request_Http
	*/
	protected $_request;

	/**
	* Response object.
	*
	* @see setResponse()
	* @var Zend_Controller_Response_Http
	*/
	protected $_response;

	/**
	* Controls whether calling {@link run()} prints the response via the {@link $_response} object.
	* If set to false, the response is returned instead.
	*
	* @see setSendResponse()
	* @var boolean
	*/
	protected $_sendResponse = true;

	/**
	* Constructor. Sets up dependencies.
	*
	* @param XenForo_Dependencies_Abstract
	*/
	public function __construct(XenForo_Dependencies_Abstract $dependencies)
	{
		$this->_dependencies = $dependencies;
	}

	/**
	* Setter for {@link $_request}.
	*
	* @param Zend_Controller_Request_Http
	*/
	public function setRequest(Zend_Controller_Request_Http $request)
	{
		$this->_request = $request;
	}

	/**
	* Setter for {@link $_response}.
	*
	* @param Zend_Controller_Response_Http
	*/
	public function setResponse(Zend_Controller_Response_Http $response)
	{
		$this->_response = $response;
	}

	/**
	 * @return Zend_Controller_Request_Http
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * @return Zend_Controller_Response_Http
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * @return XenForo_Dependencies_Abstract
	 */
	public function getDependencies()
	{
		return $this->_dependencies;
	}

	/**
	* Setter for {@link $_sendResponse}.
	*
	* @param boolean
	*/
	public function setSendResponse($sendResponse)
	{
		$this->_sendResponse = (bool)$sendResponse;
	}

	/**
	* Runs the request, handling from routing straight through to response output.
	* Primary method to be used by the external API.
	*
	* @return string|null Returns a string if {@link $_sendResponse} is false
	*/
	public function run($innerContent = "", $newParams = array())
	{
		ob_start();

		$this->setup();
		$this->setRequestPaths();
		$showDebugOutput = $this->showDebugOutput();

		$this->_dependencies->preLoadData();

		XenForo_CodeEvent::fire('front_controller_pre_route', array($this));
		$routeMatch = $this->route();
		
		XenForo_CodeEvent::fire('front_controller_pre_dispatch', array($this, &$routeMatch));

		$controllerResponse = $this->dispatch($routeMatch);
		
		if (!$controllerResponse)
		{
			XenForo_Error::noControllerResponse($routeMatch, $this->_request);
			exit;
		}

		$viewRenderer = $this->_getViewRenderer($routeMatch->getResponseType());
		if (!$viewRenderer)
		{
			// note: should only happen if there's an error getting the default renderer, which should never happen :)
			XenForo_Error::noViewRenderer($this->_request);
			exit;
		}

		$containerParams = array(
			'majorSection' => $routeMatch->getMajorSection(),
			'minorSection' => $routeMatch->getMinorSection()
		);
		
		XenForo_CodeEvent::fire('front_controller_pre_view',
			array($this, &$controllerResponse, &$viewRenderer, &$containerParams)
		);
		
		$content = $this->renderView($controllerResponse, $viewRenderer, $containerParams, $innerContent, $newParams);
		
		if ($showDebugOutput)
		{
			$content = $this->renderDebugOutput($content);
		}
	
		$bufferedContents = ob_get_contents();
		ob_end_clean();
		if ($bufferedContents !== '')
		{
			$content = $bufferedContents . $content;
		}
		
		
		XenForo_CodeEvent::fire('front_controller_post_view', array($this, &$content));

		if ($this->_sendResponse)
		{
			$headers = $this->_response->getHeaders();
			$isText = false;
			foreach ($headers AS $header)
			{
				if ($header['name'] == 'Content-Type')
				{
					if (strpos($header['value'], 'text/') === 0)
					{
						$isText = true;
					}
					break;
				}
			}
			if ($isText && is_string($content) && $content)
			{
				$extraHeaders = XenForo_Application::gzipContentIfSupported($content);
				foreach ($extraHeaders AS $extraHeader)
				{
					$this->_response->setHeader($extraHeader[0], $extraHeader[1], $extraHeader[2]);
				}
			}

			if (is_string($content) && $content && !ob_get_level() && XenForo_Application::get('config')->enableContentLength)
			{
				$this->_response->setHeader('Content-Length', strlen($content), true);
			}

			$this->_response->sendHeaders();
			
			if ($content instanceof XenForo_FileOutput)
			{
				$content->output();
			}
			else
			{
				//$uncompressed = gzuncompress($content);
				//echo $uncompressed;
				echo $content;
			}
		}
		else
		{
			return $content;
		}
	}

	/**
	* Sets up the default version of objects needed to run.
	*/
	public function setup()
	{
		if (!$this->_request)
		{
			$this->_request = new Zend_Controller_Request_Http();
		}

		if (!$this->_response)
		{
			$this->_response = new Zend_Controller_Response_Http();
		}
	}

	/**
	 * Sets the request paths for this request.
	 */
	public function setRequestPaths()
	{
		$requestPaths = XenForo_Application::getRequestPaths($this->_request);
		XenForo_Application::set('requestPaths', $requestPaths);
	}

	/**
	 * Determines if the full debug output show be shown. This usually depends
	 * on application configuration and a request param.
	 *
	 * @return boolean
	 */
	public function showDebugOutput()
	{
		return ($this->_request->getParam('_debug') && XenForo_Application::debugMode());
	}

	/**
	* Sends the request to the router for routing.
	*
	* @return XenForo_RouteMatch
	*/
	public function route()
	{
		$return = $this->_dependencies->route($this->_request);
		if (!$return || !$return->getControllerName())
		{
			list($controllerName, $action) = $this->_dependencies->getNotFoundErrorRoute();
			$return->setControllerName($controllerName);
			$return->setAction($action);
		}

		return $return;
	}

	/**
	* Executes the controller dispatch loop.
	*
	* @param XenForo_RouteMatch $routeMatch
	*
	* @return XenForo_ControllerResponse_Abstract|null Null will only occur if error handling is broken
	*/
	public function dispatch(XenForo_RouteMatch $routeMatch)
	{
		$reroute = array(
			'controllerName' => $routeMatch->getControllerName(),
			'action' => $routeMatch->getAction()
		);
		
		$allowReroute = true;

		do
		{
			$controllerResponse = null;
			$controllerName = $reroute['controllerName'];
			
			$action = str_replace(array('-', '/'), ' ', strtolower($reroute['action']));
			$action = str_replace(' ', '', ucwords($action));
			if ($action === '')
			{
				$action = 'Index';
			}

			$reroute = false;

			$controller = $this->_getValidatedController($controllerName, $action, $routeMatch);
			if ($controller)
			{
				try
				{
					try
					{
						$controller->preDispatch($action);
						$controllerResponse = $controller->{'action' . $action}();
					}
					catch (XenForo_ControllerResponse_Exception $e)
					{
						$controllerResponse = $e->getControllerResponse();
					}

					$controller->postDispatch($controllerResponse, $controllerName, $action);
					$reroute = $this->_handleControllerResponse($controllerResponse, $controllerName, $action);
				}
				catch (Exception $e)
				{
					// this is a bit hacky, but it's a selective catch so it's a strange case
					if ($e instanceof XenForo_Exception && $e->isUserPrintable())
					{
						$controllerResponse = $this->_getErrorResponseFromException($e);
						$controller->postDispatch($controllerResponse, $controllerName, $action);
					}
					else
					{
						if (!$allowReroute)
						{
							break;
						}

						$reroute = $this->_rerouteServerError($e);
						$allowReroute = false;

						XenForo_Error::logException($e);
					}
				}

				$responseType = $controller->getResponseType();
				$this->_dependencies->mergeViewStateChanges($controller->getViewStateChanges());
			}
			else
			{
				if (!$allowReroute)
				{
					break;
				}

				$reroute = $this->_rerouteNotFound($controllerName, $action);
				$allowReroute = false;
			}
		}
		while ($reroute);

		if ($controllerResponse instanceof XenForo_ControllerResponse_Abstract)
		{
			$controllerResponse->controllerName = $controllerName;
			$controllerResponse->controllerAction = $action;
		}

		return $controllerResponse;
	}

	/**
	* Called when a printable exception occurs, to get the controller response object.
	*
	* @param Exception Exception that occurred
	*
	* @return XenForo_ControllerResponse_Abstract
	*/
	protected function _getErrorResponseFromException(Exception $e)
	{
		if (method_exists($e, 'getMessages'))
		{
			$message = $e->getMessages();
		}
		else
		{
			$message = $e->getMessage();
		}

		$controllerResponse = new XenForo_ControllerResponse_Error();
		$controllerResponse->errorText = $message;

		return $controllerResponse;
	}

	/**
	* Loads the controller only if it and the specified action have been validated as callable.
	*
	* @param string Name of the controller to load
	* @param string Name of the action to run
	* @param XenForo_RouteMatch Route match for this request (may not match controller)
	*
	* @return XenForo_Controller|null
	*/
	protected function _getValidatedController($controllerName, $action, XenForo_RouteMatch $routeMatch)
	{
		$controllerName = XenForo_Application::resolveDynamicClass($controllerName, 'controller');
		if ($controllerName)
		{
			$controller = new $controllerName($this->_request, $this->_response, $routeMatch);
			if (method_exists($controller, 'action' . $action) && $this->_dependencies->allowControllerDispatch($controller, $action))
			{
				return $controller;
			}
		}

		return null;
	}

	/**
	* Handles a controller response to determine if something failed or a reroute is needed.
	*
	* @param mixed  Exceptions will be thrown if is not {@link XenForo_ControllerResponse_Abstract}
	* @param string Name of the controller that generated this response
	* @param string Name of the action that generated this response
	*
	* @return false|array False if no reroute is needed. Array with keys controllerName and action if needed.
	*/
	protected function _handleControllerResponse($controllerResponse, $controllerName, $action)
	{
		if (!$controllerResponse)
		{
			throw new XenForo_Exception("No controller response from $controllerName::action$action");
		}
		else if (!($controllerResponse instanceof XenForo_ControllerResponse_Abstract))
		{
			throw new XenForo_Exception("Invalid controller response from $controllerName::action$action");
		}
		else if ($controllerResponse instanceof XenForo_ControllerResponse_Reroute)
		{
			if ($controllerResponse->controllerName == $controllerName && strtolower($controllerResponse->action) == strtolower($action))
			{
				throw new XenForo_Exception("Cannot reroute controller to itself ($controllerName::action$action)");
			}

			return array(
				'controllerName' => $controllerResponse->controllerName,
				'action' => $controllerResponse->action
			);
		}

		return false;
	}

	/**
	* Returns information about how to reroute if a server error occurs.
	*
	* @param Exception Exception object that triggered the error
	*
	* @return array Reroute array
	*/
	protected function _rerouteServerError(Exception $e)
	{
		$this->_request->setParam('_exception', $e);

		list($controllerName, $action) = $this->_dependencies->getServerErrorRoute();
		return array(
			'controllerName' => $controllerName,
			'action' => $action
		);
	}

	/**
	* Returns information about how to reroute if a page not found error occurs.
	*
	* @param string Controller name
	* @param string Action
	*
	* @return array Reroute array
	*/
	protected function _rerouteNotFound($controllerName, $action)
	{
		$this->_request->setParams(array(
			'_controllerName' => $controllerName,
			'_action' => $action
		));

		list($controllerName, $action) = $this->_dependencies->getNotFoundErrorRoute();
		return array(
			'controllerName' => $controllerName,
			'action' => $action
		);
	}

	/**
	* Gets the view renderer for the specified response type.
	*
	* @param string Response type (eg, html, xml, json)
	*
	* @return XenForo_ViewRenderer_Abstract
	*/
	protected function _getViewRenderer($responseType)
	{
		return new XenForo_ViewRenderer_HtmlPublicC($this->_dependencies,$this->_response, $this->_request);
		//return $this->_dependencies->getViewRenderer($this->_response, $responseType, $this->_request);
	}

	/**
	* Renders the view.
	*
	* @param XenForo_ControllerResponse_Abstract Controller response object from last controller
	* @param XenForo_ViewRenderer_Abstract       View renderer for specified response type
	* @param array                            Extra container params (probably "sections" from routing)
	*
	* @return string View output
	*/
	public function renderView(XenForo_ControllerResponse_Abstract $controllerResponse, XenForo_ViewRenderer_Abstract $viewRenderer, array $containerParams = array(), $innerContent = "", $newParams = array())
	{
		$this->_dependencies->preRenderView($controllerResponse);
		$this->_response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
		if ($controllerResponse->responseCode)
		{
			$this->_response->setHttpResponseCode($controllerResponse->responseCode);
		}
		/*
		if ($controllerResponse instanceof XenForo_ControllerResponse_Error)
		{
			$innerContent = $viewRenderer->renderError($controllerResponse->errorText);
		}
		else if ($controllerResponse instanceof XenForo_ControllerResponse_Message)
		{
			$innerContent = $viewRenderer->renderMessage($controllerResponse->message);
		}
		else if ($controllerResponse instanceof XenForo_ControllerResponse_View)
		{
			$innerContent = $viewRenderer->renderView(
				$controllerResponse->viewName, $controllerResponse->params, $controllerResponse->templateName,
				$controllerResponse->subView
			);
		}
		else if ($controllerResponse instanceof XenForo_ControllerResponse_Redirect)
		{
			$target = XenForo_Link::convertUriToAbsoluteUri($controllerResponse->redirectTarget);

			$innerContent = $viewRenderer->renderRedirect(
				$controllerResponse->redirectType,
				$target,
				$controllerResponse->redirectMessage,
				$controllerResponse->redirectParams
			);
		}
		else
		{
			// generally shouldn't happen
			$innerContent = false;
		}

		if ($innerContent === false || $innerContent === null)
		{
			$innerContent = $viewRenderer->renderUnrepresentable();
		}
		*/
		
		if ($viewRenderer->getNeedsContainer())
		{
			$specificContainerParams = XenForo_Application::mapMerge(
				$containerParams,
				$controllerResponse->containerParams
			);
			$containerParams = $this->_dependencies->getEffectiveContainerParams($specificContainerParams, $this->_request);
			
			return $viewRenderer->renderContainer($innerContent, $containerParams, $newParams);
		}
		else
		{
			return $innerContent;
		}
	}

	/**
	 * Renders page-level debugging output and replaces the original view content
	 * with it. Alternatively, it could inject itself into the view content.
	 *
	 * @param string $originalContent Original, rendered view content
	 *
	 * @return string Replacement rendered view content
	 */
	public function renderDebugOutput($originalContent)
	{
		$this->_response->clearHeaders();
		$this->_response->setHttpResponseCode(200);
		$this->_response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
		$this->_response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);

		return XenForo_Debug::getDebugHtml();
	}
}
?>