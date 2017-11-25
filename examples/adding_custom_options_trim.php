<?php

	require '../src/Philer.php';

	use xy2z\Philer\Philer;


	class MyPhiler extends Philer {

		protected static function get_default_options() : array {
			return array_merge(parent::get_default_options(), array(
				// Add your own options here.
				'trim_strings' => false
			));
		}

		protected function format_write_var($var) : string {
			// Check for your write options
			if ($this->options->trim_strings && gettype($var) == 'string') {
				$var = trim($var);
			}

			return parent::format_write_var($var);
		}
	}


	$file = new MyPhiler('myphiler.txt');
	$file->clear();

	// Overwrite value of your custom option
	$file->set_option('trim_strings', true);

	$file->write('  trim this string please   ');
	echo $file->read();
