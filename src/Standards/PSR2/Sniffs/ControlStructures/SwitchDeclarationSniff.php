<?php
/**
 * Ensures all switch statements are defined correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class SwitchDeclarationSniff implements Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [T_SWITCH];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // We can't process SWITCH statements unless we know where they start and end.
        if (isset($tokens[$stackPtr]['scope_opener']) === false
            || isset($tokens[$stackPtr]['scope_closer']) === false
        ) {
            return;
        }

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + $this->indent);

        while (($nextCase = $this->findNextCase($phpcsFile, ($nextCase + 1), $switch['scope_closer'])) !== false) {
            if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                $type = 'default';
            } else {
                $type = 'case';
            }

            if ($tokens[$nextCase]['content'] !== strtolower($tokens[$nextCase]['content'])) {
                $expected = strtolower($tokens[$nextCase]['content']);
                $error    = strtoupper($type).' keyword must be lowercase; expected "%s" but found "%s"';
                $data     = [
                    $expected,
                    $tokens[$nextCase]['content'],
                ];

                $fix = $phpcsFile->addFixableError($error, $nextCase, $type.'NotLower', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($nextCase, $expected);
                }
            }

            if ($type === 'case'
                && ($tokens[($nextCase + 1)]['code'] !== T_WHITESPACE
                || $tokens[($nextCase + 1)]['content'] !== ' ')
            ) {
                $error = 'CASE keyword must be followed by a single space';
                $fix   = $phpcsFile->addFixableError($error, $nextCase, 'SpacingAfterCase');
                if ($fix === true) {
                    if ($tokens[($nextCase + 1)]['code'] !== T_WHITESPACE) {
                        $phpcsFile->fixer->addContent($nextCase, ' ');
                    } else {
                        $phpcsFile->fixer->replaceToken(($nextCase + 1), ' ');
                    }
                }
            }

            $opener     = $tokens[$nextCase]['scope_opener'];
            $nextCloser = $tokens[$nextCase]['scope_closer'];
            if ($tokens[$opener]['code'] === T_COLON) {
                if ($tokens[($opener - 1)]['code'] === T_WHITESPACE) {
                    $error = 'There must be no space before the colon in a '.strtoupper($type).' statement';
                    $fix   = $phpcsFile->addFixableError($error, $nextCase, 'SpaceBeforeColon'.strtoupper($type));
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($opener - 1), '');
                    }
                }

                for ($next = ($opener + 1); $next < $nextCloser; $next++) {
                    if (isset(Tokens::$emptyTokens[$tokens[$next]['code']]) === false
                        || (isset(Tokens::$commentTokens[$tokens[$next]['code']]) === true
                        && $tokens[$next]['line'] !== $tokens[$opener]['line'])
                    ) {
                        break;
                    }
                }

                if ($tokens[$next]['line'] !== ($tokens[$opener]['line'] + 1)) {
                    $error = 'The '.strtoupper($type).' body must start on the line following the statement';
                    $fix   = $phpcsFile->addFixableError($error, $nextCase, 'BodyOnNextLine'.strtoupper($type));
                    if ($fix === true) {
                        if ($tokens[$next]['line'] === $tokens[$opener]['line']) {
                            $padding = str_repeat(' ', ($caseAlignment + $this->indent - 1));
                            $phpcsFile->fixer->addContentBefore($next, $phpcsFile->eolChar.$padding);
                        } else {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = ($opener + 1); $i < $next; $i++) {
                                if ($tokens[$i]['line'] === $tokens[$opener]['line']) {
                                    // Ignore trailing comments.
                                    continue;
                                }

                                if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                                    break;
                                }

                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }//end if
                }//end if

                if ($tokens[$nextCloser]['scope_condition'] === $nextCase) {
                    // Only need to check some things once, even if the
                    // closer is shared between multiple case statements, or even
                    // the default case.
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCloser - 1), $nextCase, true);
                    if ($tokens[$prev]['line'] === $tokens[$nextCloser]['line']) {
                        $error = 'Terminating statement must be on a line by itself';
                        $fix   = $phpcsFile->addFixableError($error, $nextCloser, 'BreakNotNewLine');
                        if ($fix === true) {
                            $phpcsFile->fixer->addNewline($prev);
                            $phpcsFile->fixer->replaceToken($nextCloser, trim($tokens[$nextCloser]['content']));
                        }
                    } else {
                        $diff = ($tokens[$nextCase]['column'] + $this->indent - $tokens[$nextCloser]['column']);
                        if ($diff !== 0) {
                            $error = 'Terminating statement must be indented to the same level as the CASE body';
                            $fix   = $phpcsFile->addFixableError($error, $nextCloser, 'BreakIndent');
                            if ($fix === true) {
                                if ($diff > 0) {
                                    $phpcsFile->fixer->addContentBefore($nextCloser, str_repeat(' ', $diff));
                                } else {
                                    $phpcsFile->fixer->substrToken(($nextCloser - 1), 0, $diff);
                                }
                            }
                        }
                    }//end if
                }//end if
            } else {
                $error = strtoupper($type).' statements must be defined using a colon';
                if ($tokens[$opener]['code'] === T_SEMICOLON) {
                    $fix = $phpcsFile->addFixableError($error, $nextCase, 'WrongOpener'.$type);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($opener, ':');
                    }
                } else {
                    // Probably a case/default statement with colon + curly braces.
                    $phpcsFile->addError($error, $nextCase, 'WrongOpener'.$type);
                }
            }//end if

            // We only want cases from here on in.
            if ($type !== 'case') {
                continue;
            }

            $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($opener + 1), $nextCloser, true);

            if ($tokens[$nextCode]['code'] !== T_CASE && $tokens[$nextCode]['code'] !== T_DEFAULT) {
                // This case statement has content. If the next case or default comes
                // before the closer, it means we don't have an obvious terminating
                // statement and need to make some more effort to find one. If we
                // don't, we do need a comment.
                $nextCode = $this->findNextCase($phpcsFile, ($opener + 1), $nextCloser);
                if ($nextCode !== false) {
                    $prevCode = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCode - 1), $nextCase, true);
                    if (isset(Tokens::$commentTokens[$tokens[$prevCode]['code']]) === false
                        && $this->findNestedTerminator($phpcsFile, ($opener + 1), $nextCode) === false
                    ) {
                        $error = 'There must be a comment when fall-through is intentional in a non-empty case body';
                        $phpcsFile->addError($error, $nextCase, 'TerminatingComment');
                    }
                }
            }
        }//end while

    }//end process()


    /**
     * Find the next CASE or DEFAULT statement from a point in the file.
     *
     * Note that nested switches are ignored.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position to start looking at.
     * @param int                         $end       The position to stop looking at.
     *
     * @return int|false
     */
    private function findNextCase($phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();
        while (($stackPtr = $phpcsFile->findNext([T_CASE, T_DEFAULT, T_SWITCH], $stackPtr, $end)) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$stackPtr]['code'] === T_SWITCH) {
                $stackPtr = $tokens[$stackPtr]['scope_closer'];
                continue;
            }

            break;
        }

        return $stackPtr;

    }//end findNextCase()


    /**
     * Returns the position of the nested terminating statement.
     *
     * Returns false if no terminating statement was found.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position to start looking at.
     * @param int                         $end       The position to stop looking at.
     *
     * @return int|bool
     */
    private function findNestedTerminator($phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();

        $lastToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($end - 1), $stackPtr, true);
        if ($lastToken === false) {
            return false;
        }

        if ($tokens[$lastToken]['code'] === T_CLOSE_CURLY_BRACKET) {
            // We found a closing curly bracket and want to check if its block
            // belongs to a SWITCH, IF, ELSEIF or ELSE, TRY, CATCH OR FINALLY clause.
            // If yes, we continue searching for a terminating statement within that
            // block. Note that we have to make sure that every block of
            // the entire if/else/switch statement has a terminating statement.
            // For a try/catch/finally statement, either the finally block has
            // to have a terminating statement or every try/catch block has to have one.
            $currentCloser = $lastToken;
            $hasElseBlock  = false;
            $hasCatchWithoutTerminator = false;
            do {
                $scopeOpener = $tokens[$currentCloser]['scope_opener'];
                $scopeCloser = $tokens[$currentCloser]['scope_closer'];

                $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($scopeOpener - 1), $stackPtr, true);
                if ($prevToken === false) {
                    return false;
                }

                // SWITCH, IF, ELSEIF, CATCH clauses possess a condition we have to account for.
                if ($tokens[$prevToken]['code'] === T_CLOSE_PARENTHESIS) {
                    $prevToken = $tokens[$prevToken]['parenthesis_owner'];
                }

                if ($tokens[$prevToken]['code'] === T_IF) {
                    // If we have not encountered an ELSE clause by now, we cannot
                    // be sure that the whole statement terminates in every case.
                    if ($hasElseBlock === false) {
                        return false;
                    }

                    return $this->findNestedTerminator($phpcsFile, ($scopeOpener + 1), $scopeCloser);
                } else if ($tokens[$prevToken]['code'] === T_ELSEIF
                    || $tokens[$prevToken]['code'] === T_ELSE
                ) {
                    // If we find a terminating statement within this block,
                    // we continue with the previous ELSEIF or IF clause.
                    $hasTerminator = $this->findNestedTerminator($phpcsFile, ($scopeOpener + 1), $scopeCloser);
                    if ($hasTerminator === false) {
                        return false;
                    }

                    $currentCloser = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevToken - 1), $stackPtr, true);
                    if ($tokens[$prevToken]['code'] === T_ELSE) {
                        $hasElseBlock = true;
                    }
                } else if ($tokens[$prevToken]['code'] === T_FINALLY) {
                    // If we find a terminating statement within this block,
                    // the whole try/catch/finally statement is covered.
                    $hasTerminator = $this->findNestedTerminator($phpcsFile, ($scopeOpener + 1), $scopeCloser);
                    if ($hasTerminator !== false) {
                        return $hasTerminator;
                    }

                    // Otherwise, we continue with the previous TRY or CATCH clause.
                    $currentCloser = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevToken - 1), $stackPtr, true);
                } else if ($tokens[$prevToken]['code'] === T_TRY) {
                    // If we've seen CATCH blocks without terminator statement and
                    // have not seen a FINALLY *with* a terminator statement, we
                    // don't even need to bother checking the TRY.
                    if ($hasCatchWithoutTerminator === true) {
                        return false;
                    }

                    return $this->findNestedTerminator($phpcsFile, ($scopeOpener + 1), $scopeCloser);
                } else if ($tokens[$prevToken]['code'] === T_CATCH) {
                    // Keep track of seen catch statements without terminating statement,
                    // but don't bow out yet as there may still be a FINALLY clause
                    // with a terminating statement before the CATCH.
                    $hasTerminator = $this->findNestedTerminator($phpcsFile, ($scopeOpener + 1), $scopeCloser);
                    if ($hasTerminator === false) {
                        $hasCatchWithoutTerminator = true;
                    }

                    $currentCloser = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($prevToken - 1), $stackPtr, true);
                } else if ($tokens[$prevToken]['code'] === T_SWITCH) {
                    $hasDefaultBlock = false;
                    $endOfSwitch     = $tokens[$prevToken]['scope_closer'];
                    $nextCase        = $prevToken;

                    // We look for a terminating statement within every blocks.
                    while (($nextCase = $this->findNextCase($phpcsFile, ($nextCase + 1), $endOfSwitch)) !== false) {
                        if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                            $hasDefaultBlock = true;
                        }

                        $opener = $tokens[$nextCase]['scope_opener'];

                        $nextCode = $phpcsFile->findNext(Tokens::$emptyTokens, ($opener + 1), $endOfSwitch, true);
                        if ($tokens[$nextCode]['code'] === T_CASE || $tokens[$nextCode]['code'] === T_DEFAULT) {
                            // This case statement has no content, so skip it.
                            continue;
                        }

                        $endOfCase = $this->findNextCase($phpcsFile, ($opener + 1), $endOfSwitch);
                        if ($endOfCase === false) {
                            $endOfCase = $endOfSwitch;
                        }

                        $hasTerminator = $this->findNestedTerminator($phpcsFile, ($opener + 1), $endOfCase);
                        if ($hasTerminator === false) {
                            return false;
                        }
                    }//end while

                    // If we have not encountered a DEFAULT block by now, we cannot
                    // be sure that the whole statement terminates in every case.
                    if ($hasDefaultBlock === false) {
                        return false;
                    }

                    return $hasTerminator;
                } else {
                    return false;
                }//end if
            } while ($currentCloser !== false && $tokens[$currentCloser]['code'] === T_CLOSE_CURLY_BRACKET);

            return true;
        } else if ($tokens[$lastToken]['code'] === T_SEMICOLON) {
            // We found the last statement of the CASE. Now we want to
            // check whether it is a terminating one.
            $terminators = [
                T_RETURN   => T_RETURN,
                T_BREAK    => T_BREAK,
                T_CONTINUE => T_CONTINUE,
                T_THROW    => T_THROW,
                T_EXIT     => T_EXIT,
                T_GOTO     => T_GOTO,
            ];

            $terminator = $phpcsFile->findStartOfStatement(($lastToken - 1));
            if (isset($terminators[$tokens[$terminator]['code']]) === true) {
                return $terminator;
            }
        }//end if

        return false;

    }//end findNestedTerminator()


}//end class
