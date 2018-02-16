<?php
/**
 * Tests for run() method of _Beans_Image_Editor.
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use _Beans_Image_Editor;
use Beans\Framework\Tests\Integration\API\Image\Includes\Image_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-image-test-case.php';
require_once BEANS_API_PATH . 'image/class-beans-image-editor.php';

/**
 * Class Tests_Beans_Edit_Image_Run
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 * @group   integration-tests
 * @group   api
 */
class Tests_Beans_Edit_Image_Run extends Image_Test_Case {

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
	 * Test run() should edit the existing image, store it in the "rebuilt path", and then return it's URL.
	 */
	public function test_should_edit_store_and_return_its_url() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( beans_path_to_url( $edited_image_src ), $image_info );

			// Check the edited image's dimensions.
			list( $width, $height ) = @getimagesize( $edited_image_src ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->assertEquals( 800, $width );
			$this->assertEquals( 420, $height );
		}
	}

	/**
	 * Test init() should return original src when the image does not exist.
	 */
	public function test_should_return_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new _Beans_Image_Editor( $src, array( 'resize' => array( 800, false ) ) );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( $src, $editor->run() );
	}

	/**
	 * Test run() should return the URL when the edited image exists, meaning that it has already been edited and
	 * stored.
	 */
	public function test_should_return_url_when_edited_image_exists() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run it once to create the "edited image".
			$editor->run();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( beans_path_to_url( $edited_image_src ), $editor->run() );
		}
	}

	/**
	 * Test run() should edit the existing image, store it in the "rebuilt path", and then return an index array of its
	 * image info.
	 */
	public function test_should_edit_store_and_return_indexed_array() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, ARRAY_N );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( array( beans_path_to_url( $edited_image_src ), 800, 420 ), $image_info );

			// Check the edited image's dimensions.
			list( $width, $height ) = @getimagesize( $edited_image_src ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->assertEquals( 800, $width );
			$this->assertEquals( 420, $height );
		}
	}

	/**
	 * Test beans_edit_image() should return an indexed array with the original src when the image does not exist.
	 */
	public function test_should_return_indexed_array_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new _Beans_Image_Editor( $src, array( 'resize' => array( 800, false ) ), ARRAY_N );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( array( $src, null, null ), $editor->run() );
	}

	/**
	 * Test run() should return return an indexed array when the edited image exists, meaning that it has already been
	 * edited and stored.
	 */
	public function test_should_return_index_array_when_edited_image_exists() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, ARRAY_N );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run it once to create the "edited image".
			$editor->run();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( array( beans_path_to_url( $edited_image_src ), 800, 420 ), $editor->run() );
		}
	}

	/**
	 * Test run() should edit the existing image, store it in the "rebuilt path", and then return its image info as an
	 * object.
	 */
	public function test_should_edit_store_and_return_object() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 400, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, OBJECT );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertInstanceOf( 'stdClass', $image_info );
			$this->assertSame( beans_path_to_url( $edited_image_src ), $image_info->src );
			$this->assertSame( 400, $image_info->width );
			$this->assertSame( 210, $image_info->height );

			// Check the edited image's dimensions.
			list( $width, $height ) = @getimagesize( $edited_image_src ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->assertEquals( 400, $width );
			$this->assertEquals( 210, $height );
		}
	}

	/**
	 * Test beans_edit_image() should return an object with the original src when the image does not exist.
	 */
	public function test_should_return_object_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new _Beans_Image_Editor( $src, array( 'resize' => array( 800, false ) ), OBJECT );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$image_info = $editor->run();
		$this->assertInstanceOf( 'stdClass', $image_info );
		$this->assertSame( $src, $image_info->src );
		$this->assertNull( $image_info->width );
		$this->assertNull( $image_info->height );
	}

	/**
	 * Test run() should return return an object when the edited image exists, meaning that it has already been
	 * edited and stored.
	 */
	public function test_should_return_object_when_edited_image_exists() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 400, false ) );

		// Run the tests.
		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, OBJECT );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run it once to create the "edited image".
			$editor->run();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertInstanceOf( 'stdClass', $image_info );
			$this->assertSame( beans_path_to_url( $edited_image_src ), $image_info->src );
			$this->assertSame( 400, $image_info->width );
			$this->assertSame( 210, $image_info->height );
		}
	}

	/**
	 * Test run() should edit the existing image, store it in the "rebuilt path", and then return its image info as an
	 * associative array.
	 */
	public function test_should_edit_image_and_return_associative_array() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 600, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, ARRAY_A );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame(
				array(
					'src'    => beans_path_to_url( $edited_image_src ),
					'width'  => 600,
					'height' => 315,
				),
				$image_info
			);

			// Check the edited image's dimensions.
			list( $width, $height ) = @getimagesize( $edited_image_src ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->assertEquals( 600, $width );
			$this->assertEquals( 315, $height );
		}
	}

	/**
	 * Test beans_edit_image() should return an array with the original src when the image does not exist.
	 */
	public function test_should_return_associative_array_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new _Beans_Image_Editor( $src, array( 'resize' => array( 800, false ) ), ARRAY_A );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			array(
				'src'    => $src,
				'width'  => null,
				'height' => null,
			),
			$editor->run()
		);
	}

	/**
	 * Test run() should return return an associative array when the edited image exists, meaning that it has already
	 * been edited and stored.
	 */
	public function test_should_return_associatve_array_when_edited_image_exists() {
		$rebuilt_path  = $this->get_reflective_property();
		$image_sources = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args          = array( 'resize' => array( 600, false ) );

		// Run the tests.
		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args, ARRAY_A );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Run it once to create the "edited image".
			$editor->run();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame(
				array(
					'src'    => beans_path_to_url( $edited_image_src ),
					'width'  => 600,
					'height' => 315,
				),
				$editor->run()
			);
		}
	}
}
