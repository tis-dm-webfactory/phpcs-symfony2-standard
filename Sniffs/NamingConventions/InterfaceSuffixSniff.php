<?php
/**
 * This file is part of the Symfony2-coding-standard (phpcs standard)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Symfony2-phpcs-authors <Symfony2-coding-standard@opensky.github.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @version  GIT: master
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */

namespace PHP_CodeSniffer\Standards\Symfony2\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * InterfaceSuffixSniff.
 *
 * Throws errors if interface names are not suffixed with "Interface".
 *
 * Symfony coding standard specifies: "Suffix interfaces with Interface;"
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */
class InterfaceSuffixSniff implements Sniff {
	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
	);

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
	 * @param int                  $stackPtr  The position of the current token in
	 *                                        the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$line = $tokens[$stackPtr]['line'];

		while ($tokens[$stackPtr]['line'] == $line) {
			if ('T_STRING' == $tokens[$stackPtr]['type']) {
				if (substr($tokens[$stackPtr]['content'], -9) != 'Interface') {
					$phpcsFile->addError(
						'Interface name is not suffixed with "Interface"',
						$stackPtr
					);
				}
				break;
			}
			$stackPtr++;
		}

		return;
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_INTERFACE);
	}
}
