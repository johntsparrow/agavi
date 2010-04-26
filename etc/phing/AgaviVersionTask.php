<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2005-2010 the Agavi Project.                                |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

class AgaviVersionTask extends Task
{
	public function main()
	{
		$agaviPath = realpath(getcwd() . '/src/agavi.php');
		
		if(!$agaviPath && !file_exists($agaviPath)) {
			throw new BuildException('Agavi not found.');
		}

		require_once($agaviPath);
		
		$this->project->setUserProperty('agavi.version', AgaviConfig::get('agavi.version'));
		$this->project->setUserProperty('agavi.pear.version', sprintf("%d.%d.%d%s", 
			AgaviConfig::get('agavi.major_version'), 
			AgaviConfig::get('agavi.minor_version'), 
			AgaviConfig::get('agavi.micro_version'), 
			AgaviConfig::has('agavi.status') ? AgaviConfig::get('agavi.status') : 'stable'
		));
		
		$status = AgaviConfig::get('agavi.status');
		
		if($status == 'dev') {
			$status = 'snapshot';
		} elseif(strpos($status, 'alpha')) {
			$status = 'alpha';
		} elseif(strpos($status, 'beta')) {
			$status = 'beta';
		} elseif(strpos($status, 'RC')) {
			$status = 'beta';
		} else {
			$status = 'stable';
		}
		
		$this->project->setUserProperty('agavi.status', $status);
		
		
		$this->project->setUserProperty('agavi.pear.filename', sprintf("agavi-%d.%d.%d%s", 
			AgaviConfig::get('agavi.major_version'), 
			AgaviConfig::get('agavi.minor_version'), 
			AgaviConfig::get('agavi.micro_version'), 
			($status == 'stable') ? '' : $status 
		));
	}
}

?>