/**
	 * Gets a list of the actions that can be performed.
	 *
	 * categoryID falls es später noch weitere einschränkungen geben soll
	 * 
	 * @return	JObject
	 *
	 */
	 
use Joomla\CMS\Factory;

	public static function getActions($categoryId = 0)
	{
		$user	= Factory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_clm';
			$level = 'component';
				}

		$actions = JAccess::getActions('com_clm', $level);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}