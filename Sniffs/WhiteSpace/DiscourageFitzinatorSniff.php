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

namespace PHP_CodeSniffer\Standards\Symfony2\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * DiscourageFitzinatorSniff.
 *
 * Throws warnings if a file contains trailing whitespace.
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Justin Hileman <justin@shopopensky.com>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     https://github.com/opensky/Symfony2-coding-standard
 */
class DiscourageFitzinatorSniff implements Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
		'JS',
		'CSS',
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

		// Make sure this is trailing whitespace.
		$line = $tokens[$stackPtr]['line'];
		if (($stackPtr < count($tokens) - 1) && $tokens[($stackPtr + 1)]['line'] === $line) {
			return;
		}

		if (strpos($tokens[$stackPtr]['content'], "\n") > 0 || strpos($tokens[$stackPtr]['content'], "\r") > 0) {
			$warning = 'Please trim any trailing whitespace';
			$phpcsFile->addWarning($warning, $stackPtr, 'Whitespace');
		}
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_WHITESPACE);
	}

}
