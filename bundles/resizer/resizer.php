<?php
	
/**
 * Provides a very simple way to resize an image.
 *
 * Credits to Jarrod Oberto.
 * Jarrod wrote a tutorial on NetTuts.
 * http://net.tutsplus.com/tutorials/php/image-resizing-made-easy-with-php/
 *
 * I only turned it into a Laravel bundle.
 *
 * @package Resizer
 * @version 1.1
 * @author Maikel D (original author Jarrod Oberto)
 * @link
 * @example
 * 		Resizer::open( mixed $file )
 *			->resize( int $width , int $height , string 'exact, portrait, landscape, auto or crop' )
 *			->save( string 'path/to/file.jpg' , int $quality );
 *
 *		// Resize and save an image.
 * 		Resizer::open( Input::file('field_name') )
 *			->resize( 800 , 600 , 'crop' )
 *			->save( 'path/to/file.jpg' , 100 );
 *
 *		// Recompress an image.
 *		Resizer::open( 'path/to/image.jpg' )
 *			->save( 'path/to/new_image.jpg' , 60 );
 */
class Resizer {
	
	/**
	 * Store the image resource which we'll modify.
	 * @var Resource
	 */
	private $image;
	
	/**
	 * Original width of the image we're modifying.
	 * @var int
	 */
	private $width;
	
	/**
	 * Original height of the image we're modifying.
	 * @var int
	 */
	private $height;
	
	/**
	 * Store the resource of the resized image.
	 * @var Resource
	 */
	private $image_resized;

	const FINAL_IMAGE_TYPE = "png";
	const PNG = "png";
	
	/**
	 * Instantiates the Resizer and receives the path to an image we're working with.
	 * @param mixed $file The file array provided by Laravel's Input::file('field_name') or a path to a file
	 */
	function __construct( $file )
	{
		// Open up the file.
		$this->image = $this->open_image( $file );
		
		if ( !$this->image ) {
			throw new Exception('File not recognised. Possibly because the path is wrong. Keep in mind, paths are relative to the main index.php file.');
		}
		
		// Get width and height of our image.
		$this->width  = imagesx( $this->image );
		$this->height = imagesy( $this->image );
	}
	
	/**
	 * Static call, Laravel style.
	 * Returns a new Resizer object, allowing for chainable calls.
	 * @param  mixed $file The file array provided by Laravel's Input::file('field_name') or a path to a file
	 * @return Resizer
	 */
	public static function open( $file )
	{
		return new Resizer( $file );
	}
	
