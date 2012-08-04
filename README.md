ImageManager
============
This is a simple tool for resizing, scaling, rendering and uploading images with PHP.  

Installation
-------------------------
The easiest way to install this tool is using composer.  Create the following composer.json file in the root of your project.

  {
      "require": {
          "centralapps/image-manager": "dev-master"
      }
  }

Then run composer to download and install the component.

  $ curl -s https://getcomposer.org/installer | php
  $ php composer.phar install
  
Usage
-------------------------

  $imageTools = new \CentralApps\ImageManager\Tools();
  $imageTools->loadFromFile('/var/www/original.png');
  $imageTools->resizeScaleHeight(150);
	$imageTools->save('/var/www/newfile.png');