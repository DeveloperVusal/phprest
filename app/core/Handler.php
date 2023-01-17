<?php
namespace App\Core;

class Handler {
	/**
	 * Метод генерации рандомных символов
	 * 
	 * @param integer $length
	 * @param string $type
	 * @access private
	 * @return integer|string
	 */
	public function generate_code($length = 6, $type = 'numeric')
	{
		$build = '';

		switch ($type) {
			case 'numeric':
				for ($i = 0; $i < $length; $i++) { 
					$build .= mt_rand(0, 9);
				}
				
				break;
			
			case 'chars':
				$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				$letters_arr = str_split($letters);
				$gen_length = sizeof($letters_arr) - 1;

				for ($i = 0; $i < $length; $i++) { 
					$build .= $letters_arr[mt_rand(0, $gen_length)];
				}
				
				break;
		}

		return $build;
	}
}