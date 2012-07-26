<?php
namespace CentralApps\ImageManager;

class Tools
{
	private $type = '';
	private $uploadExtentions = array( 'png', 'jpg', 'jpeg', 'gif' );
	private $uploadTypes = array( 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png' );
	private $image;
	private $name;
	
	/**
	 * Load image from local file system
	 * @param String $filepath
	 * @return void	
	 */
	public function loadFromFile( $filepath )
	{
		$info = getimagesize( $filepath );
      	$this->type = $info[2];
     	if( $this->type == IMAGETYPE_JPEG )  {
        	$this->image = imagecreatefromjpeg($filepath);
      	} elseif( $this->type == IMAGETYPE_GIF ) {
        	$this->image = imagecreatefromgif($filepath);
        } elseif( $this->type == IMAGETYPE_PNG ) {
	        $this->image = imagecreatefrompng($filepath);
      	}
	}
	
	/**
	 * Get the image width
	 * @return int
	 */
	public function getWidth() 
	{
    	return imagesx($this->image);
	}
   	
	public function getHeight() 
   	{
      return imagesy($this->image);
   	}
   	
   	public function resize( $x, $y )
   	{
	   	$new = imagecreatetruecolor($x, $y);
	   	// transparency resizing: http://webcache.googleusercontent.com/search?q=cache:hK_mvhxbCekJ:mediumexposure.com/smart-image-resizing-while-preserving-transparency-php-and-gd-library/+http://mediumexposure.com/techblog/smart-image-resizing-while-preserving-transparency-php-and-gd-library&cd=1&hl=en&ct=clnk&gl=uk&source=www.google.co.uk
	   	if( $this->type == IMAGETYPE_GIF || $this->type == IMAGETYPE_PNG ) {
	   		$transparency = imagecolortransparent( $this->image );
	   		if( $transparency >= 0 ) {
	   			$transparentColour = imagecolorsforindex( $this->image, 0 );
	   			$transparency = imagecolorallocate( $new, $transparentColour['red'], $transparentColour['green'], $transparentColour['blue'] );
	   			imagefill( $new, 0, 0, $transparency );
	   			imagecolortransparent( $new, $transparency );
	   		} elseif( $this->type == IMAGETYPE_PNG ) {
	   			imagealphablending( $new, false );
	   			$colour = imagecolorallocatealpha( $new, 0, 0, 0, 127 );
	   			imagesavealpha( $new, true );
	   		}
	   	}
      	imagecopyresampled($new, $this->image, 0, 0, 0, 0, $x, $y, $this->getWidth(), $this->getHeight());
      	$this->image = $new;
   	}
   	
   	public function resizeScaleWidth( $height )
   	{
      	$width = $this->getWidth() * ( $height / $this->getHeight() );
      	$this->resize( $width, $height );
   	}
   	
   	public function resizeScaleHeight( $width )
   	{
		$height = $this->getHeight() * ( $width / $this->getWidth() );
      	$this->resize( $width, $height );
   	}
   	
   	public function scale( $percentage )
   	{
	   	$width = $this->getWidth() * $percentage / 100;
      	$height = $this->getheight() * $percentage / 100; 
      	$this->resize( $width, $height );
   	}
   	
   	public function display()
   	{
	   	$type = '';
	   	if( $this->type == IMAGETYPE_JPEG ) {
		   	$type = 'image/jpeg';
	   	} elseif( $this->type == IMAGETYPE_GIF ) {
		   	$type = 'image/gif';
	   	} elseif( $this->type == IMAGETYPE_PNG ) {
		   	$type = 'image/png';
	   	}
	   	
	   	header('Content-Type: ' . $type );
	   	
	   	if( $this->type == IMAGETYPE_JPEG )	{
		   	imagejpeg( $this->image );
	   	} elseif( $this->type == IMAGETYPE_GIF ) {
		   	imagegif( $this->image );
	   	} elseif( $this->type == IMAGETYPE_PNG ) {
		   	imagepng( $this->image );
	   	}
   	}
	
	public function loadFromPost( $postfield, $moveto, $namePrefix='' )
	{
		if( is_uploaded_file( $_FILES[ $postfield ]['tmp_name'] ) ) {
			$i = strrpos( $_FILES[ $postfield ]['name'], '.');
	    	if (! $i ) { 
		   		return false; 
		   	} else {
			   	$l = strlen(  $_FILES[ $postfield ]['name'] ) - $i;
		        $ext = strtolower ( substr(  $_FILES[ $postfield ]['name'], $i+1, $l ) );
		        
		        if( in_array( $ext, $this->uploadExtentions ) ) {
			        if( in_array( $_FILES[ $postfield ]['type'], $this->uploadTypes ) ) {
			        	$name = str_replace( ' ', '', $_FILES[ $postfield ]['name'] );
			        	$this->name = $namePrefix . $name;
			        	$path = $moveto . $name;
			        	
			        	move_uploaded_file( $_FILES[ $postfield ]['tmp_name'] , $path );
			        	
			        	$this->loadFromFile( $path );
			        	return true;
			        } else {
						throw new \Exception("Invalid image type");
				        return false;
			        }
		        } else {
					throw new \Exception("Invalid image extension");
			        return false;
		        }
		   	}
	        
		} else {
			throw new \Exception("File trying to be modified was not uploaded");
			return false;
		}
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function save( $location, $type='', $quality=100 )
	{
		$type = ( $type == '' ) ? $this->type : $type;
		
		if( $type == IMAGETYPE_JPEG ) {
        	imagejpeg( $this->image, $location, $quality);
    	} elseif( $type == IMAGETYPE_GIF ) {
        	imagegif( $this->image, $location );         
      	} elseif( $type == IMAGETYPE_PNG ) {
        	imagepng( $this->image, $location );
        }
	}
}
