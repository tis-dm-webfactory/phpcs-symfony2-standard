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

namespace PHP_CodeSniffer\Standards\Symfony2\Sniffs\Commenting;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FunctionCommentSniff as BaseFunctionCommentSniff;

if (class_exists('PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FunctionCommentSniff', true) === false) {
	$error = 'Class PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FunctionCommentSniff not found';
	throw new RuntimeException($error);
}

/**
 * Symfony2 standard customization to PEARs FunctionCommentSniff.
 *
 * Verifies that :
 * <ul>
 *  <li>There is a &#64;return tag if a return statement exists inside the method</li>
 * </ul>
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Felix Brandt <mail@felixbrandt.de>
 * @license  http://spdx.org/licenses/BSD-3-Clause BSD 3-clause "New" or "Revised" License
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class FunctionCommentSniff extends BaseFunctionCommentSniff {

	/**
	 * @var string
	 */
	public $requiredScopes = 'public';

	/**
	 * {@inheritdoc}
	 */
	public function process(File $phpcsFile, $stackPtr) {
		if (false === $commentEnd = $phpcsFile->findPrevious(array(T_COMMENT, T_DOC_COMMENT, T_CLASS, T_FUNCTION, T_OPEN_TAG), ($stackPtr - 1))) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$code = $tokens[$commentEnd]['code'];

		// a comment is not required on protected/private methods
		$method = $phpcsFile->getMethodProperties($stackPtr);
		$commentRequired = $this->isRequiredScope($method['scope']);

		if (($code === T_COMMENT && !$commentRequired)
			|| ($code !== T_DOC_COMMENT && !$commentRequired)
		) {
			return;
		}

		parent::process($phpcsFile, $stackPtr);
	}

	/**
	 * Checks if the comment an inheritdoc?
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int                  $stackPtr
	 *
	 * @return bool
	 */
	protected function isInheritDoc(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$start = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr - 1);
		$end = $phpcsFile->findNext(T_DOC_COMMENT_CLOSE_TAG, $start);

		$content = $phpcsFile->getTokensAsString($start, ($end - $start));

		return preg_match('#{@inheritdoc}#i', $content) === 1;
	} /* end processReturn() */

	/**
	 * Is the return statement matching?
	 *
	 * @param array $tokens    Array of tokens
	 * @param int   $returnPos Stack position of the T_RETURN token to process
	 *
	 * @return boolean True if the return does not return anything
	 */
	protected function isMatchingReturn($tokens, $returnPos) {
		do {
			$returnPos++;
		} while ($tokens[$returnPos]['code'] === T_WHITESPACE);

		return $tokens[$returnPos]['code'] !== T_SEMICOLON;
	} // end isInheritDoc()

	/**
	 * Check if the given function visibility scope needs to have a docblock.
	 *
	 * @param string $scope Visibility scope of the function.
	 *
	 * @return bool
	 */
	protected function isRequiredScope($scope) {
		return strpos($this->requiredScopes, $scope) !== false;
	} // end processParams()

	/**
	 * {@inheritdoc}
	 */
	protected function processParams(File $phpcsFile, $stackPtr, $commentStart) {
		if ($this->isInheritDoc($phpcsFile, $stackPtr)) {
			return;
		}

		parent::processParams($phpcsFile, $stackPtr, $commentStart);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function processReturn(File $phpcsFile, $stackPtr, $commentStart) {
		if ($this->isInheritDoc($phpcsFile, $stackPtr)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$funcPtr = $phpcsFile->findNext(T_FUNCTION, $commentStart);

		// Only check for a return comment if a non-void return statement exists
		if (isset($tokens[$stackPtr]['scope_opener'])) {
			$start = $tokens[$stackPtr]['scope_opener'];

			// iterate over all return statements of this function,
			// run the check on the first which is not only 'return;'
			while ($returnToken = $phpcsFile->findNext(T_RETURN, $start, $tokens[$stackPtr]['scope_closer'])) {
				if ($this->isMatchingReturn($tokens, $returnToken)) {
					parent::processReturn($phpcsFile, $stackPtr, $commentStart);
					break;
				}
				$start = $returnToken + 1;
			}
		}
	}

}//end class
