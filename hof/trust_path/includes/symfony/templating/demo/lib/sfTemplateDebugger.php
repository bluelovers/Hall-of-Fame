<?

class sfTemplateDebugger implements sfTemplateDebuggerInterface
{
	function log($message)
	{
		$file = 'error.log';
		file_put_contents($file, $message."\n---------------------------\n", FILE_APPEND);
	}
}