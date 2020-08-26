

$base_list = HOF_Model_Char::getBaseCharList();

			foreach ($base_list as $i)
			{
				$base = HOF_Model_Char::getBaseCharStatus($i);
				$jobdata = HOF_Model_Data::getJobData($i);

				foreach(array_keys($jobdata['gender']) as $j)
				{
					$chars[$k] = HOF_Model_Char::newBaseChar($i, array('gender' => $j));

					if ($j == GENDER_GIRL)
					{
						$Gender = '♀';
					}
					elseif ($j == GENDER_BOY)
					{
						$Gender = '♂';
					}
					else
					{
						$Gender = '';
					}

					$chars[$k]->job_name .= $Gender;

					$chars[$k]->recruit_money = $base['data_ex']['recruit_money'];

					$k++;
				}
			}