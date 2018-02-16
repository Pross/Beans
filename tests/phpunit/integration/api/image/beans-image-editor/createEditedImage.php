<?php
/**
 * Tests for get_image_info() method of _Beans_Image_Editor.
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use _Beans_Image_Editor;
use Beans\Framework\Tests\Integration\API\Image\Includes\Image_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-image-test-case.php';
require_once BEANS_API_PATH . 'image/class-beans-image-editor.php';

/**
 * Class Tests_Beans_Edit_Image_GetImageInfo
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 * @group   integration-tests
 * @group   api
 */
class Tests_Beans_Edit_Image_CreateEditedImage extends Image_Test_Case {

	/**
	 * Path of the fixtures directory.
	 *
	 * @var string
	 */
	protected static $fixtures_dir;

	/**
	 * Set up the test fixture before we start.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$fixtures_dir = realpath( __DIR__ . '/../fixtures' );
	}

	/**
	 * Test get_image_info() should edit the given image and then create a new "edited image", which is stored in the
	 * "rebuilt path".
	 */
	public function test_should_edit_create_and_store_image() {
		$get_image_info = $this->get_reflective_method( 'create_edited_image' );
		$rebuilt_path   = $this->get_reflective_property();
		$image_sources  = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args           = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$this->assertTrue( $get_image_info->invoke( $editor ) );
			$this->assertFileExists( $edited_image_src );

			list( $width, $height ) = @getimagesize( $edited_image_src ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->assertEquals( 800, $width );
			$this->assertEquals( 420, $height );
		}
	}

	/**
	 * Test get_image_info() should return false when the image does not exist.
	 */
	public function test_should_return_false_when_no_image() {
		$get_image_info = $this->get_reflective_method( 'create_edited_image' );
		$rebuilt_path   = $this->get_reflective_property();
		$src            = 'path/does/not/exist/image.jpg';

		$editor           = new _Beans_Image_Editor( $src, array( 'resize' => array( 800, false ) ) );
		$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertFalse( $get_image_info->invoke( $editor ) );
		$this->assertFileNotExists( $edited_image_src );
	}
}
