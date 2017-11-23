<?php

	namespace xy2z\Tools;

	class Philer {

		private $path;

		private $handle;

		private $options;

		/**
		 * Constructor
		 *
		 * @param string $path    Path of the file to open/create.
		 * @param array  $options Philer options, see self::get_default_options().
		 */
		public function __construct(string $path, array $options = array()) {
			$this->options = (object) array_merge(self::get_default_options(), $options);
			$this->load($path);
		}


		/**
		 * Get default options
		 */
		private static function get_default_options() {
			return array(
				'prepend_timestamp' => false,
				'include_trace' => false,
				'var_dump' => false,
			);
		}

		public function set_option(string $key, $value) {
			if (!isset($this->options->$key)) {
				throw new Exception('Unknown Philer option');
			}

			$this->options->$key = $value;
			var_dump($this->options);
		}


		/**
		 * Init the file stream
		 */
		private function init() {
			if (empty($this->handle) || !is_resource($this->handle)) {
				$this->handle = fopen($this->path, 'a+');
			}
		}

		/**
		 * Load a new file
		 * @param  string $path Path to file
		 */
		public function load(string $path) {
			$this->path = $path;
			unset($this->handle);
			$this->init();
		}


		/**
		 * Write to file
		 */
		public function write() {
			$this->init();
			foreach (func_get_args() as $key => $arg) {
				$this->write_var($arg);
			}
		}


		/**
		 * Write single variable
		 * @param  mixed $var String, int, array, object, etc.
		 */
		private function write_var($var) {
			$output = '';

			if ($this->options->prepend_timestamp) {
				$output = '[' . date('Y-m-d H:i:s') . '] ' . $output;
			}

			if ($this->options->include_trace) {
				$r = debug_backtrace();
				$output .= $r[1]['file'] . ':' . $r[1]['line'] . PHP_EOL;
			}

			if ($this->options->var_dump) {
				ob_start();
				var_dump($var);
				$output .= ob_get_clean();
			} else {
				$output .= print_r($var, true) . PHP_EOL;
			}

			if ($this->options->include_trace) {
				// Add extra linebreak.
				$output .= PHP_EOL;
			}

			fwrite($this->handle, $output);
		}


		/**
		 * Clear/empty the file.
		 */
		public function clear() {
			file_put_contents($this->path, '');
		}


		/**
		 * Delete the file
		 */
		public function delete() {
			$this->close();
			return unlink($this->path);
		}


		/**
		 * Read the file
		 * @param  integer $from [description]
		 * @param  integer $to   [description]
		 * @return string        File content
		 */
		public function read($from = 0, $to = -1) {
			return file_get_contents($this->path);
			// fseek($this->handle, $from);
			// clearstatcache();
			// return fread($this->handle, filesize($this->path));
		}


		private function close() {
			if (is_resource($this->handle)) {
				fclose($this->handle);
			}
		}


		public function __destruct() {
			$this->close();
		}


	}
