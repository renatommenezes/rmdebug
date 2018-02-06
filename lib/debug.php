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



		public function start()
		{
			self::get();
			self::post();
			self::session();
			self::page();
			self::startExecuteScript();
		}

		public function stop()
		{
			self::endExecuteScript();
		}

		private function get()
		{
			self::$get = isset($_GET) ? $_GET : array();
		}

		private function post()
		{
			self::$post = isset($_POST) ? $_POST : array();
		}

		private function session()
		{
			self::$session = isset($_SESSION) ? $_SESSION : array();
		}

		public function db($query)
		{
			array_push(self::$db, self::formatQuery(strtoupper($query)));
		}

		public function page($page="")
		{
			if($page == "")
			{
				$page = basename($_SERVER['PHP_SELF']);
			}

			array_push(self::$page, $page);
		}

		public function custom($mensage, $array = array())
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

		public function startExecuteScript()
		{
			self::$startTime = microtime(true);
		}

		public function endExecuteScript()
		{
			self::$endTime = microtime(true);
		}

		public function destroy()
		{
			self::$destroy = true;
		}

		private function formatArray($array)
		{
			$string = "";
			foreach($array as $key=>$value)
			{
				$string .= "[". $key ."] => ". $value .", "; 
			}

			return substr($string, 0, -2);
		}

		private function runTime()
		{
			return self::$endTime - self::$startTime ." seconds";
		}

		private function formatQuery($query)
		{

			$wordQuery = array(
		        'SELECT', 'FROM', 'WHERE', 'SET', 'ORDER BY', 'GROUP BY', 'LIMIT', 'DROP', 'INSERT', 'INTO', 'UPDATE', 'SET', 'AS', 'ON', 'COUNT', 'MAX', 'MIN', 'LEFT OUTER JOIN', 'INNER JOIN', 'RIGHT OUTER JOIN', 'OUTER JOIN', 'JOIN', 'XOR', 'CASE',
		        'VALUES', 'UPDATE', 'HAVING', 'ADD', 'AFTER', 'ALTER TABLE', 'DELETE FROM', 'UNION ALL', 'UNION', 'EXCEPT', 'INTERSECT', 'AND' , 'IN', 'OR'
			);
			
			$query = preg_replace("%(\s". implode('\s|', $wordQuery)."\s)%iUs", '<span style="color: #909; font-weight: bold;">$1</span>', ' '.$query);
			
			return '<p style="background-color: #e6e6e6; padding: 10px; font-size: 14px; margin-bottom: 5px; border: 1px solid #cecece;">'. $query .'</p>'; 
		}

		private function handle($data)
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
					$content .= '<fieldset style="margin-bottom: 10px; border: 1px solid #c7c7c7"><legend style="font-weight: bold;">'. $key .'</legend><pre>';

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
						console.log("antes key: "+ key +", event: "+ evt.keyCode);
						if(parseInt(key) == 0 && parseInt(evt.keyCode) == 17)
						{
							console.log("entrou");
							key = evt.keyCode;
						}
						else
						{
							console.log("duarnte key: "+ key +", event: "+ evt.keyCode);
							console.log(key==evt.keyCode);
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
						console.log("depois key: "+ key +", event: "+ evt.keyCode);
					}
				</script>
				<button id="btnAgtDebug" type="button" style="display: none;position: absolute; bottom: 20px; right: 20px; z-index: 9999; width: 56px; height: 56px; border-radius: 50%; border: 1px solid #114184; background-color: #106aea; color: #fff;" onclick="document.getElementById(\'agt-debugshow\').style.display=\'block\'">Debug</button>
				<div id="agt-debugshow" style="position: absolute; display: none; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.18); z-index: 9999999999; top: 0; left: 0;">
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

		public function show()
		{
			self::endExecuteScript();

			$data["BENCHMARK"] = array("TIME" => self::runTime());

			$data["GET"] = self::$get;
			$data["POST"] = self::$post;
			$data["SESSION"] = self::$session;
			$data["PAGES"] = self::$page;
			$data["CUSTOM"] = self::$custom;
			$data["DATA BASE"] = self::$db;
			
			print self::handle($data);
		}
	}