	/**
	 * Resizes and/or crops an image.
	 * @param  int    $new_width  The width of the image
	 * @param  int    $new_height The height of the image
	 * @param  string $option     Either exact, portrait, landscape, auto or crop.
	 * @return [type]
	 */
	public function resize( $new_width , $new_height , $option = 'auto' )
	{
		// Get optimal width and height - based on $option.
		$option_array = $this->get_dimensions( $new_width , $new_height , $option );
		
		$optimal_width	= $option_array['optimal_width'];
		$optimal_height	= $option_array['optimal_height'];
		
		// Resample - create image canvas of x, y size.
		$this->image_resized = imagecreatetruecolor( $optimal_width , $optimal_height );

		if (self::FINAL_IMAGE_TYPE == self::PNG)
		{
		    // Retain transparency for PNG and GIF files.
		    imagecolortransparent( $this->image_resized , imagecolorallocatealpha( $this->image_resized , 255 , 255 , 255 , 127 ) );
		    imagealphablending( $this->image_resized , false );
		    imagesavealpha( $this->image_resized , true );
		    imagecopyresampled( $this->image_resized , $this->image , 0 , 0 , 0 , 0 , $optimal_width ,$optimal_height , $this->width , $this->height );
		} else {
		    // Resample - create image canvas of x, y size.
		    $this->image_background = imagecreatetruecolor( $this->width , $this->height );
		    // Retain transparency for PNG and GIF files.
		    $white = imagecolorallocate($this->image_background,  255, 255, 255);
		    imagefilledrectangle($this->image_background, 0, 0,  $this->width , $this->height, $white);
		    imagecopy($this->image_background, $this->image, 0, 0, 0, 0,  $this->width , $this->height);
		    // Create the new image.
		    imagecopyresampled( $this->image_resized , $this->image_background , 0 , 0 , 0 , 0 , $optimal_width ,$optimal_height , $this->width , $this->height );
		}

		/*
		$image_background = imagecreatetruecolor( $this->width , $this->height );
		
		// Retain transparency for PNG and GIF files.
		$background_colour = imagecolorallocate(
			$image_background,
			Config::get('resizer::defaults.background_color.r'),
			Config::get('resizer::defaults.background_color.g'),
			Config::get('resizer::defaults.background_color.b')
		);
		
		imagefilledrectangle( $image_background , 0 , 0 , $this->width , $this->height , $background_colour );
		imagecopy( $image_background , $this->image , 0 , 0 , 0 , 0 , $this->width , $this->height );
		// imagecolortransparent( $this->image_resized , imagecolorallocatealpha( $this->image_resized , 255 , 255 , 255 , 127 ) );
		// imagealphablending( $this->image_resized , false );
		// imagesavealpha( $this->image_resized , true );
		
		// convert transparency to white when converting from PNG to JPG.
		// PNG to PNG should retain transparency as per normal.
		// imagefill( $this->image_resized , 0 , 0 , IMG_COLOR_TRANSPARENT );
		
		// Create the new image.
		imagecopyresampled( $this->image_resized , $image_background , 0 , 0 , 0 , 0 , $optimal_width , $optimal_height , $this->width , $this->height );
		*/

		// if option is 'crop' or 'fit', then crop too.
		if ( $option == 'crop' || $option == 'fit' ) {
			$this->crop( $optimal_width , $optimal_height , $new_width , $new_height );
		}
		
		// Return $this to allow calls to be chained.
		return $this;
	}
	
	/**
	 * Save the image based on its file type.
	 * @param  string $save_path     Where to save the image
	 * @param  int    $image_quality The output quality of the image
	 * @return boolean
	 */
	public function save( $save_path , $image_quality = 95 )
	{
		// If the image wasn't resized, fetch original image.
		if ( !$this->image_resized ) {
			$this->image_resized = $this->image;
		}
		
		// Get extension of the output file.
		$extension = strtolower( File::extension($save_path) );
		
		// Create and save an image based on it's extension.
		switch( $extension )
		{
			case 'jpg':
			case 'jpeg':
				if ( imagetypes() & IMG_JPG ) {
					imagejpeg( $this->image_resized , $save_path , $image_quality );
				}
				break;
				
			case 'gif':
				if ( imagetypes() & IMG_GIF ) {
					imagegif( $this->image_resized , $save_path );
				}
				break;
				
			case 'png':
				// Scale quality from 0-100 to 0-9.
				$scale_quality = round( ($image_quality/100) * 9 );
				
				// Invert quality setting as 0 is best, not 9.
				$invert_scale_quality = 9 - $scale_quality;
				
				if ( imagetypes() & IMG_PNG ) {
					imagepng( $this->image_resized , $save_path , $invert_scale_quality );
				}
				break;
				
			default:
				return false;
				break;
		}
		
		// Remove the resource for the resized image.
		imagedestroy( $this->image_resized );
		
		return true;
	}
	
	/**
	 * Open a file, detect its mime-type and create an image resrource from it.
	 * @param  array $file Attributes of file from the $_FILES array
	 * @return mixed
	 */
	private function open_image( $file )
	{
		// If $file isn't an array, we'll turn it into one.
		if ( !is_array($file) ) {
			$file = array(
				'type'		=> File::mime( strtolower(File::extension($file)) ),
				'tmp_name'	=> $file
			);
		}
		
		$mime = $file['type'];
		$file_path = $file['tmp_name'];

		switch ( $mime )
		{
			case 'image/pjpeg': // IE6
			case File::mime('jpg'):	$img = @imagecreatefromjpeg( $file_path );	break;
			case File::mime('gif'):	$img = @imagecreatefromgif( $file_path );	break;
			case File::mime('png'):	$img = @imagecreatefrompng( $file_path );	break;
			default:				$img = false;								break;
		}
		
		return $img;
	}
	
