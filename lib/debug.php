<?php
	 
	class Debug 
	{
		protected static $startTime;
		protected static $endTime;
		protected static $page = array();
		protected static $post;
		protected static $get;
		protected static $db = array();
		protected static $session;
		protected static $custom = array();
		protected static $destroy = false;
		protected static $log = array();


		public static function start()
		{
			//error_reporting(E_ALL);
			error_reporting(0);
			//ini_set('display_errors', 0);
			self::get();
			self::post();
			self::session();
			self::page();
			self::startExecuteScript();
		}

		public static function stop()
		{
			self::endExecuteScript();
		}

		private static function get()
		{
			self::$get = isset($_GET) ? $_GET : array();
		}

		private static function post()
		{
			self::$post = isset($_POST) ? $_POST : array();
		}

		private static function session()
		{
			self::$session = isset($_SESSION) ? $_SESSION : array();
		}

		public static function db($query)
		{
			array_push(self::$db, self::formatQuery(strtoupper($query)));
		}

		public static function page($page="")
		{
			if($page == "")
			{
				$page = basename($_SERVER['PHP_SELF']);
			}

			array_push(self::$page, $page);
		}

		public static function logInfo($mensage)
		{
			array_push(self::$log, date('d/m/Y H:i:s'). ' - INFO --> '. $mensage);
		}

		private static function logError()
		{
			$error = error_get_last();
			if(!empty($error))
			{
				array_push(self::$log, date('d/m/Y H:i:s'). ' - '. self::friendlyErrorType($error['type']) .' --> '. $error['message'] .', file: '. $error['file'] .',line: '. $error['line']);	
			}
		}

		public static function custom($mensage, $array = array())
		{
			if(is_array($mensage))
			{
				array_push(self::$custom, self::formatArray($mensage));
			}
			else
			{
				if(is_array($array) && sizeof($array) > 0)
				{
					array_push(self::$custom, $mensage . ": " . self::formatArray($array));
				}
				else
				{
					array_push(self::$custom, $mensage);	
				}	
			}
		}

		public static function startExecuteScript()
		{
			self::$startTime = microtime(true);
		}

		public static function endExecuteScript()
		{
			self::$endTime = microtime(true);
		}

		public static function destroy()
		{
			self::$destroy = true;
		}

		private static function formatArray($array)
		{
			$string = "";
			foreach($array as $key=>$value)
			{
				$string .= "[". $key ."] => ". $value .", "; 
			}

			return substr($string, 0, -2);
		}

		private static function runTime()
		{
			return self::$endTime - self::$startTime ." seconds";
		}

		private static function formatQuery($query)
		{

			$wordQuery = array(
		        'SELECT', 'FROM', 'WHERE', 'SET', 'ORDER BY', 'GROUP BY', 'LIMIT', 'DROP', 'INSERT', 'INTO', 'UPDATE', 'SET', 'AS', 'ON', 'COUNT', 'MAX', 'MIN', 'LEFT OUTER JOIN', 'INNER JOIN', 'RIGHT OUTER JOIN', 'OUTER JOIN', 'JOIN', 'XOR', 'CASE',
		        'VALUES', 'UPDATE', 'HAVING', 'ADD', 'AFTER', 'ALTER TABLE', 'DELETE FROM', 'UNION ALL', 'UNION', 'EXCEPT', 'INTERSECT', 'AND' , 'IN', 'OR'
			);
			
			$query = preg_replace("%(\s". implode('\s|', $wordQuery)."\s)%iUs", '<span style="color: #909; font-weight: bold;">$1</span>', ' '.$query);
			
			return '<p style="background-color: #e6e6e6; padding: 10px; font-size: 14px; margin-bottom: 5px; border: 1px solid #cecece;">'. $query .'</p>'; 
		}

		private static function friendlyErrorType($type) 
		{ 
		    switch($type) 
		    { 
		        case 1: // 1 // 
		            return 'E_ERROR'; 
		        case 2: // 2 // 
		            return 'E_WARNING'; 
		        case 4: // 4 // 
		            return 'E_PARSE'; 
		        case 8: // 8 // 
		            return 'E_NOTICE'; 
		        case 16: // 16 // 
		            return 'E_CORE_ERROR'; 
		        case 32: // 32 // 
		            return 'E_CORE_WARNING'; 
		        case 64: // 64 // 
		            return 'E_COMPILE_ERROR'; 
		        case 128: // 128 // 
		            return 'E_COMPILE_WARNING'; 
		        case 256: // 256 // 
		            return 'E_USER_ERROR'; 
		        case 512: // 512 // 
		            return 'E_USER_WARNING'; 
		        case 1024: // 1024 // 
		            return 'E_USER_NOTICE'; 
		        case 2048: // 2048 // 
		            return 'E_STRICT'; 
		        case 4096: // 4096 // 
		            return 'E_RECOVERABLE_ERROR'; 
		        case 8192: // 8192 // 
		            return 'E_DEPRECATED'; 
		        case 16384: // 16384 // 
		            return 'E_USER_DEPRECATED'; 
		    } 
		    return ""; 
		} 

		private static function handle($data)
		{

			if(self::$destroy)
			{
				return false;
			}
			
			$content = "";
			foreach($data as $key=>$values)
			{
				if(sizeof($values) > 0)
				{
					$content .= '<fieldset style="margin-bottom: 10px; border: 1px solid #c7c7c7"><legend style="font-weight: bold;">'. $key .' <a href="javascript:void(0)" onclick="expandDebug(this, \'debug_'.$key.'\')" style="text-decoration: none; color: #000; font-weight: normal;">[ + ]</a></legend><pre id="debug_'. $key .'" style="display: none;">';

					foreach($values as $key=>$value)
					{
						if(is_int($key))
						{
							$content .= $value .'<br>';
						}
						else
						{
							$content .= $key .' = '. $value . PHP_EOL;
						}
						
					}
				}
				
				$content .= '</pre></fieldset>';
			}

			return '
				<script type="text/javascript">
					var key = 0;
					document.onkeyup = function(event){
						
						evt = event || window.event;
						if(parseInt(key) == 0 && parseInt(evt.keyCode) == 17)
						{
							key = evt.keyCode;
						}
						else
						{
							if(parseInt(key) == parseInt(evt.keyCode))
							{
								document.getElementById("btnAgtDebug").style.display = "block";
							}
							else
							{
								key = 0;
								document.getElementById("btnAgtDebug").style.display = "none";
							}
						}
					}

					function expandDebug(link, e)
					{

						var el = document.getElementById(e);
						
						if(el.style.display == "none"){
							link.innerHTML = "[ - ]";
							el.style.display = "block";

						} else {
							link.innerHTML = "[ + ]";
							el.style.display = "none";
						}
					}
				</script>
				<button id="btnAgtDebug" type="button" style="display: none;position: absolute; bottom: 20px; right: 20px; z-index: 9999; width: 56px; height: 56px; border-radius: 50%; border: 1px solid #114184; background-color: #106aea; color: #fff;" onclick="document.getElementById(\'agt-debugshow\').style.display=\'block\'">Debug</button>
				<div id="agt-debugshow" style="position: absolute; display: block; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.18); z-index: 9999999999; top: 0; left: 0;">
					<div style="width: 60%; height: 400px; background-color: #fff; margin: 150px auto; border-radius: 5px; padding: 10px; font-family: arial; font-size: 14px; box-shadow: 0 3px 9px rgba(0,0,0,.5);">
						<div style="width: 100%; height: 40px;">
							<h2>Debug</h2>
						</div>
						<div style="width: 100%; height: 300px; overflow-y: auto; overflow-x: none;">
						'.$content .'
						</div>
						<div style="text-align: center; margin: 20px;">
							<button type="button" onclick="document.getElementById(\'agt-debugshow\').style.display=\'none\'">Close</button>
						</div>
					</div>
				</div>
			';

		}

		public static function show()
		{
			self::endExecuteScript();
			self::logError();

			$data["BENCHMARK"] = array("TIME" => self::runTime());

			$data["GET"] = self::$get;
			$data["POST"] = self::$post;
			$data["SESSION"] = self::$session;
			$data["PAGES"] = self::$page;
			$data["CUSTOM"] = self::$custom;
			$data["LOG"] = self::$log;
			$data["DATA BASE"] = self::$db;

			
			print self::handle($data);
		}
	}