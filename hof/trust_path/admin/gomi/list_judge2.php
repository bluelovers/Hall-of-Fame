<?php

for ($i = 1000; $i < 9999; $i++)
{
	$j = HOF_Model_Data::getJudgeData($i);
	if ($j)
	{
		print ("case {$i}:// {$j[exp2]}<br>");
	}
}


?>