	/**
	 * Return the image dimentions based on the option that was chosen.
	 * @param  int    $new_width  The width of the image
	 * @param  int    $new_height The height of the image
	 * @param  string $option     Either exact, portrait, landscape, auto or crop.
	 * @return array
	 */
	private function get_dimensions( $new_width , $new_height , $option )
	{
		switch ( $option )
		{
			case 'exact':
				$optimal_width	= $new_width;
				$optimal_height	= $new_height;
				break;
			case 'portrait':
				$optimal_width	= $this->get_size_by_fixed_height( $new_height );
				$optimal_height	= $new_height;
				break;
			case 'landscape':
				$optimal_width	= $new_width;
				$optimal_height	= $this->get_size_by_fixed_width( $new_width );
				break;
			case 'auto':
				$option_array	= $this->get_size_by_auto( $new_width , $new_height );
				$optimal_width	= $option_array['optimal_width'];
				$optimal_height	= $option_array['optimal_height'];
				break;
			case 'fit':
				$option_array	= $this->get_size_by_fit( $new_width , $new_height );
				$optimal_width	= $option_array['optimal_width'];
				$optimal_height	= $option_array['optimal_height'];
				break;
			case 'crop':
				$option_array	= $this->get_optimal_crop( $new_width , $new_height );
				$optimal_width	= $option_array['optimal_width'];
				$optimal_height	= $option_array['optimal_height'];
				break;
		}
		
		return array(
			'optimal_width'		=> $optimal_width,
			'optimal_height'	=> $optimal_height
		);
	}
	
	/**
	 * Returns the width based on the image height.
	 * @param  int    $new_height The height of the image
	 * @return int
	 */
	private function get_size_by_fixed_height( $new_height )
	{
		$ratio		= $this->width / $this->height;
		$new_width	= $new_height * $ratio;
		
		return $new_width;
	}
	
	/**
	 * Returns the height based on the image width.
	 * @param  int    $new_width The width of the image
	 * @return int
	 */
	private function get_size_by_fixed_width( $new_width )
	{
		$ratio		= $this->height / $this->width;
		$new_height	= $new_width * $ratio;
		
		return $new_height;
	}
	
	/**
	 * Checks to see if an image is portrait or landscape and resizes accordingly.
	 * @param  int    $new_width  The width of the image
	 * @param  int    $new_height The height of the image
	 * @return array
	 */
	private function get_size_by_auto( $new_width , $new_height )
	{
		// Image to be resized is wider (landscape).
		if ( $this->height < $this->width )
		{
			$optimal_width	= $new_width;
			$optimal_height	= $this->get_size_by_fixed_width( $new_width );
		}
		// Image to be resized is taller (portrait).
		else if ( $this->height > $this->width )
		{
			$optimal_width	= $this->get_size_by_fixed_height( $new_height );
			$optimal_height	= $new_height;
		}
		// Image to be resizerd is a square.
		else
		{
			if ( $new_height < $new_width )
			{
				$optimal_width	= $new_width;
				$optimal_height	= $this->get_size_by_fixed_width( $new_width );
			}
			else if ( $new_height > $new_width )
			{
				$optimal_width	= $this->get_size_by_fixed_height( $new_height );
				$optimal_height	= $new_height;
			}
			else
			{
				// Sqaure being resized to a square.
				$optimal_width	= $new_width;
				$optimal_height	= $new_height;
			}
		}
		
		return array(
			'optimal_width'		=> $optimal_width,
			'optimal_height'	=> $optimal_height
		);
	}
	
	/**
	 * Resizes an image so it fits entirely inside the given dimensions.
	 * @param  int    $new_width  The width of the image
	 * @param  int    $new_height The height of the image
	 * @return array
	 */
	private function get_size_by_fit( $new_width , $new_height )
	{
		
		$height_ratio	= $this->height / $new_height;
		$width_ratio	= $this->width /  $new_width;
		
		$max = max( $height_ratio , $width_ratio );
		
		return array(
			'optimal_width'		=> $this->width / $max,
			'optimal_height'	=> $this->height / $max,
		);
	}
	
