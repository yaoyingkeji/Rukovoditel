/*Required modification after update lib*/

//remove check php version
includes\libs\dompdf\1.1.1\vendor\composer\platform_check.php

//src/Options.php in funciton __construct
$this->setChroot(DIR_FS_CATALOG);
$this->setTempDir(DIR_FS_TMP);
$this->setFontDir(CFG_PATH_TO_DOMPDF_FONTS);

//src/FontMetrics.php in function loadFontFamilies
$file = $this->options->getFontDir() . "/installed-fonts.dist.json";				
if(is_file($file))
{