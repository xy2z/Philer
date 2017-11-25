<?php

	require '../src/Philer.php';

	use xy2z\Philer\Philer;


	class MyPhiler extends Philer {

		protected static function get_default_options() : array {
			return array_merge(parent::get_default_options(), array(
				// Add your own options here.
				'clear_on_load' => true
			));
		}

		public function load(string $path) {
			parent::load($path);
			if ($this->options->clear_on_load) {
				$this->clear();
			}
		}
	}


	$file = new MyPhiler('myphiler.txt');
	$file->write('hello world');

	$file->load('myphiler.txt');
	$file->write('Content is cleared');
	echo $file->read();