	/**
	 * Attempts to find the best way to crop. Whether crop is based on the
	 * image being portrait or landscape.
	 * @param  int    $new_width  The width of the image
	 * @param  int    $new_height The height of the image
	 * @return array
	 */
	private function get_optimal_crop( $new_width , $new_height )
	{
		$height_ratio	= $this->height / $new_height;
		$width_ratio	= $this->width /  $new_width;
		
		if ( $height_ratio < $width_ratio ) {
			$optimal_ratio = $height_ratio;
		} else {
			$optimal_ratio = $width_ratio;
		}
		
		$optimal_height	= $this->height / $optimal_ratio;
		$optimal_width	= $this->width  / $optimal_ratio;
		
		return array(
			'optimal_width'		=> $optimal_width,
			'optimal_height'	=> $optimal_height
		);
	}
	
	/**
	 * Crops an image from its center.
	 * @param  int    $optimal_width  The width of the image
	 * @param  int    $optimal_height The height of the image
	 * @param  int    $new_width      The new width
	 * @param  int    $new_height     The new height
	 * @return true
	 */
	private function crop( $optimal_width , $optimal_height , $new_width , $new_height )
	{
		$crop_points = $this->get_crop_points( $optimal_width , $optimal_height , $new_width , $new_height );
		
		// Find center - this will be used for the crop.
		$crop_start_x = $crop_points['x'];
		$crop_start_y = $crop_points['y'];
		
		$crop = $this->image_resized;
		
		$dest_offset_x	= max( 0, -$crop_start_x );
		$dest_offset_y	= max( 0, -$crop_start_y );
		$crop_start_x	= max( 0, $crop_start_x );
		$crop_start_y	= max( 0, $crop_start_y );
		$dest_width		= min( $optimal_width, $new_width );
		$dest_height	= min( $optimal_height, $new_height );
		
		// Now crop from center to exact requested size.
		$this->image_resized = imagecreatetruecolor( $new_width , $new_height );
		
		imagealphablending( $crop , true );
		imagealphablending( $this->image_resized , false );
		imagesavealpha( $this->image_resized , true );
		
		imagefilledrectangle( $this->image_resized , 0 , 0 , $new_width , $new_height,
			imagecolorallocatealpha( $this->image_resized , 255 , 255 , 255 , 127 )
		);
		
		imagecopyresampled( $this->image_resized , $crop , $dest_offset_x , $dest_offset_y , $crop_start_x , $crop_start_y , $dest_width , $dest_height , $dest_width , $dest_height );
		
		return true;
	}
	
	/**
	 * Gets the crop points based on the configuration either set in the file
	 * or overridden by user in their own config file, or on the fly.
	 * @param  int    $optimal_width  The width of the image
	 * @param  int    $optimal_height The height of the image
	 * @param  int    $new_width      The new width
	 * @param  int    $new_height     The new height
	 * @return array                  Array containing the crop x and y points.
	 */
	private function get_crop_points( $optimal_width , $optimal_height , $new_width , $new_height ) {
		$crop_points = array();
		
		// Where is our vertical starting crop point?
		switch ( Config::get('resizer::defaults.crop_vertical_start_point') ) {
			case 'top':
				$crop_points['y'] = 0;
				break;
			case 'center':
				$crop_points['y'] = ( $optimal_height / 2 ) - ( $new_height / 2 );
				break;
			case 'bottom':
				$crop_points['y'] = $optimal_height - $new_height;
				break;
			
			default:
				throw new Exception('Unknown value for crop_vertical_start_point: '. Config::get('resizer::defaults.crop_vertical_start_point') .'. Please check config file in the Resizer bundle.');
				break;
		}
		
		// Where is our horizontal starting crop point?
		switch ( Config::get('resizer::defaults.crop_horizontal_start_point') ) {
			case 'left':
				$crop_points['x'] = 0;
				break;
			case 'center':
				$crop_points['x'] = ( $optimal_width / 2 ) - ( $new_width / 2 );
				break;
			case 'right':
				$crop_points['x'] = $optimal_width - $new_width;
				break;
			
			default:
				throw new Exception('Unknown value for crop_horizontal_start_point: '. Config::get('resizer::defaults.crop_horizontal_start_point') .'. Please check config file in the Resizer bundle.');
				break;
		}
		
		return $crop_points;
	}
	
}