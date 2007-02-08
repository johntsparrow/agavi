<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2007 the Agavi Project.                                |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

class AgaviPackageTask extends Task {
	private $dir = '';

	public function setDir($dir)
	{
		$this->dir = (string) $dir;
	}

	public function main()
	{
		if (!@require_once('PEAR/PackageFileManager2.php')) {
			throw new BuildException('Requires PEAR_PackageFileManager >=1.6.0a1');
		}
		require_once('PEAR/Exception.php');
		PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,'PEAR_ErrorToPEAR_Exception');
		if (!$this->dir || !file_exists($this->dir)) {
			throw new BuildException('Build dir is not defined or does not exist.');
		}

		$this->log("Building package contents in: {$this->dir}", PROJECT_MSG_INFO);

		set_time_limit(0);

		// Modify short description. Try to keep under 80 chars width
$shortDesc = <<<EOD
PHP5 MVC Application Framework
EOD;

		// Modify long description. Try to keep under 80 chars width
$longDesc = <<<EOD
Agavi is a full-featured MVC-style framework for PHP5 with a strong focus on structure, code reusability and flexibility.
EOD;

		$p2 = new PEAR_PackageFileManager2;
		$p2->setOptions(array(
			'filelistgenerator' => 'file',
			'packagedirectory' => $this->dir,
			'baseinstalldir' => 'agavi',
			'ignore' => array(
				'.svn/'
			),
			'dir_roles' => array(
				'buildtools' => 'php',
				'config' => 'php',
				'translation' => 'php',
				'samples' => 'data'
			),
			'installexceptions' => array(
				'scripts/agavi-dist' => '/',
				'scripts/agavi.bat-dist' => '/'
			),
			'exceptions' => array(
				'API_CHANGELOG' => 'doc',
				'CHANGELOG' => 'doc',
				'COPYRIGHT' => 'doc',
				'INSTALL' => 'doc',
				'KNOWN_ISSUES' => 'doc',
				'LICENSE' => 'doc',
				'LICENSE-AGAVI' => 'doc',
				'LICENSE-ICU' => 'doc',
				'LICENSE-TANGO_ICON_THEME' => 'doc',
				'LICENSE-UNICODE_CLDR' => 'doc',
				'RELEASE_NOTES' => 'doc',
				'TODO' => 'doc',
				'UPGRADING' => 'doc',
				'scripts/agavi-dist' => 'script',
				'scripts/agavi.bat-dist' => 'script',
				'build.xml' => 'php',
				'config/xsd/' => 'php'
			)
		));
		$p2->setPackageType('php');
		$p2->setPackage('agavi');
		$p2->addMaintainer('lead', 'david', 'David Zülke', 'dz@bitxtender.com');
		$p2->addMaintainer('developer', 'dominik', 'Dominik del Bondio', 'ddb@bitxtender.com');
		$p2->addMaintainer('developer', 'v-dogg', 'Veikko Mäkinen', 'mail@veikkomakinen.com');
		$p2->setChannel('pear.agavi.org');
		$p2->setReleaseVersion('0.11.0RC2');
		$p2->setAPIVersion('0.11.0RC2');
		$p2->setReleaseStability('beta');
		$p2->setAPIStability('beta');
		$p2->setSummary($shortDesc);
		$p2->setDescription($longDesc);
		$p2->setNotes("To see what's new, please refer to the RELEASE_NOTES. Also, the CHANGELOG contains a full list of changes. \n\nFor installation instructions, consult INSTALL. Information on how to migrate existing 0.10.x series applications can be found in UPGRADING.");

		// this must be the most stupid syntax I've ever seen.
		$p2->addRelease();
		$p2->setOSInstallCondition('windows');
		$p2->addInstallAs('scripts/agavi.bat-dist', 'agavi.bat');
		$p2->addIgnoreToRelease('scripts/agavi-dist');

		// and the next release... very cool, eh? how utterly stupid is that
		$p2->addRelease();
		$p2->addInstallAs('scripts/agavi-dist', 'agavi');
		$p2->addIgnoreToRelease('scripts/agavi.bat-dist');

		$p2->addPackageDepWithChannel( 'required', 'phing', 'pear.phing.info', '2.2.0');
		$p2->addPackageDepWithChannel( 'optional', 'creole', 'pear.phpdb.org', '1.1.0');
		$p2->addPackageDepWithChannel( 'optional', 'propel_generator', 'pear.phpdb.org', '1.2.0');
		$p2->addPackageDepWithChannel( 'optional', 'propel_runtime', 'pear.phpdb.org', '1.2.0');
		$p2->setPhpDep('5.1.0');
		$p2->setPearinstallerDep('1.4.0');
		$p2->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.html');
		$p2->addReplacement('scripts/agavi-dist', 'pear-config', '@PEAR-DIR@', 'php_dir');
		$p2->addReplacement('scripts/agavi.bat-dist', 'pear-config', '@PEAR-DIR@', 'php_dir');
		$p2->generateContents();


		try {
			$p2->writePackageFile();
		} catch (PEAR_Exception $e) {
			$this->log("Oops!  Caught PEAR Exception: ".$e->getMessage());
		}
	}
}

function PEAR_ErrorToPEAR_Exception($err)
{
    if ($err->getCode()) {
        throw new PEAR_Exception($err->getMessage(),
            $err->getCode());
    }
    throw new PEAR_Exception($err->getMessage());
}
?>