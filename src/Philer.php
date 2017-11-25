<?php

	namespace xy2z\Philer;


	/**
	 * Philer class
	 *
	 * @link https://packagist.org/packages/xy2z/philer
	 */
	class Philer {

		protected $path;

		protected $handle;

		protected $options = array();

		/**
		 * Constructor
		 *
		 * @param string $path    Path of the file to open/create.
		 * @param array  $options Philer options, see self::get_default_options().
		 */
		public function __construct(string $path, array $options = array()) {
			$this->options = (object) $this->get_default_options();

			// Overwrite default options
			foreach ($options as $key => $value) {
				$this->set_option($key, $value);
			}

			$this->load($path);
		}

		/**
		 * Get default options
		 *
		 */
		protected static function get_default_options() : array {
			return array(
				'prepend_timestamp' => false,
				'var_dump' => false,
				'include_trace' => false,
				'write_prepend' => '',
				'write_append' => PHP_EOL,
			);
		}

		/**
		 * Change an option
		 *
		 * @param string $option The option name.
		 * @param mixed $value   The new value.
		 */
		public function set_option(string $option, $value) {
			if (!isset($this->options->$option)) {
				throw new \Exception('Unknown Philer option: "' . $option . '"');
			}

			$this->options->$option = $value;
		}

		/**
		 * Get option value
		 *
		 * @param string $option Option label
		 *
		 * @return mixed Option value
		 */
		public function get_option(string $option) {
			if (!isset($this->options->$option)) {
				throw new \Exception('Unknown Philer option: "' . $option . '"');
			}

			return $this->options->$option;
		}

		/**
		 * Init the file stream
		 *
		 */
		protected function init() {
			if (empty($this->handle) || !is_resource($this->handle)) {
				$this->handle = fopen($this->path, 'a+');
			}
		}

		/**
		 * Load a new file
		 *
		 * @param  string $path Path to file
		 */
		public function load(string $path) {
			$this->path = $path;
			$this->close();
			unset($this->handle);
			$this->init();
		}

		/**
		 * Write to file
		 *
		 */
		public function write() {
			$this->init();
			foreach (func_get_args() as $key => $arg) {
				fwrite($this->handle, $this->format_write_var($arg));
			}
		}

		/**
		 * Format the write variable before writing to file.
		 *
		 * @param mixed $var Can be any type
		 *
		 * @return string The formatted variable.
		 */
		protected function format_write_var($var) : string {
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
				$output .= print_r($var, true);
			}

			if (!empty($this->options->write_prepend)) {
				$output = $this->options->write_prepend . $output;
			}

			if (!empty($this->options->write_append)) {
				if ($this->options->var_dump && $this->options->write_append === PHP_EOL) {
					// Don't add PHP_EOL since var_dump() already adds it.
				} else {
					$output .= $this->options->write_append;
				}
			}

			if ($this->options->include_trace) {
				// Add extra linebreak.
				$output .= PHP_EOL;
			}

			return $output;
		}

		/**
		 * Empty the file.
		 *
		 */
		public function clear() {
			file_put_contents($this->path, '');
		}

		/**
		 * Delete the file
		 *
		 */
		public function delete() {
			$this->close();
			return unlink($this->path);
		}

		/**
		 * Read the file content
		 *
		 * @return string        File content
		 */
		public function read() {
			return file_get_contents($this->path);
		}

		/**
		 * Close the handle
		 *
		 */
		protected function close() {
			if (is_resource($this->handle)) {
				fclose($this->handle);
			}
		}

		/**
		 * Destructor
		 *
		 */
		public function __destruct() {
			$this->close();
		}
	}
