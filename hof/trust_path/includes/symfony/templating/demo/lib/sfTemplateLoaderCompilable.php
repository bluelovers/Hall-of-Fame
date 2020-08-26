<?

class sfTemplateLoaderCompilable extends sfTemplateLoaderFilesystem implements sfTemplateLoaderCompilableInterface
{

	public function compile($template)
	{
		return file_get_contents((string)$template);
	}

}