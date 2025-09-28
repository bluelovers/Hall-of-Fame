<?php

function GlobOnlyDir($pattern) 
{
	return glob($pattern, GLOB_ONLYDIR);
}

function GlobUserList() 
{
	return GlobOnlyDir(USER."*");
}

function GlobOnlyFileDat($dir) 
{
	return glob($dir."*.dat");
}
